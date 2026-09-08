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
require_once STAR_ELECTRIC_PATH . 'includes/class-shortcodes.php';

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

	if ( class_exists( 'WooCommerce' ) ) {
		Star_Electric_Quote_Only::init();
	}
}
add_action( 'plugins_loaded', 'star_electric_boot' );

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
