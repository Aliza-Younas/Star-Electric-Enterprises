<?php
/**
 * Home — All Product Rails.
 *
 * The full stack of approved homepage rails, in the approved order, each one
 * queried live. It exists so the homepage can carry all ten with a single
 * widget; a page that wants them individually - to reorder them, or to put a
 * different section between two - uses the Product Rail widget instead.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * All product rails widget.
 */
class Star_Electric_Widget_Product_Rails extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-product-rails';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Home — All Product Rails', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-posts-carousel';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Product rails', 'star-electric' ) )
		);

		$rails = array();
		foreach ( Star_Electric_Navigation::rails() as $rail ) {
			$rails[] = (string) $rail['title'];
		}

		$this->note(
			sprintf(
				/* translators: %s: the rail headings, comma separated */
				__( 'Shows every approved rail in order: %s. Each is queried from WooCommerce. To change which rails appear, use separate Product Rail widgets instead of this one.', 'star-electric' ),
				implode( ', ', $rails )
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'   => __( 'Products per rail', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 4,
				'max'     => 24,
				'default' => 10,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		Star_Electric_Sections::product_rails(
			array( 'limit' => max( 4, min( 24, (int) ( $s['limit'] ?? 10 ) ) ) )
		);
	}
}
