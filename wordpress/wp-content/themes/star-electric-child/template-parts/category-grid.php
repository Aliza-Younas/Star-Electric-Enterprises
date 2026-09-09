<?php
/**
 * The department grid.
 *
 * SEE_UI.renderCategoryCard() in PHP. The approved storefront drops this list
 * into five different pages, so the markup lives in one place - the plugin's
 * Star_Electric_Sections, which is also what the Elementor Department Grid
 * widget renders. This part stays so the templates that still call
 * get_template_part() keep working.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'Star_Electric_Sections' ) ) {
	Star_Electric_Sections::category_grid();
}
