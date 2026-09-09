<?php
/**
 * Page Head.
 *
 * The breadcrumb, the page title and its supporting line - the band every
 * content page opens with. The breadcrumb is a repeater, so a page nested under
 * another can say so.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Page head widget.
 */
class Star_Electric_Widget_Page_Head extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-page-head';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Page Head', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-post-title';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Page head', 'star-electric' ) )
		);

		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Page title', 'star-electric' ),
			)
		);
		$this->add_control(
			'sub',
			array(
				'label' => __( 'Supporting line', 'star-electric' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 3,
			)
		);

		$crumb = new \Elementor\Repeater();
		$crumb->add_control(
			'label',
			array( 'label' => __( 'Label', 'star-electric' ), 'type' => \Elementor\Controls_Manager::TEXT )
		);
		$crumb->add_control(
			'url',
			array(
				'label'       => __( 'Link', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'description' => __( 'Leave the last one empty - it is the current page.', 'star-electric' ),
			)
		);

		$this->add_control(
			'crumbs',
			array(
				'label'       => __( 'Breadcrumb', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $crumb->get_controls(),
				'default'     => array(
					array( 'label' => __( 'Home', 'star-electric' ), 'url' => array( 'url' => home_url( '/' ) ) ),
					array( 'label' => __( 'Page title', 'star-electric' ), 'url' => array( 'url' => '' ) ),
				),
				'title_field' => '{{{ label }}}',
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

		$s      = $this->get_settings_for_display();
		$crumbs = array();
		foreach ( (array) ( $s['crumbs'] ?? array() ) as $row ) {
			$label = trim( (string) ( $row['label'] ?? '' ) );
			if ( '' !== $label ) {
				$crumbs[ $label ] = $this->url( $row['url'] ?? array() );
			}
		}

		Star_Electric_Shell::page_head(
			$crumbs,
			(string) ( $s['title'] ?? '' ),
			(string) ( $s['sub'] ?? '' )
		);
	}
}
