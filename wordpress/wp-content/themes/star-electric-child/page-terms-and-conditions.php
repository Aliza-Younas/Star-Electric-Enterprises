<?php
/**
 * Terms & Conditions. A direct port of terms.html.
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
		__( 'Home', 'star-electric-child' )                => home_url( '/' ),
		__( 'Terms & Conditions', 'star-electric-child' )  => '',
	),
	__( 'Terms & Conditions', 'star-electric-child' ),
	__( 'The terms that apply when you use this website or buy from us.', 'star-electric-child' )
);

Star_Electric_Shell::policy_panel(
	__( 'Terms and conditions are not published yet', 'star-electric-child' ),
	__( 'Star Electric Enterprises has not published terms and conditions of sale yet. Rather than show terms that have not been agreed, this page states plainly that they are not available.', 'star-electric-child' ),
	__( 'Anything you buy is agreed directly with the store. Contact it with your requirements and it will confirm the terms that apply.', 'star-electric-child' )
);

get_footer();
