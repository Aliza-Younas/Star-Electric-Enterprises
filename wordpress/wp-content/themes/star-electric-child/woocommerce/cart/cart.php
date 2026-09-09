<?php
/**
 * The cart.
 *
 * A port of cart.html onto the real WooCommerce cart. Every name, nonce and
 * hook WooCommerce reads is preserved - cart[key][qty], update_cart,
 * apply_coupon, the woocommerce-cart nonce and the remove URL - because those
 * are the contract; only the markup around them is the approved storefront's.
 *
 * The static page could not do the two things this one does: quantities really
 * update, and a coupon really applies. What it also could not do was show a
 * quote-only product in a cart, and nor can this - those products are refused
 * at add-to-cart by the plugin and can never reach here.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_cart' );
?>

<div class="cart-main">
	<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
		<?php do_action( 'woocommerce_before_cart_table' ); ?>

		<div class="cart-panel" id="cartPanel">
			<table class="table table--stack shop_table woocommerce-cart-form__contents">
				<caption class="sr-only"><?php esc_html_e( 'Products in your cart', 'star-electric-child' ); ?></caption>
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Product', 'star-electric-child' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Unit Price', 'star-electric-child' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Quantity', 'star-electric-child' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Subtotal', 'star-electric-child' ); ?></th>
						<th scope="col"><span class="sr-only"><?php esc_html_e( 'Remove', 'star-electric-child' ); ?></span></th>
					</tr>
				</thead>
				<tbody id="cartBody">
					<?php do_action( 'woocommerce_before_cart_contents' ); ?>

					<?php
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

						if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							continue;
						}

						$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
						?>
						<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
							<td class="td-full">
								<div class="cart-item">
									<span class="cart-item__img">
										<?php
										$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
										if ( '' === $product_permalink ) {
											echo wp_kses_post( $thumbnail );
										} else {
											printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) );
										}
										?>
									</span>
									<span>
										<span class="cart-item__name">
											<?php
											if ( '' === $product_permalink ) {
												echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) );
											} else {
												echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
											}
											?>
										</span>
										<span class="cart-item__var">
											<?php
											echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

											if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
												echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on back order', 'star-electric-child' ) . '</p>', $product_id ) );
											}
											?>
										</span>
									</span>
								</div>
							</td>

							<td data-label="<?php esc_attr_e( 'Unit Price', 'star-electric-child' ); ?>">
								<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ) ); ?>
							</td>

							<td data-label="<?php esc_attr_e( 'Quantity', 'star-electric-child' ); ?>">
								<?php
								if ( $_product->is_sold_individually() ) {
									$min_quantity = 1;
									$max_quantity = 1;
								} else {
									$min_quantity = 0;
									$max_quantity = $_product->get_max_purchase_quantity();
								}

								$product_quantity = woocommerce_quantity_input(
									array(
										'input_name'   => "cart[{$cart_item_key}][qty]",
										'input_value'  => $cart_item['quantity'],
										'max_value'    => $max_quantity,
										'min_value'    => $min_quantity,
										'product_name' => $_product->get_name(),
										'classes'      => array( 'input', 'qty' ),
									),
									$_product,
									false
								);

								echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							</td>

							<td data-label="<?php esc_attr_e( 'Subtotal', 'star-electric-child' ); ?>">
								<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ) ); ?>
							</td>

							<td data-label="<?php esc_attr_e( 'Remove', 'star-electric-child' ); ?>">
								<?php
								echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a href="%s" class="icon-btn icon-btn--sm remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">%s</a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										/* translators: %s: product name */
										esc_attr( sprintf( __( 'Remove %s from your cart', 'star-electric-child' ), wp_strip_all_tags( $_product->get_name() ) ) ),
										esc_attr( $product_id ),
										esc_attr( $_product->get_sku() ),
										Star_Electric_Shell::icon( 'trash' )
									),
									$cart_item_key
								);
								?>
							</td>
						</tr>
						<?php
					}
					?>

					<?php do_action( 'woocommerce_cart_contents' ); ?>

					<tr>
						<td colspan="5" class="actions">
							<?php
							/*
							 * Update cart is not a decoration: WooCommerce only reads a
							 * changed quantity when this button posts, so it stays even
							 * though the approved static page had no need of one.
							 */
							?>
							<button class="btn btn--ghost btn--sm" type="submit" name="update_cart"
								value="<?php esc_attr_e( 'Update cart', 'star-electric-child' ); ?>">
								<?php esc_html_e( 'Update cart', 'star-electric-child' ); ?>
							</button>
							<?php do_action( 'woocommerce_cart_actions' ); ?>
							<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
						</td>
					</tr>

					<?php do_action( 'woocommerce_after_cart_contents' ); ?>
				</tbody>
			</table>
		</div>

		<?php do_action( 'woocommerce_after_cart_table' ); ?>
	</form>

	<?php if ( wc_coupons_enabled() ) : ?>
		<div class="panel" style="margin-top:24px" id="couponPanel">
			<div class="form-grid" style="grid-template-columns:minmax(0,1fr) auto;align-items:end;gap:24px">
				<form class="field" method="post" action="<?php echo esc_url( wc_get_cart_url() ); ?>">
					<label class="field__label" for="coupon_code"><?php esc_html_e( 'Have a coupon code?', 'star-electric-child' ); ?></label>
					<div class="coupon">
						<input class="input" id="coupon_code" name="coupon_code" type="text"
							placeholder="<?php esc_attr_e( 'Enter coupon code', 'star-electric-child' ); ?>" autocomplete="off">
						<button class="btn btn--ghost" type="submit" name="apply_coupon"
							value="<?php esc_attr_e( 'Apply coupon', 'star-electric-child' ); ?>">
							<?php esc_html_e( 'Apply Coupon', 'star-electric-child' ); ?>
						</button>
					</div>
					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</form>
				<div class="btn-row" style="padding-bottom:22px">
					<a class="btn btn--ghost btn--sm" href="<?php echo esc_url( Star_Electric_Shell::url( 'shop' ) ); ?>">
						<?php esc_html_e( 'Continue Shopping', 'star-electric-child' ); ?>
					</a>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>

<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

<aside class="summary cart-collaterals" id="cartSummary">
	<?php do_action( 'woocommerce_cart_collaterals' ); ?>
</aside>

<?php do_action( 'woocommerce_after_cart' ); ?>
