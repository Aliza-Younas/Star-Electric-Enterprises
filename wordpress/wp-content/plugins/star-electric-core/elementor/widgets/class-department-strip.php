<?php
/**
 * Home — Shop by Department.
 *
 * The department carousel. The departments themselves come from the product
 * category taxonomy, with their real counts and their real pictures, so the
 * strip cannot fall out of step with the catalogue. What the editor controls is
 * the heading and where "View All" goes.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Department strip widget.
 */
class Star_Electric_Widget_Department_Strip extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-department-strip';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Home — Shop by Department', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-carousel';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Department strip', 'star-electric' ) )
		);

		$this->note(
			__( 'The departments, their pictures and their product counts come from the catalogue. Adding or removing one is done in Products → Categories, not here.', 'star-electric' )
		);

		$this->add_control(
			'title',
			array(
				'label'   => __( 'Heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Shop by Department', 'star-electric' ),
			)
		);
		$this->add_control(
			'view_all',
			array(
				'label'   => __( 'Link text', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'View All Categories', 'star-electric' ),
			)
		);
		$this->add_control(
			'view_all_url',
			array(
				'label'       => __( 'Link', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => home_url( '/shop/' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		Star_Electric_Sections::department_strip(
			array(
				'title'        => (string) ( $s['title'] ?? '' ),
				'view_all'     => (string) ( $s['view_all'] ?? '' ),
				'view_all_url' => $this->url(
					$s['view_all_url'] ?? array(),
					function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' )
				),
			)
		);
	}
}
