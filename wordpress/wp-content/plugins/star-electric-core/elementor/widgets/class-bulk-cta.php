<?php
/**
 * Bulk / Project call to action.
 *
 * The heading, the copy, both buttons and the numbered steps are all editable.
 * The steps are a repeater, so one can be reworded or a fourth added without
 * the numbering having to be maintained by hand.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bulk CTA widget.
 */
class Star_Electric_Widget_Bulk_Cta extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-bulk-cta';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Bulk / Project CTA', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-call-to-action';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Content', 'star-electric' ) )
		);

		$this->add_control(
			'title',
			array(
				'label'   => __( 'Heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Buying for a Project?', 'star-electric' ),
			)
		);
		$this->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => __( 'Bulk electrical requirements for contractors, electricians, builders and commercial projects. Send us your item list and we will prepare a written quotation.', 'star-electric' ),
			)
		);
		$this->add_control(
			'primary_label',
			array(
				'label'   => __( 'Primary button label', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Request Bulk Quote', 'star-electric' ),
			)
		);
		$this->add_control(
			'primary_url',
			array(
				'label' => __( 'Primary button link', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::URL,
			)
		);
		$this->add_control(
			'second_label',
			array(
				'label'       => __( 'Second button label', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Contact the Store', 'star-electric' ),
				'description' => __( 'Leave empty for a single button.', 'star-electric' ),
			)
		);
		$this->add_control(
			'second_url',
			array(
				'label'     => __( 'Second button link', 'star-electric' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'condition' => array( 'second_label!' => '' ),
			)
		);
		$this->add_control(
			'second_icon',
			array(
				'label'     => __( 'Icon on the second button', 'star-electric' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'mail',
				'options'   => array(
					'mail' => __( 'Envelope', 'star-electric' ),
					''     => __( 'None', 'star-electric' ),
				),
				'condition' => array( 'second_label!' => '' ),
			)
		);
		$this->add_control(
			'heading_id',
			array(
				'label'       => __( 'Heading id', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'bulkTitle',
				'description' => __( 'Screen readers use this to announce the section. Change it only if two of these sections appear on the same page.', 'star-electric' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'star_steps_section',
			array( 'label' => __( 'Steps', 'star-electric' ) )
		);

		$this->note( __( 'The 01, 02, 03 numbers are added automatically.', 'star-electric' ) );

		$step = new \Elementor\Repeater();
		$step->add_control(
			'title',
			array(
				'label' => __( 'Step', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$defaults = array();
		foreach ( Star_Electric_Sections::default_bulk_steps() as $text ) {
			$defaults[] = array( 'title' => $text );
		}

		$this->add_control(
			'steps',
			array(
				'label'       => __( 'Steps', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $step->get_controls(),
				'default'     => $defaults,
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		$steps = array();
		foreach ( (array) ( $s['steps'] ?? array() ) as $row ) {
			$text = trim( (string) ( $row['title'] ?? '' ) );
			if ( '' !== $text ) {
				$steps[] = $text;
			}
		}

		$quote   = class_exists( 'Star_Electric_Shell' ) ? Star_Electric_Shell::url( 'quote' ) : home_url( '/' );
		$contact = class_exists( 'Star_Electric_Shell' ) ? Star_Electric_Shell::url( 'contact' ) : home_url( '/' );

		Star_Electric_Sections::bulk_cta(
			array(
				'title'         => (string) ( $s['title'] ?? '' ),
				'text'          => (string) ( $s['text'] ?? '' ),
				'primary_label' => (string) ( $s['primary_label'] ?? '' ),
				'primary_url'   => $this->url( $s['primary_url'] ?? array(), $quote ),
				'second_label'  => (string) ( $s['second_label'] ?? '' ),
				'second_url'    => $this->url( $s['second_url'] ?? array(), $contact ),
				'second_icon'   => (string) ( $s['second_icon'] ?? 'mail' ),
				'heading_id'    => (string) ( $s['heading_id'] ?? 'bulkTitle' ) ?: 'bulkTitle',
				'steps'         => $steps ? $steps : Star_Electric_Sections::default_bulk_steps(),
			)
		);
	}
}
