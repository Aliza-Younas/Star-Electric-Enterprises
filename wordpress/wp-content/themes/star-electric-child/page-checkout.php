<?php
/**
 * The checkout page.
 *
 * A port of checkout.html's page head and two-column layout. The checkout
 * itself is WooCommerce's, rendered through woocommerce/checkout/.
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
				<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'cart' ) ); ?>"><?php esc_html_e( 'Cart', 'star-electric-child' ); ?></a></li>
				<li class="sep" aria-hidden="true">/</li>
				<li aria-current="page"><?php esc_html_e( 'Checkout', 'star-electric-child' ); ?></li>
			</ol>
		</nav>
		<h1 class="page-head__title"><?php esc_html_e( 'Checkout', 'star-electric-child' ); ?></h1>
		<?php Star_Electric_Shell::steps( 'checkout' ); ?>
	</div>
</div>

<section class="section section--sm">
	<div class="container checkout-layout">
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
