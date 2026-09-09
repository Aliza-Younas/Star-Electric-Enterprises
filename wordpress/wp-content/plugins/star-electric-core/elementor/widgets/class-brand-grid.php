<?php
/**
 * Home — Shop by Brand.
 *
 * The brand tiles come from the star_brand taxonomy, with their real product
 * counts, in the catalogue's own order. Brands are not typed into Elementor:
 * doing so would let the page claim a brand the catalogue does not carry.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Brand grid widget.
 */
class Star_Electric_Widget_Brand_Grid extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-brand-grid';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Home — Shop by Brand', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-gallery-grid';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Brand section', 'star-electric' ) )
		);

		$this->note(
			__( 'The brands and their counts come from the catalogue. Brand tiles are monograms rather than logos, because no dealership or distribution relationship is on record.', 'star-electric' )
		);

		$this->add_control(
			'eyebrow',
			array(
				'label' => __( 'Small label', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Shop by Brand', 'star-electric' ),
			)
		);
		$this->add_control(
			'sub',
			array(
				'label' => __( 'Supporting text', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 2,
			)
		);
		$this->add_control(
			'link_label',
			array(
				'label'   => __( 'Link text', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'All brands', 'star-electric' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		Star_Electric_Sections::brand_grid(
			array(
				'eyebrow'    => (string) ( $s['eyebrow'] ?? '' ),
				'title'      => (string) ( $s['title'] ?? '' ),
				'sub'        => (string) ( $s['sub'] ?? '' ),
				'link_label' => (string) ( $s['link_label'] ?? '' ),
			)
		);
	}
}
