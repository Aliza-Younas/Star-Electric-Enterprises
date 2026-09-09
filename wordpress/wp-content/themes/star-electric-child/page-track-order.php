<?php
/**
 * Track Order.
 *
 * A port of track-order.html, with the form wired to WooCommerce's real order
 * tracking instead of the static page's placeholder. The static site could only
 * say "online order tracking is not available yet" because it had no orders to
 * look up; here the lookup is genuine, so the panel that said so is gone and
 * the form does the work.
 *
 * One field changed: the approved form asked for "billing email or phone".
 * WooCommerce matches on the billing email only, so asking for a phone number
 * that cannot be matched would send people away thinking their order is
 * missing.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

Star_Electric_Shell::page_head(
	array(
		__( 'Home', 'star-electric-child' )        => home_url( '/' ),
		__( 'Track Order', 'star-electric-child' ) => '',
	),
	__( 'Track Your Order', 'star-electric-child' ),
	__( 'Enter the order number from your confirmation along with the billing email used when the order was taken.', 'star-electric-child' )
);
?>

<section class="section section--sm">
	<div class="container container--mid">

		<div class="panel" style="margin-bottom:32px">
			<?php echo do_shortcode( '[woocommerce_order_tracking]' ); ?>

			<p class="field__hint" style="margin-top:16px">
				<?php
				printf(
					/* translators: %s: link to the contact page */
					esc_html__( 'Can’t find your order number? It appears on your order confirmation. You can also %s for help.', 'star-electric-child' ),
					'<a class="link-inline" href="' . esc_url( Star_Electric_Shell::url( 'contact' ) ) . '">' . esc_html__( 'contact the store', 'star-electric-child' ) . '</a>'
				);
				?>
			</p>
		</div>

		<div class="form-grid" style="margin-top:32px">
			<div class="info-card">
				<h3><?php esc_html_e( 'Order questions', 'star-electric-child' ); ?></h3>
				<p class="t-sm t-muted" style="margin-bottom:16px"><?php esc_html_e( 'Ask the store about an order, a delivery or a replacement.', 'star-electric-child' ); ?></p>
				<div class="btn-row">
					<a class="btn btn--ghost btn--sm" href="<?php echo esc_url( Star_Electric_Shell::url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact the store', 'star-electric-child' ); ?></a>
					<a class="btn btn--ghost btn--sm" href="<?php echo esc_url( Star_Electric_Shell::url( 'complaint' ) ); ?>"><?php esc_html_e( 'Report a problem', 'star-electric-child' ); ?></a>
				</div>
			</div>
			<div class="info-card">
				<h3><?php esc_html_e( 'Related pages', 'star-electric-child' ); ?></h3>
				<ul class="info-list">
					<li>
						<?php echo Star_Electric_Shell::icon( 'truck' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><a class="link-inline" href="<?php echo esc_url( Star_Electric_Shell::url( 'shipping' ) ); ?>"><?php esc_html_e( 'Shipping & delivery', 'star-electric-child' ); ?></a></span>
					</li>
					<li>
						<?php echo Star_Electric_Shell::icon( 'refresh' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><a class="link-inline" href="<?php echo esc_url( Star_Electric_Shell::url( 'returns' ) ); ?>"><?php esc_html_e( 'Returns & replacements', 'star-electric-child' ); ?></a></span>
					</li>
					<li>
						<?php echo Star_Electric_Shell::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><a class="link-inline" href="<?php echo esc_url( Star_Electric_Shell::url( 'faq' ) ); ?>"><?php esc_html_e( 'Frequently asked questions', 'star-electric-child' ); ?></a></span>
					</li>
				</ul>
			</div>
		</div>

	</div>
</section>

<?php
get_footer();
