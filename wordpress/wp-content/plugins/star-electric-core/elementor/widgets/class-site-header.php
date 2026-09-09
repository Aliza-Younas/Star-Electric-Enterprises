<?php
/**
 * Site Header.
 *
 * The whole header: topbar, logo, search, account/wishlist/cart, the primary
 * navigation with its mega menu, and the mobile drawer.
 *
 * It is one widget because the approved stylesheet lays the header out as a
 * single grid - splitting the logo, search and actions into separate Elementor
 * widgets would put wrappers between .header__inner and its children and the
 * bar would fall apart. The wording, the links and the call to action are all
 * controls; the departments and the cart count come from WordPress.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Site header widget.
 */
class Star_Electric_Widget_Site_Header extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-site-header';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Site Header', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-header';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_topbar',
			array( 'label' => __( 'Top bar', 'star-electric' ) )
		);

		$this->note(
			__( 'The store name comes from Settings → General, and the logo from the theme. The departments in the menu and the search dropdown come from the catalogue.', 'star-electric' )
		);

		$this->add_control(
			'location',
			array(
				'label'   => __( 'Location', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Saddar, Rawalpindi', 'star-electric' ),
			)
		);

		$link = new \Elementor\Repeater();
		$link->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => Star_Electric_Widget_Trust_Strip::icon_options() + array(
					'mail'  => __( 'Mail', 'star-electric' ),
					'phone' => __( 'Phone', 'star-electric' ),
				),
				'default' => 'mail',
			)
		);
		$link->add_control(
			'label',
			array( 'label' => __( 'Label', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$link->add_control(
			'url',
			array( 'label' => __( 'Link', 'star-electric' ), 'type' => \Elementor\Controls_Manager::URL )
		);

		$defaults = array();
		foreach ( Star_Electric_Chrome::default_topbar_links() as $row ) {
			$defaults[] = array(
				'icon'  => $row['icon'],
				'label' => $row['label'],
				'url'   => array( 'url' => $row['url'] ),
			);
		}

		$this->add_control(
			'topbar_links',
			array(
				'label'       => __( 'Top bar links', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $link->get_controls(),
				'default'     => $defaults,
				'title_field' => '{{{ label }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'star_search',
			array( 'label' => __( 'Search', 'star-electric' ) )
		);

		$this->add_control(
			'search_all',
			array(
				'label'   => __( 'All-categories option', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'All Categories', 'star-electric' ),
			)
		);
		$this->add_control(
			'search_place',
			array(
				'label'   => __( 'Placeholder', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Search cables, switches, breakers, lighting, fans and more...', 'star-electric' ),
			)
		);
		$this->add_control(
			'search_button',
			array(
				'label'   => __( 'Button label', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Search', 'star-electric' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'star_actions',
			array( 'label' => __( 'Account, wishlist, cart', 'star-electric' ) )
		);

		foreach ( array(
			'account_small' => array( __( 'Account — small line', 'star-electric' ), __( 'Customer', 'star-electric' ) ),
			'account_label' => array( __( 'Account — label', 'star-electric' ), __( 'Account', 'star-electric' ) ),
			'wish_small'    => array( __( 'Wishlist — small line', 'star-electric' ), __( 'Saved', 'star-electric' ) ),
			'wish_label'    => array( __( 'Wishlist — label', 'star-electric' ), __( 'Wishlist', 'star-electric' ) ),
			'cart_small'    => array( __( 'Cart — small line', 'star-electric' ), __( 'Cart', 'star-electric' ) ),
		) as $key => $pair ) {
			$this->add_control(
				$key,
				array(
					'label'   => $pair[0],
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => $pair[1],
				)
			);
		}

		$this->add_control(
			'quote_label',
			array(
				'label'   => __( 'Button label', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Request a Quote', 'star-electric' ),
			)
		);
		$this->add_control(
			'quote_url',
			array(
				'label' => __( 'Button link', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::URL,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'star_menu',
			array( 'label' => __( 'Menu and drawer', 'star-electric' ) )
		);

		$this->add_control(
			'mega_note',
			array(
				'label'   => __( 'Mega menu note', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => __( 'Every product listed is an individual item published by one of our approved supplier sources.', 'star-electric' ),
			)
		);
		$this->add_control(
			'mega_link',
			array(
				'label'   => __( 'Mega menu link', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Browse the full shop', 'star-electric' ),
			)
		);
		$this->add_control(
			'drawer_title',
			array(
				'label'   => __( 'Mobile drawer title', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Menu', 'star-electric' ),
			)
		);
		$this->add_control(
			'drawer_cats',
			array(
				'label'   => __( 'Drawer — categories heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Shop by category', 'star-electric' ),
			)
		);
		$this->add_control(
			'drawer_help',
			array(
				'label'   => __( 'Drawer — service heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Customer service', 'star-electric' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		$links = $this->rows(
			(array) ( $s['topbar_links'] ?? array() ),
			function ( array $row ): array {
				return array(
					'icon'  => (string) ( $row['icon'] ?? 'mail' ),
					'label' => (string) ( $row['label'] ?? '' ),
					'url'   => $this->url( $row['url'] ?? array(), home_url( '/' ) ),
				);
			},
			'label'
		);

		$args = array( 'topbar_links' => $links ? $links : Star_Electric_Chrome::default_topbar_links() );
		foreach ( array( 'location', 'search_all', 'search_place', 'search_button', 'account_small',
			'account_label', 'wish_small', 'wish_label', 'cart_small', 'quote_label',
			'mega_note', 'mega_link', 'drawer_title', 'drawer_cats', 'drawer_help' ) as $key ) {
			$value = trim( (string) ( $s[ $key ] ?? '' ) );
			if ( '' !== $value ) {
				$args[ $key ] = $value;
			}
		}

		$quote = $this->url( $s['quote_url'] ?? array() );
		if ( '' !== $quote ) {
			$args['quote_url'] = $quote;
		}

		Star_Electric_Chrome::site_header( $args );
	}
}
