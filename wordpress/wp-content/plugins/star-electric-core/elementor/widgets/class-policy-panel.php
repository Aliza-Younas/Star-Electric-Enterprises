<?php
/**
 * Policy Panel.
 *
 * The panel the Shipping, Returns, Privacy and Terms pages are built from: a
 * heading, a lead paragraph, a detail paragraph and the two routes that do
 * work - contacting the store and asking for a quotation.
 *
 * These pages deliberately state what is *not* published rather than inventing
 * a delivery charge, a return window or a warranty term. Nothing here should be
 * edited into a commitment the store has not made.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Policy panel widget.
 */
class Star_Electric_Widget_Policy_Panel extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-policy-panel';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Policy Panel', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-document-file';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Panel', 'star-electric' ) )
		);

		$this->note(
			__( 'These pages say what the store has not published rather than stating terms it has not agreed. Please do not edit a delivery charge, return window or warranty into them until those terms are on record.', 'star-electric' )
		);

		$this->add_control(
			'heading',
			array(
				'label'   => __( 'Heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Terms are not published yet', 'star-electric' ),
			)
		);
		$this->add_control(
			'lead',
			array(
				'label' => __( 'Lead paragraph', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 5,
			)
		);
		$this->add_control(
			'detail',
			array(
				'label' => __( 'Second paragraph', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 4,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		if ( ! class_exists( 'Star_Electric_Shell' ) ) {
			return;
		}
		$s = $this->get_settings_for_display();
		Star_Electric_Shell::policy_panel(
			(string) ( $s['heading'] ?? '' ),
			(string) ( $s['lead'] ?? '' ),
			(string) ( $s['detail'] ?? '' )
		);
	}
}
