<?php
/**
 * Plugin Name:       Star Electric Core
 * Plugin URI:        https://github.com/Aliza-Younas/Star-Electric-Enterprises
 * Description:       Business logic for the Star Electric Enterprises storefront: quote-only products, brand and department taxonomies, product ranges, source provenance, catalogue rails and the catalogue importer.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Star Electric Enterprises
 * License:           GPL-2.0-or-later
 * Text Domain:       star-electric
 *
 * Everything here is behaviour that must survive a theme switch, so none of it
 * belongs in functions.php. The child theme handles presentation only.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STAR_ELECTRIC_VERSION', '1.0.0' );
define( 'STAR_ELECTRIC_FILE', __FILE__ );
define( 'STAR_ELECTRIC_PATH', plugin_dir_path( __FILE__ ) );
define( 'STAR_ELECTRIC_URL', plugin_dir_url( __FILE__ ) );

/**
 * A product whose source never published a price carries this meta.
 *
 * The whole quote-only behaviour keys off it, so it is a constant rather than a
 * string repeated across the codebase.
 */
define( 'STAR_ELECTRIC_QUOTE_META', '_star_electric_quote_only' );

/** Canonical mapping back to the record this product was imported from. */
define( 'STAR_ELECTRIC_SOURCE_ID_META', '_star_electric_source_id' );

require_once STAR_ELECTRIC_PATH . 'includes/class-taxonomies.php';
require_once STAR_ELECTRIC_PATH . 'includes/class-ranges.php';
require_once STAR_ELECTRIC_PATH . 'includes/class-quote-only.php';
require_once STAR_ELECTRIC_PATH . 'includes/class-provenance.php';
require_once STAR_ELECTRIC_PATH . 'includes/class-search.php';
require_once STAR_ELECTRIC_PATH . 'includes/class-navigation.php';
require_once STAR_ELECTRIC_PATH . 'includes/class-forms.php';
require_once STAR_ELECTRIC_PATH . 'includes/class-privacy.php';
require_once STAR_ELECTRIC_PATH . 'includes/class-wishlist.php';
require_once STAR_ELECTRIC_PATH . 'includes/class-seo.php';
require_once STAR_ELECTRIC_PATH . 'includes/class-chrome.php';
require_once STAR_ELECTRIC_PATH . 'includes/class-sections.php';
require_once STAR_ELECTRIC_PATH . 'includes/class-product-layout.php';
require_once STAR_ELECTRIC_PATH . 'includes/class-shortcodes.php';
require_once STAR_ELECTRIC_PATH . 'includes/class-filters.php';
require_once STAR_ELECTRIC_PATH . 'includes/class-importer.php';

/*
 * Elementor owns presentation; this plugin owns the markup and the catalogue
 * behind it. The widgets render through Star_Electric_Sections, which is the
 * same code the child theme's templates call, so a page built in Elementor and
 * a page rendered by PHP cannot drift apart.
 */
require_once STAR_ELECTRIC_PATH . 'elementor/class-elementor.php';

if ( is_admin() ) {
	require_once STAR_ELECTRIC_PATH . 'includes/class-admin-import.php';
}

/**
 * Boot the plugin.
 *
 * Taxonomies and the range post type are registered unconditionally so the
 * content model exists even if WooCommerce is deactivated - deactivating a
 * plugin should never orphan the catalogue's brands and departments.
 */
function star_electric_boot(): void {
	Star_Electric_Taxonomies::init();
	Star_Electric_Ranges::init();
	Star_Electric_Provenance::init();
	Star_Electric_Search::init();
	Star_Electric_Shortcodes::init();
	Star_Electric_Forms::init();
	Star_Electric_Privacy::init();
	Star_Electric_Wishlist::init();
	Star_Electric_SEO::init();
	Star_Electric_Elementor::init();
	Star_Electric_Product_Layout::init();

	if ( class_exists( 'WooCommerce' ) ) {
		Star_Electric_Quote_Only::init();
		Star_Electric_Filters::init();
	}

	// The dashboard import runner. WP-CLI is the comfortable route, but it needs
	// a shell; this is the one that works with nothing but an admin login.
	if ( is_admin() ) {
		Star_Electric_Admin_Import::init();
	}
}
add_action( 'plugins_loaded', 'star_electric_boot' );

