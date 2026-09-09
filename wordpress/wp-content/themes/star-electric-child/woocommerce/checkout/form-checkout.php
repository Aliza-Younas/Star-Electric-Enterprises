<?php
/**
 * The checkout form.
 *
 * A port of checkout.html's two-column layout onto WooCommerce's real checkout.
 * The billing fields, the order review and the payment block are WooCommerce's
 * own hooks, so an address validates and a total recalculates the way it should.
 *
 * The notice at the top is the approved page's own words. checkout.html carries
 * data-inactive-form="Online ordering is not open yet. Please contact the store
 * to place this order." - the static site said so because it had nowhere to post
 * to, and this site says so because no payment method is configured, which is
 * the store's decision and not an oversight. Either way it is true, and a
 * shopper reads it before filling the form rather than after.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_ajax() ) {
	do_action( 'woocommerce_before_checkout_form', $checkout );

	// Registration is closed on this site, so the "you must be logged in" branch
	// of WooCommerce's default template cannot apply and is not reproduced.
	if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
		echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to check out.', 'star-electric-child' ) ) );
		return;
	}
}

$star_gateways = WC()->payment_gateways() ? WC()->payment_gateways->get_available_payment_gateways() : array();
?>

<div id="checkoutMain">

	<?php if ( empty( $star_gateways ) ) : ?>
		<div class="alert alert--info" style="margin-bottom:24px">
			<?php echo Star_Electric_Shell::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span>
				<strong><?php esc_html_e( 'Online ordering is not open yet.', 'star-electric-child' ); ?></strong>
				<?php
				printf(
					/* translators: 1: contact page link, 2: quote request link */
					esc_html__( 'No payment method is set up on this website, so an order cannot be completed here. Send the items you need through the %1$s or a %2$s and the store will confirm price, availability and collection or delivery with you directly.', 'star-electric-child' ),
					'<a class="link-inline" href="' . esc_url( Star_Electric_Shell::url( 'contact' ) ) . '">' . esc_html__( 'contact form', 'star-electric-child' ) . '</a>',
					'<a class="link-inline" href="' . esc_url( Star_Electric_Shell::url( 'quote' ) ) . '">' . esc_html__( 'quotation request', 'star-electric-child' ) . '</a>'
				);
				?>
			</span>
		</div>
	<?php endif; ?>

	<form name="checkout" method="post" class="checkout woocommerce-checkout"
		action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

		<?php if ( $checkout->get_checkout_fields() ) : ?>

			<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

			<div class="panel" style="margin-bottom:24px" id="customer_details">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>

			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

		<?php endif; ?>

		<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

		<h2 class="sr-only" id="order_review_heading"><?php esc_html_e( 'Your order', 'star-electric-child' ); ?></h2>

		<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

		<div class="panel woocommerce-checkout-review-order" id="order_review">
			<?php do_action( 'woocommerce_checkout_order_review' ); ?>
		</div>

		<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

	</form>
</div>

<aside class="summary" id="checkoutAside">
	<div class="panel">
		<h2 class="panel__title" style="margin-bottom:16px"><?php esc_html_e( 'Your Order', 'star-electric-child' ); ?></h2>

		<?php
		/*
		 * A static picture of the cart, so the shopper can see what they are
		 * ordering while the live, recalculating review sits inside the form.
		 */
		foreach ( WC()->cart->get_cart() as $star_key => $star_item ) :
			$star_product = $star_item['data'];
			if ( ! $star_product || ! $star_product->exists() || $star_item['quantity'] <= 0 ) {
				continue;
			}
			?>
			<div class="summary__row">
				<span>
					<?php echo esc_html( $star_product->get_name() ); ?>
					<span class="t-faint">&times; <?php echo esc_html( (string) $star_item['quantity'] ); ?></span>
				</span>
				<strong><?php echo wp_kses_post( WC()->cart->get_product_subtotal( $star_product, $star_item['quantity'] ) ); ?></strong>
			</div>
		<?php endforeach; ?>

		<div class="summary__row" style="margin-top:16px">
			<span><?php esc_html_e( 'Subtotal', 'star-electric-child' ); ?></span>
			<strong id="coSubtotal"><?php wc_cart_totals_subtotal_html(); ?></strong>
		</div>

		<div class="summary__total">
			<span><?php esc_html_e( 'Total', 'star-electric-child' ); ?></span>
			<strong id="coTotal"><?php wc_cart_totals_order_total_html(); ?></strong>
		</div>

		<p class="t-xs t-muted" style="margin-top:16px">
			<a class="link-inline" href="<?php echo esc_url( Star_Electric_Shell::url( 'cart' ) ); ?>"><?php esc_html_e( 'Edit cart', 'star-electric-child' ); ?></a>
		</p>
	</div>

	<div class="info-card" style="margin-top:16px">
		<h3><?php esc_html_e( 'Need help with this order?', 'star-electric-child' ); ?></h3>
		<p class="t-sm t-muted" style="margin-bottom:16px">
			<?php esc_html_e( 'Send us the details and the store will come back to you to confirm the order and any delivery charge.', 'star-electric-child' ); ?>
		</p>
		<div class="btn-row">
			<a class="btn btn--ghost btn--sm" href="<?php echo esc_url( Star_Electric_Shell::url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact the store', 'star-electric-child' ); ?></a>
			<a class="btn btn--ghost btn--sm" href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>"><?php esc_html_e( 'Request a quotation', 'star-electric-child' ); ?></a>
		</div>
	</div>
</aside>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
