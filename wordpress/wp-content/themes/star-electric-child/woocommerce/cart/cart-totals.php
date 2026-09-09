<?php
/**
 * Cart totals.
 *
 * The approved cart shows a subtotal, a total and a note that any delivery
 * charge is confirmed before an order is placed. It shows no delivery row and
 * no tax row, because neither is on record.
 *
 * The shipping, fee and tax rows below are WooCommerce's own conditionals
 * rather than a flat omission. Nothing is configured today, so none of them
 * renders and the panel matches the approved design exactly; if the store ever
 * sets a real rate, the total will account for it instead of quietly
 * understating what a shopper owes.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="panel cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h2 class="panel__title"><?php esc_html_e( 'Cart Totals', 'star-electric-child' ); ?></h2>

	<div class="summary__row">
		<span><?php esc_html_e( 'Subtotal', 'star-electric-child' ); ?></span>
		<strong id="sumSubtotal"><?php wc_cart_totals_subtotal_html(); ?></strong>
	</div>

	<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
		<div class="summary__row coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
			<span><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
			<strong><?php wc_cart_totals_coupon_html( $coupon ); ?></strong>
		</div>
	<?php endforeach; ?>

	<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
		<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>
		<?php wc_cart_totals_shipping_html(); ?>
		<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>
	<?php endif; ?>

	<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
		<div class="summary__row fee">
			<span><?php echo esc_html( $fee->name ); ?></span>
			<strong><?php wc_cart_totals_fee_html( $fee ); ?></strong>
		</div>
	<?php endforeach; ?>

	<?php
	if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
		$taxable_address = WC()->customer->get_taxable_address();
		$estimated_text  = '';

		if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
			/* translators: %s location. */
			$estimated_text = sprintf( ' <small>' . esc_html__( '(estimated for %s)', 'star-electric-child' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] );
		}

		if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
			foreach ( WC()->cart->get_tax_totals() as $code => $tax ) {
				?>
				<div class="summary__row tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
					<span><?php echo esc_html( $tax->label ) . wp_kses_post( $estimated_text ); ?></span>
					<strong><?php echo wp_kses_post( $tax->formatted_amount ); ?></strong>
				</div>
				<?php
			}
		} else {
			?>
			<div class="summary__row tax-total">
				<span><?php echo esc_html( WC()->countries->tax_or_vat() ) . wp_kses_post( $estimated_text ); ?></span>
				<strong><?php wc_cart_totals_taxes_total_html(); ?></strong>
			</div>
			<?php
		}
	}
	?>

	<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

	<div class="summary__total">
		<span><?php esc_html_e( 'Total', 'star-electric-child' ); ?></span>
		<strong id="sumTotal"><?php wc_cart_totals_order_total_html(); ?></strong>
	</div>

	<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

	<div class="wc-proceed-to-checkout" style="margin-top:20px">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<p class="alert alert--info" style="margin-top:16px">
		<?php echo Star_Electric_Shell::icon( 'lock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span><?php esc_html_e( 'This total covers the items only. Any delivery charge is confirmed with you before an order is placed.', 'star-electric-child' ); ?></span>
	</p>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>

<div class="info-card" style="margin-top:16px">
	<h3><?php esc_html_e( 'Buying in quantity?', 'star-electric-child' ); ?></h3>
	<p class="t-sm t-muted" style="margin-bottom:16px">
		<?php esc_html_e( 'Larger project requirements can be quoted separately.', 'star-electric-child' ); ?>
	</p>
	<a class="btn btn--ghost btn--block btn--sm" href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>">
		<?php esc_html_e( 'Request a Bulk Quote', 'star-electric-child' ); ?>
	</a>
</div>
