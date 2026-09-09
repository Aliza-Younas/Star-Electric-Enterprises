<?php
/**
 * Text Band.
 *
 * A short band of copy with a row of buttons under it - the shape the Contact
 * page closes with.
 *
 * What that band leaves out is deliberate: there is no map, street address,
 * opening hours or collection process on it, because none of those is on
 * record. Please do not add one until the business supplies it.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Text band widget.
 */
class Star_Electric_Widget_Text_Band extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-text-band';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Text Band', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-text-area';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Band', 'star-electric' ) )
		);

		$this->note(
			__( 'No address, opening hours or phone number belongs here until the business supplies one. {site}, {shop}, {quote}, {contact}, {privacy} and {faq} are filled in for you.', 'star-electric' )
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
				'label' => __( 'Text', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 4,
			)
		);
		$this->add_control(
			'tint',
			array(
				'label'        => __( 'Tinted background', 'star-electric' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'heading_id',
			array(
				'label'       => __( 'Heading id', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'bandTitle',
				'description' => __( 'Screen readers use this to announce the section. Change it only if two of these appear on one page.', 'star-electric' ),
			)
		);

		$button = new \Elementor\Repeater();
		$button->add_control(
			'label',
			array( 'label' => __( 'Button label', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$button->add_control(
			'url',
			array( 'label' => __( 'Link', 'star-electric' ), 'type' => \Elementor\Controls_Manager::URL )
		);
		$button->add_control(
			'style',
			array(
				'label'   => __( 'Style', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'accent',
				'options' => array(
					'accent' => __( 'Primary (red)', 'star-electric' ),
					'ghost'  => __( 'Outline', 'star-electric' ),
				),
			)
		);

		$this->add_control(
			'buttons',
			array(
				'label'       => __( 'Buttons', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $button->get_controls(),
				'default'     => array(),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		Star_Electric_Sections::text_band(
			array(
				'eyebrow'    => (string) ( $s['eyebrow'] ?? '' ),
				'title'      => (string) ( $s['title'] ?? '' ),
				'sub'        => (string) ( $s['sub'] ?? '' ),
				'tint'       => 'yes' === (string) ( $s['tint'] ?? 'yes' ),
				'heading_id' => (string) ( $s['heading_id'] ?? '' ) ?: 'bandTitle',
				'buttons'    => $this->rows(
					(array) ( $s['buttons'] ?? array() ),
					function ( array $row ): array {
						return array(
							'label' => (string) ( $row['label'] ?? '' ),
							'url'   => $this->url( $row['url'] ?? array(), home_url( '/' ) ),
							'style' => (string) ( $row['style'] ?? 'accent' ),
						);
					},
					'label'
				),
			)
		);
	}
}
