<?php
/**
 * The account page.
 *
 * A port of my-account.html. It exists as its own template rather than falling
 * through to page.php because page.php wraps content in .prose, which is a
 * 76ch reading column with red bullets on list items - right for an article,
 * wrong for a grid of service cards.
 *
 * The content itself is WooCommerce's [woocommerce_my_account]: a signed-out
 * visitor gets the approved services grid through
 * woocommerce/myaccount/form-login.php, and a signed-in one gets WooCommerce's
 * real dashboard.
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
		__( 'Home', 'star-electric-child' )             => home_url( '/' ),
		__( 'Customer Account', 'star-electric-child' ) => '',
	),
	__( 'Customer Account', 'star-electric-child' ),
	__( 'Manage shopping and service options.', 'star-electric-child' )
);
?>

<section class="section section--sm">
	<div class="container">
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
