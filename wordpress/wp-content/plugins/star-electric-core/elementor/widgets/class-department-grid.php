<?php
/**
 * Department Grid.
 *
 * A headed section showing every department as a picture card. The departments,
 * their pictures and their product counts come from the catalogue, so this
 * panel only sets what the section is called and where its "more" link goes.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Department grid widget.
 */
class Star_Electric_Widget_Department_Grid extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-department-grid';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Department Grid', 'star-electric' );
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
			array( 'label' => __( 'Section', 'star-electric' ) )
		);

		$this->note(
			__( 'The cards themselves are built from the catalogue. Write {departments} in the supporting line to show how many there are.', 'star-electric' )
		);

		$this->add_control(
			'eyebrow',
			array( 'label' => __( 'Small label above', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$this->add_control(
			'title',
			array( 'label' => __( 'Heading', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$this->add_control(
			'sub',
			array(
				'label' => __( 'Supporting line', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 3,
			)
		);
		$this->add_control(
			'link_label',
			array(
				'label'       => __( 'Link label', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Leave empty to remove the link.', 'star-electric' ),
			)
		);
		$this->add_control(
			'link_url',
			array(
				'label'     => __( 'Link', 'star-electric' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'condition' => array( 'link_label!' => '' ),
			)
		);
		$this->add_control(
			'tint',
			array(
				'label'        => __( 'Tinted background', 'star-electric' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'compact',
			array(
				'label'        => __( 'Less space above and below', 'star-electric' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'heading_id',
			array(
				'label'       => __( 'Heading id', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'deptTitle',
				'condition'   => array( 'title!' => '' ),
				'description' => __( 'Screen readers use this to announce the section. Change it only if two of these appear on one page.', 'star-electric' ),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s    = $this->get_settings_for_display();
		$shop = class_exists( 'Star_Electric_Shell' ) ? Star_Electric_Shell::url( 'shop' ) : home_url( '/' );

		Star_Electric_Sections::department_grid(
			array(
				'eyebrow'    => (string) ( $s['eyebrow'] ?? '' ),
				'title'      => (string) ( $s['title'] ?? '' ),
				'sub'        => (string) ( $s['sub'] ?? '' ),
				'link_label' => (string) ( $s['link_label'] ?? '' ),
				'link_url'   => $this->url( $s['link_url'] ?? array(), $shop ),
				'section_class' => 'section'
					. ( 'yes' === (string) ( $s['tint'] ?? '' ) ? ' section--tint' : '' )
					. ( 'yes' === (string) ( $s['compact'] ?? '' ) ? ' section--sm' : '' ),
				'heading_id' => (string) ( $s['heading_id'] ?? '' ) ?: 'deptTitle',
			)
		);
	}
}
