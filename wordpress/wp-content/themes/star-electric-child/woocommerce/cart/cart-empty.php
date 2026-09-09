<?php
/**
 * The empty cart.
 *
 * A port of the approved empty state. It offers the two routes that work on
 * this catalogue: browsing the shop, and a quotation - which is how 2,548 of
 * the 4,348 products are bought, so it is not a consolation link.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * WooCommerce hangs its own "Your cart is currently empty." notice on this
 * action. The approved page says it once, inside the panel below, so the
 * notice is dropped and the rest of the action - which other plugins may use -
 * is left alone.
 */
remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
do_action( 'woocommerce_cart_is_empty' );
?>
<div class="cart-main" id="cartEmpty">
	<div class="empty-state">
		<span class="empty-state__ico">
			<?php echo Star_Electric_Shell::icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</span>
		<h2><?php esc_html_e( 'Your cart is empty', 'star-electric-child' ); ?></h2>
		<p><?php esc_html_e( 'Browse the catalogue and add the items you need, or send us a list for a bulk quotation.', 'star-electric-child' ); ?></p>
		<div class="btn-row">
			<a class="btn btn--accent" href="<?php echo esc_url( Star_Electric_Shell::url( 'shop' ) ); ?>">
				<?php esc_html_e( 'Browse the Shop', 'star-electric-child' ); ?>
			</a>
			<a class="btn btn--ghost" href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>">
				<?php esc_html_e( 'Request a Quote', 'star-electric-child' ); ?>
			</a>
		</div>
	</div>
</div>
