<?php
/**
 * Privacy Policy.
 *
 * A port of privacy.html, with one sentence necessarily rewritten. The static
 * page could say "the enquiry forms are not connected, so nothing you type
 * into them is transmitted or stored anywhere" because that was true of a
 * static site. On WordPress the quote, contact and complaint forms genuinely
 * send and are stored as enquiries, so that sentence would now be false. What
 * replaces it describes what actually happens and nothing more.
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
		__( 'Home', 'star-electric-child' )           => home_url( '/' ),
		__( 'Privacy Policy', 'star-electric-child' ) => '',
	),
	__( 'Privacy Policy', 'star-electric-child' ),
	__( 'How Star Electric Enterprises handles the information you provide.', 'star-electric-child' )
);

Star_Electric_Shell::policy_panel(
	__( 'A privacy policy is not published yet', 'star-electric-child' ),
	__( 'Star Electric Enterprises has not published a privacy policy yet. Rather than show terms that have not been agreed, this page states plainly that they are not available.', 'star-electric-child' ),
	__( 'This website does not ask you to create an account and takes no payment. The quote, contact and complaint forms send what you type to the store by email and keep a copy in the admin area of this website, so that an enquiry is not lost. Nothing else is collected.', 'star-electric-child' )
);

get_footer();