/**
 * Keep every catalogue picture rendering the same way on every page.
 *
 * WordPress decides whether an image gets decoding="async" from where it is
 * rendered: inside the main loop it does, outside it does not. That is exactly
 * the difference between a section drawn by a PHP template and the same section
 * drawn by an Elementor widget, so the same department card came out two ways
 * on two pages. A reader cannot see the attribute, but it puts the browser on a
 * different image-scaling path, and that showed up as a measurable pixel
 * difference between an approved page and its conversion. Dropping it
 * everywhere makes the two identical, which is the whole acceptance test.
 *
 * Both filters are needed: the optimiser's attributes are merged in after
 * wp_get_attachment_image_attributes has run, so removing it from one is not
 * enough on its own.
 *
 * @param array $attr Image attributes, or the loading optimiser's additions.
 * @return array
 */
function star_electric_image_attributes( $attr ): array {
	unset( $attr['decoding'] );
	return (array) $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'star_electric_image_attributes', 99 );
add_filter( 'wp_get_loading_optimization_attributes', 'star_electric_image_attributes', 99 );

/**
 * Keep WooCommerce's default product styles off the approved pages.
 *
 * Elementor's theme locations wrap their content in a div carrying
 * WooCommerce's own "product" class. That switches on every
 * `.woocommerce div.product ...` rule in WooCommerce's stylesheet - rules the
 * theme's own templates never matched, because they had no such ancestor. Two
 * of them changed the product page the moment it moved into Elementor: a 30px
 * margin under the add-to-cart form, and WooCommerce's green 1.25em price.
 * Others would have surfaced on product types not being looked at.
 *
 * Removing that one class restores the cascade the approved pages were designed
 * against, rather than patching each rule as it is noticed. It is taken off the
 * rendered wrapper rather than through a filter because Elementor adds it after
 * both get_post_class() and its own attribute filters have run - neither of
 * which could reach it.
 *
 * @param string $html The location's rendered HTML.
 * @return string
 */
function star_electric_strip_product_class( string $html ): string {
	return (string) preg_replace_callback(
		'/<div\s[^>]*class="([^"]*elementor-location-(?:single|archive)[^"]*)"/',
		static function ( array $m ): string {
			$classes = preg_split( '/\s+/', $m[1], -1, PREG_SPLIT_NO_EMPTY );
			$classes = array_values( array_diff( (array) $classes, array( 'product' ) ) );
			return str_replace( 'class="' . $m[1] . '"', 'class="' . implode( ' ', $classes ) . '"', $m[0] );
		},
		$html,
		1
	);
}

foreach ( array( 'single', 'archive' ) as $star_location ) {
	add_action(
		'elementor/theme/before_do_' . $star_location,
		static function () {
			ob_start();
		},
		0
	);
	add_action(
		'elementor/theme/after_do_' . $star_location,
		static function () {
			echo star_electric_strip_product_class( (string) ob_get_clean() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		},
		99
	);
}

/**
 * Flush rewrites once on activation so /brand/... and /range/... resolve.
 *
 * Registration happens on init, so it has to be run here before flushing.
 */
function star_electric_activate(): void {
	Star_Electric_Taxonomies::register();
	Star_Electric_Ranges::register();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'star_electric_activate' );

/**
 * Leave no stale rewrite rules behind.
 */
function star_electric_deactivate(): void {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'star_electric_deactivate' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once STAR_ELECTRIC_PATH . 'cli/class-import-command.php';
	WP_CLI::add_command( 'star-electric', 'Star_Electric_Import_Command' );
}
