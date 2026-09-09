<?php
/**
 * The wishlist.
 *
 * The approved storefront states plainly that "saved items are stored in this
 * browser only", and that is exactly what this is: the list of ids lives in
 * localStorage, and nothing about a visitor is stored on the server. Customer
 * accounts are not open, so a server-side wishlist would have nobody to belong
 * to - and quietly starting to record what anonymous visitors save would be a
 * different promise from the one the page makes.
 *
 * What the server does is render the rows, because the price, the quote-only
 * rule and the availability wording are decided in PHP in exactly one place.
 * Reimplementing them in JavaScript is how a wishlist ends up showing "Rs. 0"
 * for a product the shop correctly prices on enquiry.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders saved products for the wishlist page.
 */
class Star_Electric_Wishlist {

	/** The AJAX action name, shared with the front-end script. */
	public const ACTION = 'star_electric_wishlist';

	/**
	 * The second action: product cards rather than wishlist rows.
	 *
	 * The approved product page ends with "Recently Viewed", which is the same
	 * shape of problem - a list of ids the browser remembers, rendered by the
	 * server so the price and quote rules are not written twice.
	 */
	public const CARDS = 'star_electric_cards';

	/**
	 * A wishlist longer than this is almost certainly a broken client rather
	 * than a shopper, and rendering it would be an unbounded query.
	 */
	private const MAX = 100;

	/**
	 * Hook up.
	 */
	public static function init(): void {
		add_action( 'wp_ajax_' . self::ACTION, array( __CLASS__, 'respond' ) );
		add_action( 'wp_ajax_nopriv_' . self::ACTION, array( __CLASS__, 'respond' ) );
		add_action( 'wp_ajax_' . self::CARDS, array( __CLASS__, 'respond_cards' ) );
		add_action( 'wp_ajax_nopriv_' . self::CARDS, array( __CLASS__, 'respond_cards' ) );
	}

	/**
	 * Return the table rows for the posted product ids.
	 *
	 * No nonce is required and none would mean anything: this reads published
	 * catalogue data that any visitor can already see on the shop, it writes
	 * nothing, and a public page has to work for a visitor with no session.
	 */
	public static function respond(): void {
		$ids = self::posted_ids();

		if ( empty( $ids ) || ! function_exists( 'wc_get_product' ) ) {
			wp_send_json_success(
				array(
					'rows'  => '',
					'count' => 0,
				)
			);
		}

		/*
		 * Queried in one go and then re-ordered to match the saved list, so the
		 * page reads in the order things were saved rather than by post id.
		 */
		$found = get_posts(
			array(
				'post_type'      => 'product',
				'post__in'       => $ids,
				'post_status'    => 'publish',
				'posts_per_page' => count( $ids ),
				'fields'         => 'ids',
			)
		);

		$order = array_values( array_intersect( $ids, array_map( 'absint', (array) $found ) ) );

		ob_start();
		foreach ( $order as $id ) {
			$product = wc_get_product( $id );
			if ( $product instanceof WC_Product && $product->is_visible() ) {
				self::row( $product );
			}
		}

		wp_send_json_success(
			array(
				'rows'  => (string) ob_get_clean(),
				'count' => count( $order ),
			)
		);
	}

	/**
	 * The product ids the browser posted, cleaned and capped.
	 *
	 * @return int[]
	 */
	private static function posted_ids(): array {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$raw = isset( $_POST['ids'] ) ? sanitize_text_field( wp_unslash( $_POST['ids'] ) ) : '';

		return array_slice(
			array_values( array_unique( array_filter( array_map( 'absint', explode( ',', $raw ) ) ) ) ),
			0,
			self::MAX
		);
	}

	/**
	 * Product cards for the posted ids, in the order they were posted.
	 *
	 * Same contract as respond(): published catalogue data only, no writes, and
	 * nothing recorded about who asked.
	 */
	public static function respond_cards(): void {
		$ids = self::posted_ids();

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$skip = isset( $_POST['exclude'] ) ? absint( wp_unslash( $_POST['exclude'] ) ) : 0;
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$limit = isset( $_POST['limit'] ) ? max( 1, min( 12, absint( wp_unslash( $_POST['limit'] ) ) ) ) : 4;

		$ids = array_values( array_diff( $ids, array( $skip ) ) );

		if ( empty( $ids ) || ! function_exists( 'wc_get_product' ) ) {
			wp_send_json_success(
				array(
					'cards' => '',
					'count' => 0,
				)
			);
		}

		$found = get_posts(
			array(
				'post_type'      => 'product',
				'post__in'       => $ids,
				'post_status'    => 'publish',
				'posts_per_page' => count( $ids ),
				'fields'         => 'ids',
			)
		);

		$order = array_slice( array_values( array_intersect( $ids, array_map( 'absint', (array) $found ) ) ), 0, $limit );

		$keep = isset( $GLOBALS['product'] ) ? $GLOBALS['product'] : null;

		ob_start();
		foreach ( $order as $id ) {
			$product = wc_get_product( $id );
			if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
				continue;
			}
			$GLOBALS['post']    = get_post( $id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			$GLOBALS['product'] = $product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			setup_postdata( $GLOBALS['post'] );
			wc_get_template_part( 'content', 'product' );
		}
		wp_reset_postdata();
		$GLOBALS['product'] = $keep; // phpcs:ignore WordPress.WP.GlobalVariablesOverride

		wp_send_json_success(
			array(
				'cards' => (string) ob_get_clean(),
				'count' => count( $order ),
			)
		);
	}

