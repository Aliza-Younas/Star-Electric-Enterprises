<?php
/**
 * The homepage.
 *
 * Every section is rendered by Star_Electric_Sections in the Core plugin, which
 * is also what the Elementor widgets call. That is deliberate: there is one
 * piece of markup per section, so this template and a homepage built in
 * Elementor produce the same page rather than two that drift apart.
 *
 * This template is the fallback. When an Elementor homepage is published and
 * verified, Elementor serves the page and this file is not reached; if the
 * Elementor page were ever removed, the site still has a homepage.
 *
 * Every product shown is queried live through the plugin, so nothing here is a
 * pasted card that will go stale the moment a price changes.
 *
 * The copy is the approved copy. It claims no discount, delivery term, warranty
 * or dealership, because none of those is on record - and the store band
 * deliberately carries no phone number or opening hours for the same reason.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( class_exists( 'Star_Electric_Sections' ) ) {
	Star_Electric_Sections::campaign_banner();
	Star_Electric_Sections::trust_strip();
	Star_Electric_Sections::department_strip();
	Star_Electric_Sections::product_rails();
	Star_Electric_Sections::deals();
	Star_Electric_Sections::brand_grid();
	Star_Electric_Sections::bulk_cta();
	Star_Electric_Sections::why_choose_us();
	Star_Electric_Sections::store_band();
}

get_footer();
