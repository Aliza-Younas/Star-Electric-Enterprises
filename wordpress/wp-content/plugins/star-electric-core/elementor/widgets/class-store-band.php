<?php
/**
 * Home — Store Band.
 *
 * The closing band: who the store is, where it is, and three facts.
 *
 * The product count is read from the catalogue rather than typed, so it cannot
 * go stale. The street address, phone number, WhatsApp number and opening hours
 * are deliberately absent - none has been supplied, and there is no control
 * here to invent one on a public page.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Store band widget.
 */
class Star_Electric_Widget_Store_Band extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-store-band';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Home — Store Band', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-map-pin';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Content', 'star-electric' ) )
		);

		$this->note(
			__( 'The product count is read from the catalogue. The street address, phone number and opening hours are not shown because none has been supplied - ask for them to be added once they are.', 'star-electric' )
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Small label', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Visit us', 'star-electric' ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => __( 'Heading', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => get_bloginfo( 'name' ),
				'description' => __( 'Defaults to the site title.', 'star-electric' ),
			)
		);
		$this->add_control(
			'lead',
			array(
				'label'   => __( 'Location line', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Saddar, Rawalpindi', 'star-electric' ),
			)
		);
		$this->add_control(
			'text',
			array(
				'label' => __( 'Description', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 5,
			)
		);
		$this->add_control(
			'primary_label',
			array(
				'label'   => __( 'Primary button label', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Request a Quotation', 'star-electric' ),
			)
		);
		$this->add_control(
			'second_label',
			array(
				'label'   => __( 'Second button label', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Contact the Store', 'star-electric' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'star_facts_section',
			array( 'label' => __( 'Facts', 'star-electric' ) )
		);

		$this->add_control(
			'fact_products',
			array(
				'label'       => __( 'Product count caption', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'individual products listed from seven supplier sources', 'star-electric' ),
				'description' => __( 'The number itself comes from the catalogue.', 'star-electric' ),
			)
		);
		$this->add_control(
			'fact2_title',
			array(
				'label'   => __( 'Second fact', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Retail & bulk', 'star-electric' ),
			)
		);
		$this->add_control(
			'fact2_text',
			array(
				'label'   => __( 'Second fact caption', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'quotations prepared for project and contractor orders', 'star-electric' ),
			)
		);
		$this->add_control(
			'fact3_title',
			array(
				'label'   => __( 'Third fact', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Source-checked', 'star-electric' ),
			)
		);
		$this->add_control(
			'fact3_text',
			array(
				'label'   => __( 'Third fact caption', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'every price and specification taken from the supplier’s own catalogue', 'star-electric' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s   = $this->get_settings_for_display();
		$out = array();

		foreach ( array( 'eyebrow', 'title', 'lead', 'text', 'primary_label', 'second_label',
			'fact_products', 'fact2_title', 'fact2_text', 'fact3_title', 'fact3_text' ) as $key ) {
			$value = trim( (string) ( $s[ $key ] ?? '' ) );
			if ( '' !== $value ) {
				$out[ $key ] = $value;
			}
		}

		Star_Electric_Sections::store_band( $out );
	}
}