	/**
	 * One saved product.
	 *
	 * The markup is the approved wishlist row, and the price and buy controls
	 * follow the same rules as the product card: a product with no published
	 * price offers a quotation, never a zero.
	 *
	 * @param WC_Product $product Product.
	 */
	private static function row( WC_Product $product ): void {
		$id    = $product->get_id();
		$link  = (string) get_permalink( $id );
		$quote = class_exists( 'Star_Electric_Quote_Only' )
			? Star_Electric_Quote_Only::is_quote( $product )
			: ( '' === (string) $product->get_price( 'edit' ) );

		$brand = wp_get_object_terms( $id, 'star_brand', array( 'fields' => 'names' ) );
		$brand = ( ! is_wp_error( $brand ) && $brand ) ? $brand[0] : '';

		$pill = class_exists( 'Star_Electric_Shell' )
			? Star_Electric_Shell::availability_pill( $product )
			: array( 'pill--unknown', __( 'Availability not specified', 'star-electric' ) );
		?>
		<tr data-wish-row="<?php echo esc_attr( (string) $id ); ?>">
			<td class="td-full">
				<div class="cart-item">
					<span class="cart-item__img">
						<a href="<?php echo esc_url( $link ); ?>" tabindex="-1">
							<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
						</a>
					</span>
					<span>
						<span class="cart-item__name">
							<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
						</span>
						<span class="cart-item__var"><?php echo esc_html( $brand ); ?></span>
					</span>
				</div>
			</td>
			<td data-label="<?php esc_attr_e( 'Price', 'star-electric' ); ?>">
				<?php if ( $quote ) : ?>
					<span class="price__quote"><?php esc_html_e( 'Request a Quote', 'star-electric' ); ?></span>
				<?php else : ?>
					<span class="price__now"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
				<?php endif; ?>
			</td>
			<td data-label="<?php esc_attr_e( 'Stock', 'star-electric' ); ?>">
				<span class="pill <?php echo esc_attr( $pill[0] ); ?>"><?php echo esc_html( $pill[1] ); ?></span>
			</td>
			<td data-label="<?php esc_attr_e( 'Actions', 'star-electric' ); ?>">
				<div class="btn-row">
					<?php if ( $quote ) : ?>
						<a class="btn btn--accent btn--sm" href="<?php echo esc_url( self::quote_url( $id ) ); ?>">
							<?php esc_html_e( 'Request a Quote', 'star-electric' ); ?>
						</a>
					<?php elseif ( ! $product->is_in_stock() ) : ?>
						<button class="btn btn--ghost btn--sm" type="button" disabled>
							<?php esc_html_e( 'Out of Stock', 'star-electric' ); ?>
						</button>
					<?php elseif ( $product->is_type( 'variable' ) ) : ?>
						<a class="btn btn--ghost btn--sm" href="<?php echo esc_url( $link ); ?>">
							<?php esc_html_e( 'Select Options', 'star-electric' ); ?>
						</a>
					<?php else : ?>
						<a class="btn btn--ghost btn--sm add_to_cart_button ajax_add_to_cart"
							href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
							data-product_id="<?php echo esc_attr( (string) $id ); ?>"
							data-quantity="1" rel="nofollow">
							<?php esc_html_e( 'Add to Cart', 'star-electric' ); ?>
						</a>
					<?php endif; ?>
					<button class="icon-btn icon-btn--sm" type="button" data-wish-remove="<?php echo esc_attr( (string) $id ); ?>"
						aria-label="<?php esc_attr_e( 'Remove from wishlist', 'star-electric' ); ?>">
						<?php
						echo class_exists( 'Star_Electric_Shell' )
							? Star_Electric_Shell::icon( 'trash' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							: '&times;';
						?>
					</button>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * The quotation link for a product.
	 *
	 * @param int $id Product id.
	 */
	private static function quote_url( int $id ): string {
		if ( class_exists( 'Star_Electric_Shell' ) ) {
			return Star_Electric_Shell::quote_url( $id );
		}
		return add_query_arg( 'product', $id, home_url( '/quote-request/' ) );
	}
}
