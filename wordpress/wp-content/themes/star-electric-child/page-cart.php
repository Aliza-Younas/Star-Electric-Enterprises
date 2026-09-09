<?php
/**
 * The cart page.
 *
 * A port of cart.html's page head and layout. The cart itself is WooCommerce's,
 * rendered through the templates in woocommerce/cart/.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="page-head">
	<div class="container">
		<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'star-electric-child' ); ?>">
			<ol class="breadcrumb">
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'star-electric-child' ); ?></a></li>
				<li class="sep" aria-hidden="true">/</li>
				<li aria-current="page"><?php esc_html_e( 'Cart', 'star-electric-child' ); ?></li>
			</ol>
		</nav>
		<h1 class="page-head__title"><?php esc_html_e( 'Shopping Cart', 'star-electric-child' ); ?></h1>
		<?php Star_Electric_Shell::steps( 'cart' ); ?>
	</div>
</div>

<?php
/*
 * The two-column layout stays whether or not there is anything in the cart, so
 * the empty state is the width the approved page gives it rather than stretched
 * across the page. WooCommerce's own "your cart is currently empty" notice,
 * which would otherwise land in the empty second column, is removed in
 * cart-empty.php - the panel says it already.
 */
?>
<section class="section section--sm">
	<div class="container cart-layout">
		<?php
		while ( have_posts() ) {
			the_post();
			the_content();
		}
		?>
	</div>
</section>

<?php
get_footer();
