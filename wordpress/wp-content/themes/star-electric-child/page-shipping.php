<?php
/**
 * Shipping & Delivery. A direct port of shipping.html.
 *
 * No delivery area, charge or timeframe appears here, and none is configured
 * in WooCommerce either: the store has supplied none, and a shipping rate a
 * shopper can see is a commitment.
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
		__( 'Home', 'star-electric-child' )                 => home_url( '/' ),
		__( 'Shipping & Delivery', 'star-electric-child' )  => '',
	),
	__( 'Shipping & Delivery', 'star-electric-child' ),
	__( 'How orders from Star Electric Enterprises are delivered or collected.', 'star-electric-child' )
);

Star_Electric_Shell::policy_panel(
	__( 'Delivery terms are not published yet', 'star-electric-child' ),
	__( 'Star Electric Enterprises has not published its delivery areas, charges or timeframes yet. Rather than show terms that have not been agreed, this page states plainly that they are not available.', 'star-electric-child' ),
	__( 'Ask the store about delivery or collection for the items you need and it will confirm what is possible before you order.', 'star-electric-child' )
);

get_footer();
