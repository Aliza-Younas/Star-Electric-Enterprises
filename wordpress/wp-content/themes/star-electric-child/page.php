<?php
/**
 * The default page template.
 *
 * Used by any page without a template of its own - including the WooCommerce
 * cart, checkout and account pages, whose content is a shortcode. It gives
 * them the approved page head and container so they sit inside the storefront
 * shell rather than on a bare Hello Elementor canvas.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$star_sub = has_excerpt() ? wp_strip_all_tags( get_the_excerpt() ) : '';

	Star_Electric_Shell::page_head(
		array(
			__( 'Home', 'star-electric-child' ) => home_url( '/' ),
			get_the_title()                     => '',
		),
		get_the_title(),
		$star_sub
	);
	?>

	<section class="section section--sm">
		<div class="container">
			<div class="prose">
				<?php the_content(); ?>
			</div>
		</div>
	</section>

	<?php
endwhile;

get_footer();
