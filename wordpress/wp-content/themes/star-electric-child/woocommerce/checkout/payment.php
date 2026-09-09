<?php
/**
 * The checkout payment block.
 *
 * WooCommerce's default tells a shopper with no available gateway that there
 * are none "for your location", and suggests contacting us "if you require
 * assistance or wish to make alternate arrangements". Neither is true here:
 * nothing is unavailable because of where they are, and the alternate
 * arrangement is not a favour - it is how this store sells. So the empty state
 * says what is actually the case and points at the routes that work.
 *
 * When the store does configure a payment method, this template renders it
 * through WooCommerce's own hooks with no further change.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>
<div id="payment" class="woocommerce-checkout-payment">
	<h2 class="panel__title" style="margin:24px 0 16px"><?php esc_html_e( 'Payment Method', 'star-electric-child' ); ?></h2>

	<?php if ( WC()->cart->needs_payment() ) : ?>
		<ul class="wc_payment_methods payment_methods methods stack">
			<?php
			if ( ! empty( $available_gateways ) ) {
				foreach ( $available_gateways as $gateway ) {
					wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
				}
			} else {
				?>
				<li>
					<div class="alert alert--info">
						<?php echo Star_Electric_Shell::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span>
							<strong><?php esc_html_e( 'No payment is taken on this website.', 'star-electric-child' ); ?></strong>
							<?php esc_html_e( 'No payment method is set up, so an order cannot be completed here. The store confirms the order and arranges payment with you directly.', 'star-electric-child' ); ?>
						</span>
					</div>
				</li>
				<?php
			}
			?>
		</ul>
	<?php endif; ?>

	<div class="form-row place-order">
		<noscript>
			<?php esc_html_e( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the Update Totals button before placing your order.', 'star-electric-child' ); ?>
			<br><button type="submit" class="btn btn--ghost" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'star-electric-child' ); ?>"><?php esc_html_e( 'Update totals', 'star-electric-child' ); ?></button>
		</noscript>

		<?php wc_get_template( 'checkout/terms.php' ); ?>

		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

		<?php
		echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'woocommerce_order_button_html',
			'<button type="submit" class="btn btn--accent btn--lg btn--block" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>'
		);
		?>

		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

		<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
	</div>
</div>
<?php
if ( ! is_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}
