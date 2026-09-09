<?php
/**
 * Brand Families.
 *
 * The brands whose source publishes ranges rather than individual products.
 *
 * Ranges are shown so the catalogue can be navigated and enquired about. They
 * are not sellable products and they carry no price, and the supporting line
 * says so - please leave that sentence in place, because a range shown as if it
 * were a product would be a claim the catalogue cannot support.
 *
 * The section removes itself when every brand has products of its own.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Brand families widget.
 */
class Star_Electric_Widget_Brand_Families extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-brand-families';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Brand Families', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-folder-o';
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
			__( 'A range is not a product and has no price. The supporting line says so; please leave that in place.', 'star-electric' )
		);

		$this->text( 'title', __( 'Heading', 'star-electric' ), __( 'Listed at product-family level', 'star-electric' ) );
		$this->area(
			'sub',
			__( 'Supporting line', 'star-electric' ),
			__( 'For these brands the approved source does not publish individual product pages, so no individual products are listed. The ranges below are shown for navigation and enquiry only — they are not sellable products and carry no price.', 'star-electric' ),
			5
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		Star_Electric_Sections::brand_families(
			array(
				'title' => (string) ( $s['title'] ?? '' ),
				'sub'   => (string) ( $s['sub'] ?? '' ),
			)
		);
	}
}
