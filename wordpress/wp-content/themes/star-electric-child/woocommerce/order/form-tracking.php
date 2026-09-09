<?php
/**
 * The order tracking form.
 *
 * WooCommerce's own markup wearing the approved storefront's field classes, so
 * it matches every other form on the site. The field names, the nonce and the
 * submit button name are WooCommerce's and must not change - they are what the
 * tracking handler reads.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form class="track-form" id="trackForm" action="" method="post">
	<div class="field">
		<label class="field__label" for="orderid">
			<?php esc_html_e( 'Order number', 'star-electric-child' ); ?> <span class="req">*</span>
		</label>
		<input class="input" type="text" name="orderid" id="orderid" required
			placeholder="<?php esc_attr_e( 'The reference on your order confirmation', 'star-electric-child' ); ?>"
			value="<?php echo isset( $_REQUEST['orderid'] ) ? esc_attr( wp_unslash( $_REQUEST['orderid'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized ?>">
	</div>
	<div class="field">
		<label class="field__label" for="order_email">
			<?php esc_html_e( 'Billing email', 'star-electric-child' ); ?> <span class="req">*</span>
		</label>
		<input class="input" type="email" name="order_email" id="order_email" required autocomplete="email"
			placeholder="<?php esc_attr_e( 'The email address the order was taken against', 'star-electric-child' ); ?>"
			value="<?php echo isset( $_REQUEST['order_email'] ) ? esc_attr( wp_unslash( $_REQUEST['order_email'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized ?>">
	</div>
	<button class="btn btn--accent btn--lg" type="submit" name="track" value="<?php esc_attr_e( 'Track', 'star-electric-child' ); ?>">
		<?php esc_html_e( 'Track Order', 'star-electric-child' ); ?>
	</button>
	<?php wp_nonce_field( 'woocommerce-order_tracking', 'woocommerce-order-tracking-nonce' ); ?>
</form>
