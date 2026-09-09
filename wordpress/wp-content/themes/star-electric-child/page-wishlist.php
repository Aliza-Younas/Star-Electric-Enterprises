<?php
/**
 * My Wishlist.
 *
 * A port of wishlist.html. Saved products are held in this browser's
 * localStorage, exactly as the approved page says they are, and the rows are
 * rendered by the server so that a saved product's price, quote-only status
 * and availability read the same here as on the shop.
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
		__( 'Home', 'star-electric-child' )       => home_url( '/' ),
		__( 'My Account', 'star-electric-child' ) => Star_Electric_Shell::url( 'account' ),
		__( 'Wishlist', 'star-electric-child' )   => '',
	),
	__( 'My Wishlist', 'star-electric-child' ),
	__( 'Products you have saved. Saved items are stored in this browser only.', 'star-electric-child' )
);
?>

<section class="section section--sm">
	<div class="container">

		<div class="cart-panel" id="wishlistPanel" hidden>
			<table class="table table--stack">
				<caption class="sr-only"><?php esc_html_e( 'Saved products', 'star-electric-child' ); ?></caption>
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Product', 'star-electric-child' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Price', 'star-electric-child' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Stock', 'star-electric-child' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Actions', 'star-electric-child' ); ?></th>
					</tr>
				</thead>
				<tbody id="wishlistBody"></tbody>
			</table>
		</div>

		<div id="wishlistEmpty" hidden>
			<div class="empty-state">
				<span class="empty-state__ico">
					<?php echo Star_Electric_Shell::icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
				<h2><?php esc_html_e( 'Your wishlist is empty', 'star-electric-child' ); ?></h2>
				<p><?php esc_html_e( 'Use the heart icon on any product to save it here for later. Saved items are handy when you are pricing up a job over several visits.', 'star-electric-child' ); ?></p>
				<div class="btn-row">
					<a class="btn btn--accent" href="<?php echo esc_url( Star_Electric_Shell::url( 'shop' ) ); ?>"><?php esc_html_e( 'Browse the Shop', 'star-electric-child' ); ?></a>
					<a class="btn btn--ghost" href="<?php echo esc_url( Star_Electric_Shell::url( 'deals' ) ); ?>"><?php esc_html_e( 'See Current Deals', 'star-electric-child' ); ?></a>
				</div>
			</div>
		</div>

		<?php
		/*
		 * A failed lookup must never be shown as an empty wishlist: a shopper
		 * would read that as their saved items having been thrown away.
		 */
		?>
		<div id="wishlistError" hidden>
			<div class="alert alert--info">
				<?php echo Star_Electric_Shell::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span><?php esc_html_e( 'Your saved products could not be loaded just now. They are still saved in this browser — reload the page to try again.', 'star-electric-child' ); ?></span>
			</div>
		</div>

		<noscript>
			<div class="alert alert--info" style="margin-top:16px">
				<?php echo Star_Electric_Shell::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span><?php esc_html_e( 'The wishlist needs JavaScript, because saved products are stored in your browser rather than on this website.', 'star-electric-child' ); ?></span>
			</div>
		</noscript>

	</div>
</section>

<section class="section section--tint" aria-labelledby="wishCatTitle">
	<div class="container">
		<div class="section__head">
			<div>
				<h2 class="section__title" id="wishCatTitle"><?php esc_html_e( 'Keep browsing', 'star-electric-child' ); ?></h2>
				<p class="section__sub"><?php esc_html_e( 'Jump back into the departments you were shopping.', 'star-electric-child' ); ?></p>
			</div>
			<a class="link-more" href="<?php echo esc_url( Star_Electric_Shell::url( 'shop' ) ); ?>">
				<?php esc_html_e( 'Go to shop', 'star-electric-child' ); ?>
				<?php echo Star_Electric_Shell::icon( 'arrowright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>
		<?php get_template_part( 'template-parts/category-grid' ); ?>
	</div>
</section>

<?php
get_footer();
