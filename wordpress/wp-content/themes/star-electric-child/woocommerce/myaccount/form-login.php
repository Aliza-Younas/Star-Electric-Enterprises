<?php
/**
 * The signed-out account page.
 *
 * The approved storefront states that account sign-in is not available and
 * offers the services that genuinely work instead. That decision has not
 * changed here - customer registration is switched off in WooCommerce - so a
 * login form on this page would invite people to sign in to something that
 * does not accept them. This is the approved services grid, and a signed-in
 * user still gets WooCommerce's real dashboard, because WooCommerce only
 * loads this template when nobody is signed in.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$star_cards = array(
	array( 'cart', __( 'Cart', 'star-electric-child' ), __( 'Review the items you have added and adjust quantities before enquiring.', 'star-electric-child' ), Star_Electric_Shell::url( 'cart' ) ),
	array( 'heart', __( 'Wishlist', 'star-electric-child' ), __( 'Products you have saved while browsing on this device.', 'star-electric-child' ), Star_Electric_Shell::url( 'wishlist' ) ),
	array( 'truck', __( 'Track an Order', 'star-electric-child' ), __( 'Check where an order you have already placed has reached.', 'star-electric-child' ), Star_Electric_Shell::url( 'track-order' ) ),
	array( 'doc', __( 'Request a Quote', 'star-electric-child' ), __( 'Send us your list and we will price it against current stock.', 'star-electric-child' ), Star_Electric_Shell::url( 'quote' ) ),
	array( 'headset', __( 'Contact Us', 'star-electric-child' ), __( 'Ask about a product, an order, a delivery or a replacement.', 'star-electric-child' ), Star_Electric_Shell::url( 'contact' ) ),
);
?>
<div class="acct-layout">

	<div class="panel acct-notice">
		<h2 class="panel__title" style="margin-bottom:8px"><?php esc_html_e( 'Account sign-in', 'star-electric-child' ); ?></h2>
		<p class="t-sm t-muted">
			<?php esc_html_e( 'Account sign-in is not currently available online. You can still use the services below, and the store will look after anything else directly.', 'star-electric-child' ); ?>
		</p>
	</div>

	<ul class="acct-grid">
		<?php foreach ( $star_cards as $star_card ) : ?>
			<li>
				<a class="acct-card" href="<?php echo esc_url( $star_card[3] ); ?>">
					<span class="acct-card__ico">
						<?php echo Star_Electric_Shell::icon( $star_card[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<span class="acct-card__body">
						<span class="acct-card__title"><?php echo esc_html( $star_card[1] ); ?></span>
						<span class="acct-card__text"><?php echo esc_html( $star_card[2] ); ?></span>
					</span>
					<?php echo Star_Electric_Shell::icon( 'arrowright', 'acct-card__go' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

</div>
