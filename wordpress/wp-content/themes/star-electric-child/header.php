<?php
/**
 * The document head and the global header.
 *
 * The header markup itself lives in Star_Electric_Chrome in the Core plugin, so
 * that Elementor's Theme Builder and this template render exactly the same
 * thing. If a Header template exists in Theme Builder, Elementor renders it and
 * this template steps aside; if none does, the renderer is called directly and
 * the site still has its header.
 *
 * The document itself - doctype, <head>, wp_head(), the body tag - stays here,
 * because that is not part of a header template and Elementor does not output
 * it.
 *
 * The markup is a direct port of SEE_UI.renderHeader() and renderDrawer() from
 * the approved storefront's js/components.js. The class names are deliberately
 * identical: the stylesheets this theme ships are that storefront's own files,
 * so changing a class here would break the approved design rather than migrate
 * it.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to main content', 'star-electric-child' ); ?></a>

<?php
$star_from_elementor = function_exists( 'elementor_theme_do_location' )
	&& elementor_theme_do_location( 'header' );

if ( ! $star_from_elementor && class_exists( 'Star_Electric_Chrome' ) ) {
	Star_Electric_Chrome::site_header();
}
?>

<main id="main">
