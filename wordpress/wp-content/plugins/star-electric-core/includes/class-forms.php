<?php
/**
 * Quote, contact and complaint forms.
 *
 * On the approved static site these forms are visual only - they carry a
 * data-inactive-form notice and submit nowhere, because a static page has
 * nothing to submit to. On WordPress they have to actually work, and for a
 * catalogue where 2,548 of 4,348 products are priced on enquiry, the quote form
 * is not a nicety: it is the buying route for most of the shop.
 *
 * Every submission is stored as a private post before the mail is attempted, so
 * a failed or silently dropped email never loses an enquiry - the store can
 * still read it in the dashboard.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form handling.
 */
class Star_Electric_Forms {

	/** Post type holding submissions. */
	public const POST_TYPE = 'star_enquiry';

	/** Where submissions are emailed. */
	public const EMAIL_OPTION = 'star_electric_form_email';

	/** Nonce action. */
	private const NONCE = 'star_electric_form';

	/**
	 * Hook up.
	 */
	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_action( 'admin_post_nopriv_star_electric_form', array( __CLASS__, 'handle' ) );
		add_action( 'admin_post_star_electric_form', array( __CLASS__, 'handle' ) );
	}

	/**
	 * Store submissions where the shop can read them.
	 */
	public static function register(): void {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'          => array(
					'name'          => __( 'Enquiries', 'star-electric' ),
					'singular_name' => __( 'Enquiry', 'star-electric' ),
					'menu_name'     => __( 'Enquiries', 'star-electric' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'menu_icon'       => 'dashicons-email-alt',
				'menu_position'   => 59,
				'capability_type' => 'post',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
				'supports'        => array( 'title', 'editor' ),
				'has_archive'     => false,
				'rewrite'         => false,
				'exclude_from_search' => true,
			)
		);
	}

	/**
	 * Where enquiries are sent.
	 *
	 * Held as an option rather than written into the code: this repository is
	 * public, and a recipient address does not belong in it.
	 */
	public static function recipient(): string {
		$email = (string) get_option( self::EMAIL_OPTION, '' );
		if ( '' === $email || ! is_email( $email ) ) {
			$email = (string) get_option( 'admin_email' );
		}
		return $email;
	}

	/**
	 * The three forms and the fields each one accepts.
	 *
	 * Anything not listed here is discarded, so a crafted POST cannot smuggle
	 * extra content into the email.
	 */
	public static function forms(): array {
		return array(
			'quote'     => array(
				'title'    => __( 'Quote request', 'star-electric' ),
				'required' => array( 'name', 'phone', 'email', 'project_type', 'requirements', 'consent' ),
				'fields'   => array(
					'name'          => __( 'Full name', 'star-electric' ),
					'company'       => __( 'Company / Contractor name', 'star-electric' ),
					'phone'         => __( 'Phone', 'star-electric' ),
					'email'         => __( 'Email', 'star-electric' ),
					'project_type'  => __( 'Project type', 'star-electric' ),
					'required_date' => __( 'Required delivery date', 'star-electric' ),
					'requirements'  => __( 'Product requirements', 'star-electric' ),
					'quantities'    => __( 'Estimated quantities / order value', 'star-electric' ),
					'message'       => __( 'Additional notes', 'star-electric' ),
					'product'       => __( 'Product enquired about', 'star-electric' ),
				),
			),
			'contact'   => array(
				'title'    => __( 'Contact message', 'star-electric' ),
				'required' => array( 'name', 'phone', 'email', 'subject', 'message', 'consent' ),
				'fields'   => array(
					'name'    => __( 'Full name', 'star-electric' ),
					'phone'   => __( 'Phone', 'star-electric' ),
					'email'   => __( 'Email', 'star-electric' ),
					'subject' => __( 'Subject', 'star-electric' ),
					'message' => __( 'Message', 'star-electric' ),
				),
			),
			'complaint' => array(
				'title'    => __( 'Complaint', 'star-electric' ),
				'required' => array( 'name', 'phone', 'email', 'type', 'subject', 'description', 'consent' ),
				'fields'   => array(
					'name'        => __( 'Full name', 'star-electric' ),
					'order'       => __( 'Order number', 'star-electric' ),
					'phone'       => __( 'Phone', 'star-electric' ),
					'email'       => __( 'Email', 'star-electric' ),
					'type'        => __( 'Complaint type', 'star-electric' ),
					'subject'     => __( 'Subject', 'star-electric' ),
					'description' => __( 'Description', 'star-electric' ),
				),
			),
		);
	}

	/**
	 * Handle a submission.
	 */
	public static function handle(): void {
		$form  = isset( $_POST['star_form'] ) ? sanitize_key( wp_unslash( $_POST['star_form'] ) ) : '';
		$forms = self::forms();

		if ( ! isset( $forms[ $form ] ) ) {
			wp_safe_redirect( home_url( '/' ) );
			exit;
		}

		$back = isset( $_POST['_wp_http_referer'] )
			? esc_url_raw( wp_unslash( $_POST['_wp_http_referer'] ) )
			: home_url( '/' );
		$back = remove_query_arg( array( 'star_sent', 'star_error' ), $back );

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), self::NONCE ) ) {
			self::bounce( $back, 'expired' );
		}

		// A hidden field a person never fills in and a bot usually does.
		if ( ! empty( $_POST['star_website'] ) ) {
			self::bounce( $back, 'rejected' );
		}

		$spec   = $forms[ $form ];
		$values = array();

		foreach ( $spec['fields'] as $key => $label ) {
			$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
			if ( 'email' === $key ) {
				$values[ $key ] = sanitize_email( (string) $raw );
			} elseif ( in_array( $key, array( 'requirements', 'message', 'description' ), true ) ) {
				$values[ $key ] = sanitize_textarea_field( (string) $raw );
			} else {
				$values[ $key ] = sanitize_text_field( (string) $raw );
			}
		}

		$consent = ! empty( $_POST['consent'] );

		foreach ( $spec['required'] as $key ) {
			if ( 'consent' === $key ) {
				if ( ! $consent ) {
					self::bounce( $back, 'consent' );
				}
				continue;
			}
			if ( '' === trim( (string) ( $values[ $key ] ?? '' ) ) ) {
				self::bounce( $back, 'missing' );
			}
		}

		if ( isset( $values['email'] ) && ! is_email( $values['email'] ) ) {
			self::bounce( $back, 'email' );
		}

		// A product id arrives from a "Request a Quote" button; resolve it to a
		// name so the enquiry is readable without looking anything up.
		if ( 'quote' === $form && ! empty( $values['product'] ) ) {
			$product = function_exists( 'wc_get_product' ) ? wc_get_product( (int) $values['product'] ) : null;
			if ( $product instanceof WC_Product ) {
				$values['product'] = sprintf(
					'%s (#%d) %s',
					$product->get_name(),
					$product->get_id(),
					(string) get_permalink( $product->get_id() )
				);
			}
		}

		$stored = self::store( $form, $spec, $values );
		$sent   = self::notify( $form, $spec, $values );

		if ( ! $stored && ! $sent ) {
			self::bounce( $back, 'failed' );
		}

		wp_safe_redirect( add_query_arg( 'star_sent', $form, $back ) );
		exit;
	}

	/**
	 * Redirect back with an error code.
	 *
	 * @param string $back  Return URL.
	 * @param string $error Error code.
	 */
	private static function bounce( string $back, string $error ): void {
		wp_safe_redirect( add_query_arg( 'star_error', $error, $back ) );
		exit;
	}

	/**
	 * Keep the submission in WordPress.
	 *
	 * @param array $spec   Form spec.
	 * @param array $values Submitted values.
	 * @param string $form  Form key.
	 */
	private static function store( string $form, array $spec, array $values ): bool {
		$lines = array();
		foreach ( $spec['fields'] as $key => $label ) {
			if ( '' !== trim( (string) ( $values[ $key ] ?? '' ) ) ) {
				$lines[] = $label . ': ' . $values[ $key ];
			}
		}

		$id = wp_insert_post(
			array(
				'post_type'    => self::POST_TYPE,
				'post_status'  => 'private',
				'post_title'   => sprintf(
					'%s — %s',
					$spec['title'],
					$values['name'] ?? __( 'Unknown', 'star-electric' )
				),
				'post_content' => implode( "\n", $lines ),
			),
			true
		);

		if ( is_wp_error( $id ) ) {
			return false;
		}

		update_post_meta( $id, '_star_form', $form );
		foreach ( $values as $key => $value ) {
			if ( '' !== trim( (string) $value ) ) {
				update_post_meta( $id, '_star_' . $key, $value );
			}
		}

		return true;
	}

	/**
	 * Email the submission to the store.
	 *
	 * @param string $form   Form key.
	 * @param array  $spec   Form spec.
	 * @param array  $values Submitted values.
	 */
	private static function notify( string $form, array $spec, array $values ): bool {
		unset( $form );

		$body = array(
			$spec['title'],
			str_repeat( '-', 40 ),
			'',
		);

		foreach ( $spec['fields'] as $key => $label ) {
			if ( '' !== trim( (string) ( $values[ $key ] ?? '' ) ) ) {
				$body[] = $label . ': ' . $values[ $key ];
			}
		}

		$body[] = '';
		$body[] = sprintf(
			/* translators: %s: site name */
			__( 'Sent from the %s website.', 'star-electric' ),
			get_bloginfo( 'name' )
		);

		$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
		if ( ! empty( $values['email'] ) && is_email( $values['email'] ) ) {
			$headers[] = sprintf(
				'Reply-To: %s <%s>',
				$values['name'] ?? $values['email'],
				$values['email']
			);
		}

		return (bool) wp_mail(
			self::recipient(),
			sprintf( '[%s] %s', get_bloginfo( 'name' ), $spec['title'] ),
			implode( "\n", $body ),
			$headers
		);
	}

	/**
	 * The message to show after a redirect back, or ''.
	 *
	 * @return array{0:string,1:string} Type ('ok'|'error') and message.
	 */
	public static function notice(): array {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['star_sent'] ) ) {
			return array(
				'ok',
				__( 'Thank you — your enquiry has been sent. We will come back to you with a written response.', 'star-electric' ),
			);
		}

		$errors = array(
			'expired'  => __( 'That form had been open too long. Please send it again.', 'star-electric' ),
			'missing'  => __( 'Please complete every required field and send the form again.', 'star-electric' ),
			'email'    => __( 'That email address does not look right. Please check it and send the form again.', 'star-electric' ),
			'consent'  => __( 'Please confirm you agree to your details being used to reply to you.', 'star-electric' ),
			'rejected' => __( 'That submission could not be accepted.', 'star-electric' ),
			'failed'   => __( 'The enquiry could not be sent. Please try again, or contact the store directly.', 'star-electric' ),
		);

		$code = isset( $_GET['star_error'] ) ? sanitize_key( wp_unslash( $_GET['star_error'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( isset( $errors[ $code ] ) ) {
			return array( 'error', $errors[ $code ] );
		}

		return array( '', '' );
	}

	/**
	 * The hidden fields every form needs.
	 *
	 * @param string $form Form key.
	 */
	public static function fields( string $form ): void {
		wp_nonce_field( self::NONCE );
		printf( '<input type="hidden" name="action" value="star_electric_form">' );
		printf( '<input type="hidden" name="star_form" value="%s">', esc_attr( $form ) );
		// Honeypot. Hidden from people, offered to bots.
		echo '<div class="sr-only" aria-hidden="true"><label>Website<input type="text" name="star_website" tabindex="-1" autocomplete="off"></label></div>';
	}

	/**
	 * Where a form posts to.
	 */
	public static function action(): string {
		return admin_url( 'admin-post.php' );
	}
}
