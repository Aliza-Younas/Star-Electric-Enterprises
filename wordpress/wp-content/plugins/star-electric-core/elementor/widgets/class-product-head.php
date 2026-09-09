<?php
/**
 * Product Breadcrumb.
 *
 * The trail above a product page. The department, the subcategory and the
 * product's own name come from the catalogue; only the first two labels are
 * wording.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Breadcrumb widget.
 */
class Star_Electric_Widget_Product_Head extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-product-head';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Product Breadcrumb', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-product-breadcrumbs';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_crumbs',
			array( 'label' => __( 'Breadcrumb', 'star-electric' ) )
		);

		$this->text( 'home', __( 'Home label', 'star-electric' ), __( 'Home', 'star-electric' ) );
		$this->text( 'shop', __( 'Shop label', 'star-electric' ), __( 'Shop', 'star-electric' ) );

		$this->end_controls_section();

	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s    = $this->get_settings_for_display();
		$args = array();
		foreach ( array( 'home', 'shop' ) as $key ) {
			$args[ $key ] = (string) ( $s[ $key ] ?? '' );
		}

		Star_Electric_Sections::product_head( $args );
	}
}
