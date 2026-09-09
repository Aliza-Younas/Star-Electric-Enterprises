<?php
/**
 * Returns & Replacements. A direct port of returns.html.
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
		__( 'Home', 'star-electric-child' )                     => home_url( '/' ),
		__( 'Returns & Replacements', 'star-electric-child' )   => '',
	),
	__( 'Returns & Replacements', 'star-electric-child' ),
	__( 'How returns, replacements and refunds are handled.', 'star-electric-child' )
);

Star_Electric_Shell::policy_panel(
	__( 'Return terms are not published yet', 'star-electric-child' ),
	__( 'Star Electric Enterprises has not published a returns, replacement or warranty policy yet. Rather than show terms that have not been agreed, this page states plainly that they are not available.', 'star-electric-child' ),
	__( 'If something you bought is faulty, damaged or not what you ordered, contact the store and it will tell you how it can be put right.', 'star-electric-child' )
);

get_footer();
