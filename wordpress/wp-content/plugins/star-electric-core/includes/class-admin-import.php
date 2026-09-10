<?php
/**
 * The admin-side import runner.
 *
 * WP-CLI is the comfortable way to run a 4,348-record import, but it needs a
 * shell. This screen does the same work through the dashboard: each step is
 * driven by a sequence of small AJAX requests, every one of which finishes well
 * inside a normal PHP execution limit and reports where the next one starts.
 *
 * The consequence is that no single request has to be long-running, so the
 * import survives the constraints of shared hosting: a 30-second time limit, a
 * modest memory limit, and a browser tab that might be closed halfway through.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Import screen and AJAX endpoints.
 */
class Star_Electric_Admin_Import {

	/** Menu slug. */
	public const SLUG = 'star-electric-import';

	/** Nonce action. */
	private const NONCE = 'star_electric_import';

	/** The steps, in the order they must run. */
	public const STEPS = array(
		'media'      => array(
			'label' => 'Media library',
			'note'  => 'Each unique image master, imported once and reused.',
			'batch' => 15,
		),
		'taxonomies' => array(
			'label' => 'Categories, brands, departments',
			'note'  => 'Runs in a single request.',
			'batch' => 0,
		),
		'ranges'     => array(
			'label' => 'Ranges',
			'note'  => 'Family records, kept out of the product catalogue.',
			'batch' => 25,
		),
		'products'   => array(
			'label' => 'Products',
			'note'  => 'Requires WooCommerce. Quote-only items import with no price.',
			'batch' => 20,
		),
	);

