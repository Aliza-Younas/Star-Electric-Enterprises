<?php
/**
 * Related Products.
 *
 * Other products from the same department.
 *
 * Which products those are is the catalogue's decision. The section removes
 * itself when there are none.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Related Products widget.
 */
class Star_Electric_Widget_Product_Related extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-product-related';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Related Products', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-products';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Section', 'star-electric' ) )
		);

		$this->note(
			__( 'The products come from the same department as the one being viewed.', 'star-electric' )
		);

		$this->text( 'title', __( 'Heading', 'star-electric' ), __( 'Related Products', 'star-electric' ) );
		$this->text( 'sub', __( 'Supporting line', 'star-electric' ), __( 'Other items from the same department.', 'star-electric' ) );
		$this->text( 'link_label', __( 'Link', 'star-electric' ), __( 'Shop all', 'star-electric' ) );

		$this->end_controls_section();

	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s    = $this->get_settings_for_display();
		$args = array();
		foreach ( array( 'title', 'sub', 'link_label' ) as $key ) {
			$args[ $key ] = (string) ( $s[ $key ] ?? '' );
		}
		$args['count'] = 4;

		Star_Electric_Sections::product_related( $args );
	}
}
