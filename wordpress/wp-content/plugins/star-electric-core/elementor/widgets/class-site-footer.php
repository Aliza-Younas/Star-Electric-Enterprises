<?php
/**
 * Site Footer.
 *
 * The company block, the three link columns, the contact column and the legal
 * bar. Every column's heading and every link is a control; the Shop column
 * leads with departments read from the catalogue so it cannot list one that no
 * longer exists.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Site footer widget.
 */
class Star_Electric_Widget_Site_Footer extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-site-footer';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Site Footer', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-footer';
	}

	/**
	 * A link repeater, pre-filled from the approved footer.
	 *
	 * @param string $key   Control name.
	 * @param string $label Panel label.
	 * @param array  $rows  Default links.
	 */
	private function link_group( string $key, string $label, array $rows ): void {
		$link = new \Elementor\Repeater();
		$link->add_control(
			'label',
			array( 'label' => __( 'Label', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$link->add_control(
			'url',
			array( 'label' => __( 'Link', 'star-electric' ), 'type' => \Elementor\Controls_Manager::URL )
		);

		$defaults = array();
		foreach ( $rows as $row ) {
			$defaults[] = array( 'label' => $row['label'], 'url' => array( 'url' => $row['url'] ) );
		}

		$this->add_control(
			$key,
			array(
				'label'       => $label,
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $link->get_controls(),
				'default'     => $defaults,
				'title_field' => '{{{ label }}}',
			)
		);
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$links = Star_Electric_Chrome::default_footer_links();

		$this->start_controls_section(
			'star_company',
			array( 'label' => __( 'Company block', 'star-electric' ) )
		);
		$this->note( __( 'The logo comes from the theme and the store name from Settings → General.', 'star-electric' ) );
		$this->add_control(
			'about',
			array(
				'label'   => __( 'About text', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'rows'    => 6,
				'default' => __( 'Star Electric Enterprises is an electrical products store based in Saddar, Rawalpindi, supplying wiring, protection, lighting, fans and power equipment to homes, offices, commercial projects, electricians and contractors.', 'star-electric' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'star_shop',
			array( 'label' => __( 'Shop column', 'star-electric' ) )
		);
		$this->add_control(
			'shop_title',
			array(
				'label'   => __( 'Heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Shop', 'star-electric' ),
			)
		);
		$this->add_control(
			'shop_depts',
			array(
				'label'       => __( 'Departments listed first', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 12,
				'default'     => 6,
				'description' => __( 'Read from the catalogue, biggest first.', 'star-electric' ),
			)
		);
		$this->link_group( 'shop_links', __( 'Extra links', 'star-electric' ), $links['shop'] );
		$this->end_controls_section();

		$this->start_controls_section(
			'star_service',
			array( 'label' => __( 'Customer Service column', 'star-electric' ) )
		);
		$this->add_control(
			'service_title',
			array(
				'label'   => __( 'Heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Customer Service', 'star-electric' ),
			)
		);
		$this->link_group( 'service_links', __( 'Links', 'star-electric' ), $links['service'] );
		$this->end_controls_section();

		$this->start_controls_section(
			'star_business',
			array( 'label' => __( 'Business column', 'star-electric' ) )
		);
		$this->add_control(
			'business_title',
			array(
				'label'   => __( 'Heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Business', 'star-electric' ),
			)
		);
		$this->link_group( 'business_links', __( 'Links', 'star-electric' ), $links['business'] );
		$this->end_controls_section();

		$this->start_controls_section(
			'star_contact',
			array( 'label' => __( 'Contact column', 'star-electric' ) )
		);
		$this->note(
			__( 'No phone number, WhatsApp number or opening hours are shown, because none has been supplied. Ask for them to be added once they are on record.', 'star-electric' )
		);
		foreach ( array(
			'contact_title' => array( __( 'Heading', 'star-electric' ), __( 'Contact', 'star-electric' ) ),
			'contact_store' => array( __( 'Store row label', 'star-electric' ), __( 'Store', 'star-electric' ) ),
			'contact_area'  => array( __( 'Area row label', 'star-electric' ), __( 'Area', 'star-electric' ) ),
			'area'          => array( __( 'Area', 'star-electric' ), __( 'Saddar, Rawalpindi', 'star-electric' ) ),
			'contact_enq'   => array( __( 'Enquiries row label', 'star-electric' ), __( 'Enquiries', 'star-electric' ) ),
			'contact_enq_l' => array( __( 'Enquiries link text', 'star-electric' ), __( 'Contact form', 'star-electric' ) ),
			'contact_quo'   => array( __( 'Quotations row label', 'star-electric' ), __( 'Quotations', 'star-electric' ) ),
			'contact_quo_l' => array( __( 'Quotations link text', 'star-electric' ), __( 'Request a quotation', 'star-electric' ) ),
		) as $key => $pair ) {
			$this->add_control(
				$key,
				array( 'label' => $pair[0], 'type' => \Elementor\Controls_Manager::TEXT, 'default' => $pair[1] )
			);
		}
		$this->end_controls_section();

		$this->start_controls_section(
			'star_legal',
			array( 'label' => __( 'Legal bar', 'star-electric' ) )
		);
		$this->add_control(
			'copyright',
			array(
				'label'       => __( 'Copyright line', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Star Electric Enterprises, Saddar, Rawalpindi. All rights reserved.', 'star-electric' ),
				'description' => __( 'The year is added automatically.', 'star-electric' ),
			)
		);
		$this->link_group( 'legal_links', __( 'Legal links', 'star-electric' ), $links['legal'] );
		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();
		$defaults = Star_Electric_Chrome::default_footer_links();
		$args = array();

		foreach ( array(
			'shop_links'     => 'shop',
			'service_links'  => 'service',
			'business_links' => 'business',
			'legal_links'    => 'legal',
		) as $control => $group ) {
			$rows = $this->rows(
				(array) ( $s[ $control ] ?? array() ),
				function ( array $row ): array {
					return array(
						'label' => (string) ( $row['label'] ?? '' ),
						'url'   => $this->url( $row['url'] ?? array(), home_url( '/' ) ),
					);
				},
				'label'
			);
			$args[ $control ] = $rows ? $rows : $defaults[ $group ];
		}

		foreach ( array( 'about', 'shop_title', 'service_title', 'business_title', 'contact_title',
			'contact_store', 'contact_area', 'area', 'contact_enq', 'contact_enq_l',
			'contact_quo', 'contact_quo_l', 'copyright' ) as $key ) {
			$value = trim( (string) ( $s[ $key ] ?? '' ) );
			if ( '' !== $value ) {
				$args[ $key ] = $value;
			}
		}

		if ( isset( $s['shop_depts'] ) && '' !== $s['shop_depts'] ) {
			$args['shop_depts'] = max( 0, min( 12, (int) $s['shop_depts'] ) );
		}

		Star_Electric_Chrome::site_footer( $args );
	}
}