	/**
	 * Hook the screen up.
	 */
	public static function init(): void {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'wp_ajax_star_electric_import', array( __CLASS__, 'ajax' ) );
		add_action( 'wp_ajax_star_electric_import_audit', array( __CLASS__, 'ajax_audit' ) );
		add_action( 'wp_ajax_star_electric_import_reset', array( __CLASS__, 'ajax_reset' ) );
		add_action( 'wp_ajax_star_electric_import_recount', array( __CLASS__, 'ajax_recount' ) );
	}

	/**
	 * Register the menu entry.
	 */
	public static function menu(): void {
		add_menu_page(
			__( 'Star Electric Import', 'star-electric' ),
			__( 'Star Electric', 'star-electric' ),
			'manage_options',
			self::SLUG,
			array( __CLASS__, 'render' ),
			'dashicons-database-import',
			58
		);
	}

	/**
	 * Report the host limits that decide whether this route can work at all.
	 */
	public static function environment(): array {
		$upload = wp_upload_dir();

		return array(
			'PHP version'            => PHP_VERSION,
			'max_execution_time'     => (string) ini_get( 'max_execution_time' ) . 's',
			'memory_limit'           => (string) ini_get( 'memory_limit' ),
			'upload_max_filesize'    => (string) ini_get( 'upload_max_filesize' ),
			'post_max_size'          => (string) ini_get( 'post_max_size' ),
			'Uploads directory'      => empty( $upload['error'] ) && wp_is_writable( $upload['basedir'] )
				? 'writable'
				: 'NOT writable - ' . (string) $upload['error'],
			'Outbound HTTP requests' => self::can_reach_source() ? 'working' : 'BLOCKED',
			'WooCommerce'            => class_exists( 'WooCommerce' ) ? 'active' : 'NOT active',
		);
	}

	/**
	 * Check that the images can actually be fetched before 2,054 attempts fail.
	 */
	private static function can_reach_source(): bool {
		$response = wp_remote_head(
			Star_Electric_Importer::source_base() . '/index.html',
			array( 'timeout' => 10 )
		);
		return ! is_wp_error( $response ) && 200 === (int) wp_remote_retrieve_response_code( $response );
	}

	/**
	 * Render the screen.
	 */
	public static function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to run the import.', 'star-electric' ) );
		}

		self::save_settings();

		$payload  = Star_Electric_Importer::payload_status();
		$progress = Star_Electric_Importer::progress();
		$missing  = array_filter(
			$payload,
			static function ( $f ) {
				return ! $f['exists'];
			}
		);

		wp_enqueue_style(
			'star-electric-admin-import',
			STAR_ELECTRIC_URL . 'assets/admin-import.css',
			array(),
			(string) filemtime( STAR_ELECTRIC_PATH . 'assets/admin-import.css' )
		);
		wp_enqueue_script(
			'star-electric-admin-import',
			STAR_ELECTRIC_URL . 'assets/admin-import.js',
			array(),
			(string) filemtime( STAR_ELECTRIC_PATH . 'assets/admin-import.js' ),
			true
		);
		wp_localize_script(
			'star-electric-admin-import',
			'STAR_ELECTRIC_IMPORT',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( self::NONCE ),
				'steps'    => self::STEPS,
				'progress' => $progress,
			)
		);

		?>
		<div class="wrap star-import">
			<h1><?php esc_html_e( 'Star Electric catalogue import', 'star-electric' ); ?></h1>
			<p class="star-import__lead">
				<?php esc_html_e( 'Runs the migration in small batches through the dashboard. Every step is resumable and safe to run twice: records are matched on their source id, so a second run updates rather than duplicates.', 'star-electric' ); ?>
			</p>

			<h2><?php esc_html_e( 'Environment', 'star-electric' ); ?></h2>
			<table class="widefat striped star-import__env">
				<tbody>
				<?php foreach ( self::environment() as $key => $value ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $key ); ?></th>
						<td>
							<?php
							$bad = ( false !== stripos( $value, 'NOT' ) || false !== stripos( $value, 'BLOCKED' ) );
							printf(
								'<code class="%s">%s</code>',
								$bad ? 'star-import__bad' : 'star-import__ok',
								esc_html( $value )
							);
							?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<h2><?php esc_html_e( 'Payload', 'star-electric' ); ?></h2>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'File', 'star-electric' ); ?></th>
						<th><?php esc_html_e( 'Status', 'star-electric' ); ?></th>
						<th><?php esc_html_e( 'Size', 'star-electric' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ( $payload as $name => $file ) : ?>
					<tr>
						<td><code><?php echo esc_html( $name ); ?></code></td>
						<td>
							<?php if ( $file['exists'] ) : ?>
								<span class="star-import__ok"><?php esc_html_e( 'present', 'star-electric' ); ?></span>
							<?php else : ?>
								<span class="star-import__bad"><?php esc_html_e( 'missing', 'star-electric' ); ?></span>
							<?php endif; ?>
						</td>
						<td><?php echo esc_html( $file['exists'] ? size_format( $file['bytes'] ) : '-' ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<?php if ( $missing ) : ?>
				<div class="notice notice-error inline">
					<p><?php esc_html_e( 'The payload is incomplete. Re-upload the plugin ZIP - the data files ship inside it.', 'star-electric' ); ?></p>
				</div>
			<?php endif; ?>

			<form method="post" class="star-import__settings">
				<?php wp_nonce_field( self::NONCE ); ?>
				<h2><?php esc_html_e( 'Image source', 'star-electric' ); ?></h2>
				<p class="description">
					<?php esc_html_e( 'Images are copied into this site’s own media library from here. Nothing is hotlinked.', 'star-electric' ); ?>
				</p>
				<input type="url" name="star_electric_source_base" class="regular-text code"
					value="<?php echo esc_attr( Star_Electric_Importer::source_base() ); ?>" />

				<h2><?php esc_html_e( 'Enquiry recipient', 'star-electric' ); ?></h2>
				<p class="description">
					<?php esc_html_e( 'Quote requests, contact messages and complaints are emailed here. Every submission is also stored under Enquiries, so nothing is lost if mail fails.', 'star-electric' ); ?>
				</p>
				<input type="email" name="star_electric_form_email" class="regular-text code"
					value="<?php echo esc_attr( Star_Electric_Forms::recipient() ); ?>" />

				<p><button type="submit" class="button button-primary"><?php esc_html_e( 'Save settings', 'star-electric' ); ?></button></p>
			</form>

			<h2><?php esc_html_e( 'Steps', 'star-electric' ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'Run them in order. Media first, so products and ranges can attach the images that are already in the library.', 'star-electric' ); ?>
			</p>

			<div id="star-import-steps">
			<?php foreach ( self::STEPS as $key => $step ) : ?>
				<div class="star-step" data-step="<?php echo esc_attr( $key ); ?>">
					<div class="star-step__head">
						<h3><?php echo esc_html( $step['label'] ); ?></h3>
						<div class="star-step__buttons">
							<button type="button" class="button button-primary star-step__run">
								<?php esc_html_e( 'Run', 'star-electric' ); ?>
							</button>
							<button type="button" class="button star-step__stop" disabled>
								<?php esc_html_e( 'Stop', 'star-electric' ); ?>
							</button>
							<button type="button" class="button-link star-step__reset">
								<?php esc_html_e( 'Reset progress', 'star-electric' ); ?>
							</button>
						</div>
					</div>
					<p class="description"><?php echo esc_html( $step['note'] ); ?></p>
					<div class="star-step__bar"><span style="width:0%"></span></div>
					<p class="star-step__status">
						<?php
						$p = $progress[ $key ] ?? array();
						if ( $p ) {
							printf(
								/* translators: 1: processed, 2: total, 3: created, 4: updated, 5: failed */
								esc_html__( '%1$d of %2$d - %3$d created, %4$d updated, %5$d failed', 'star-electric' ),
								(int) ( $p['offset'] ?? 0 ),
								(int) ( $p['total'] ?? 0 ),
								(int) ( $p['created'] ?? 0 ),
								(int) ( $p['updated'] ?? 0 ) + (int) ( $p['reused'] ?? 0 ),
								(int) ( $p['failed'] ?? 0 )
							);
						} else {
							esc_html_e( 'Not started', 'star-electric' );
						}
						?>
					</p>
					<details class="star-step__errors" <?php echo empty( $progress[ $key ]['errors'] ) ? 'hidden' : ''; ?>>
						<summary><?php esc_html_e( 'Failures', 'star-electric' ); ?></summary>
						<ul>
						<?php foreach ( (array) ( $progress[ $key ]['errors'] ?? array() ) as $error ) : ?>
							<li><?php echo esc_html( $error ); ?></li>
						<?php endforeach; ?>
						</ul>
					</details>
				</div>
			<?php endforeach; ?>
			</div>

			<h2><?php esc_html_e( 'Product page layouts', 'star-electric' ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'Every product owns its own Elementor layout, so editing one product’s page cannot change another’s. This gives the approved starter layout to any product that has not got one yet.', 'star-electric' ); ?>
			</p>
			<p class="description">
				<?php esc_html_e( 'Safe to run as often as you like: a product already carrying the starter layout is left alone, and a product somebody has edited in Elementor is never written over.', 'star-electric' ); ?>
			</p>
			<p>
				<button type="button" class="button" id="star-layouts-run"><?php esc_html_e( 'Give products their starter layout', 'star-electric' ); ?></button>
				<span id="star-layouts-state" class="description"></span>
			</p>

			<h2><?php esc_html_e( 'Reconciliation', 'star-electric' ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'Compares WordPress against the source payload. Every row must read OK before the migration counts as complete.', 'star-electric' ); ?>
			</p>
			<p>
				<button type="button" class="button" id="star-import-audit"><?php esc_html_e( 'Run audit', 'star-electric' ); ?></button>
				<button type="button" class="button" id="star-import-recount"><?php esc_html_e( 'Recount categories and brands', 'star-electric' ); ?></button>
			</p>
			<p class="description">
				<?php esc_html_e( 'Recount if the shop’s category or brand filters look empty: an import that was interrupted can leave the term counts at zero.', 'star-electric' ); ?>
			</p>
			<table class="widefat striped" id="star-import-audit-table" hidden>
				<thead>
					<tr>
						<th><?php esc_html_e( 'Item', 'star-electric' ); ?></th>
						<th><?php esc_html_e( 'Source', 'star-electric' ); ?></th>
						<th><?php esc_html_e( 'WordPress', 'star-electric' ); ?></th>
						<th><?php esc_html_e( 'Status', 'star-electric' ); ?></th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Persist the image source setting.
	 */
	private static function save_settings(): void {
		if ( ! isset( $_POST['star_electric_source_base'] ) && ! isset( $_POST['star_electric_form_email'] ) ) {
			return;
		}
		check_admin_referer( self::NONCE );

		$saved = array();

		if ( isset( $_POST['star_electric_source_base'] ) ) {
			$base = esc_url_raw( wp_unslash( $_POST['star_electric_source_base'] ) );
			if ( '' !== $base ) {
				update_option( Star_Electric_Importer::SOURCE_OPTION, untrailingslashit( $base ), false );
				$saved[] = __( 'Image source', 'star-electric' );
			}
		}

		if ( isset( $_POST['star_electric_form_email'] ) ) {
			$email = sanitize_email( wp_unslash( $_POST['star_electric_form_email'] ) );
			if ( '' !== $email && is_email( $email ) ) {
				update_option( Star_Electric_Forms::EMAIL_OPTION, $email, false );
				$saved[] = __( 'Enquiry recipient', 'star-electric' );
			} elseif ( '' !== $email ) {
				add_settings_error( self::SLUG, 'email', __( 'That email address is not valid, so it was not saved.', 'star-electric' ), 'error' );
			}
		}

		if ( $saved ) {
			add_settings_error(
				self::SLUG,
				'saved',
				sprintf(
					/* translators: %s: comma-separated list of saved settings */
					__( 'Saved: %s.', 'star-electric' ),
					implode( ', ', $saved )
				),
				'updated'
			);
		}

		settings_errors( self::SLUG );
	}

	/**
	 * Shared guard for every endpoint on this screen.
	 */
	private static function guard(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Insufficient permissions.' ), 403 );
		}
		check_ajax_referer( self::NONCE, 'nonce' );
	}

	/**
	 * Run one batch of one step.
	 */
	public static function ajax(): void {
		self::guard();

		$step = isset( $_POST['step'] ) ? sanitize_key( wp_unslash( $_POST['step'] ) ) : '';
		if ( ! isset( self::STEPS[ $step ] ) ) {
			wp_send_json_error( array( 'message' => 'Unknown step: ' . $step ), 400 );
		}

		$offset = isset( $_POST['offset'] ) ? max( 0, (int) $_POST['offset'] ) : 0;
		$size   = isset( $_POST['size'] ) ? (int) $_POST['size'] : (int) self::STEPS[ $step ]['batch'];
		$size   = max( 1, min( 200, $size ) );

		switch ( $step ) {
			case 'media':
				$result = Star_Electric_Importer::step_media( $offset, $size );
				break;
			case 'taxonomies':
				$result = Star_Electric_Importer::step_taxonomies();
				break;
			case 'ranges':
				$result = Star_Electric_Importer::step_ranges( $offset, $size );
				break;
			case 'products':
				$result = Star_Electric_Importer::step_products( $offset, $size );
				break;
			default:
				wp_send_json_error( array( 'message' => 'Unknown step.' ), 400 );
				return;
		}

		$totals = Star_Electric_Importer::progress()[ $step ] ?? array();

		wp_send_json_success(
			array(
				'batch'  => $result,
				'totals' => $totals,
			)
		);
	}

	/**
	 * Reconcile WordPress against the source payload.
	 */
	public static function ajax_audit(): void {
		self::guard();
		wp_send_json_success( array( 'rows' => Star_Electric_Importer::audit() ) );
	}

	/**
	 * Recount the catalogue taxonomies.
	 */
	public static function ajax_recount(): void {
		self::guard();
		wp_send_json_success( Star_Electric_Importer::recount() );
	}

	/**
	 * Clear the counters for a step so it starts again from the top.
	 */
	public static function ajax_reset(): void {
		self::guard();
		$step = isset( $_POST['step'] ) ? sanitize_key( wp_unslash( $_POST['step'] ) ) : '';
		if ( 'all' !== $step && ! isset( self::STEPS[ $step ] ) ) {
			wp_send_json_error( array( 'message' => 'Unknown step.' ), 400 );
		}
		Star_Electric_Importer::reset( $step );
		wp_send_json_success( array( 'step' => $step ) );
	}
}
