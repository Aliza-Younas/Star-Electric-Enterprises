<?php
/**
 * Product Rail.
 *
 * One horizontal rail of products, queried live from WooCommerce. The editor
 * chooses which department or brand it shows and how many; the products, their
 * prices, their availability and the quote-only rule all come from the
 * catalogue at render time.
 *
 * No product is ever placed in Elementor by hand. A rail is a query, so a price
 * change or a new import is reflected the next time the page is served.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single product rail widget.
 */
class Star_Electric_Widget_Product_Rail extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-product-rail';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Product Rail', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-post-slider';
	}

	/**
	 * Every top-level department, for the picker.
	 *
	 * @return array<string,string>
	 */
	private function departments(): array {
		$out   = array();
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => 0,
				'hide_empty' => false,
			)
		);
		if ( is_wp_error( $terms ) ) {
			return $out;
		}
		if ( class_exists( 'Star_Electric_Taxonomies' ) ) {
			$terms = Star_Electric_Taxonomies::in_catalogue_order( (array) $terms );
		}
		foreach ( $terms as $term ) {
			if ( 'uncategorized' !== $term->slug ) {
				$out[ $term->slug ] = $term->name;
			}
		}
		return $out;
	}

	/**
	 * The cross-category departments, for the picker.
	 *
	 * @return array<string,string>
	 */
	private function cross_departments(): array {
		$out = array();
		if ( ! class_exists( 'Star_Electric_Taxonomies' ) ) {
			return $out;
		}
		$terms = get_terms(
			array(
				'taxonomy'   => Star_Electric_Taxonomies::DEPARTMENT,
				'hide_empty' => false,
			)
		);
		if ( is_wp_error( $terms ) ) {
			return $out;
		}
		foreach ( $terms as $term ) {
			$out[ $term->slug ] = $term->name;
		}
		return $out;
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Rail', 'star-electric' ) )
		);

		$this->note(
			__( 'The products are queried from WooCommerce every time the page loads. Nothing is copied into this widget, so prices and availability are always current.', 'star-electric' )
		);

		$this->add_control(
			'title',
			array(
				'label'   => __( 'Rail heading', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Wires & Cables', 'star-electric' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => __( 'Show products from', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'cat'  => __( 'A department', 'star-electric' ),
					'dept' => __( 'A cross-category department', 'star-electric' ),
				),
				'default' => 'cat',
			)
		);

		$departments = $this->departments();
		$this->add_control(
			'cat',
			array(
				'label'     => __( 'Department', 'star-electric' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => $departments,
				'default'   => (string) array_key_first( $departments ),
				'condition' => array( 'source' => 'cat' ),
			)
		);

		$cross = $this->cross_departments();
		$this->add_control(
			'dept',
			array(
				'label'     => __( 'Cross-category department', 'star-electric' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => $cross,
				'default'   => $cross ? (string) array_key_first( $cross ) : '',
				'condition' => array( 'source' => 'dept' ),
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'   => __( 'How many products', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 4,
				'max'     => 24,
				'default' => 10,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		$sel = 'dept' === (string) ( $s['source'] ?? 'cat' )
			? array( 'dept' => (string) ( $s['dept'] ?? '' ) )
			: array( 'cat' => (string) ( $s['cat'] ?? '' ) );

		if ( '' === reset( $sel ) ) {
			return;
		}

		Star_Electric_Sections::product_rail(
			array(
				'id'    => 'rail-' . sanitize_title( (string) ( $s['title'] ?? '' ) ),
				'title' => (string) ( $s['title'] ?? '' ),
				'sel'   => $sel,
				'limit' => max( 4, min( 24, (int) ( $s['limit'] ?? 10 ) ) ),
			)
		);
	}
}
