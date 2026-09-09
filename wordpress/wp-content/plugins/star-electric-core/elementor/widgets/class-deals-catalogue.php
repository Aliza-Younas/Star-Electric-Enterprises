<?php
/**
 * Deals Catalogue.
 *
 * The filter sidebar, toolbar, product grid and pager of the deals page.
 *
 * Which products appear is not editable and is not a decision anyone takes
 * here: a product is on this page only where its own source published both a
 * previous and a current price, and the importer recorded the difference. A
 * product priced on enquiry can never qualify, so nothing on this page can show
 * a saving the source did not publish, and no offer period or quantity limit is
 * implied anywhere.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Deals catalogue widget.
 */
class Star_Electric_Widget_Deals_Catalogue extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-deals-catalogue';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Deals Catalogue', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-product-related';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Catalogue', 'star-electric' ) )
		);

		$this->note(
			__( 'Which products appear here is decided by the catalogue, not by this panel: only a product whose own source published a previous and a current price can be on this page. Wording only below.', 'star-electric' )
		);

		$this->text( 'filters_button', __( 'Filters button', 'star-electric' ), __( 'Filters', 'star-electric' ) );
		$this->text( 'count_none', __( 'When nothing matches', 'star-electric' ), __( 'No offers found', 'star-electric' ) );
		$this->text( 'sort_first', __( 'First sort option', 'star-electric' ), __( 'Sort: Biggest Discount', 'star-electric' ) );
		$this->text( 'show_results', __( 'Drawer button', 'star-electric' ), __( 'Show results', 'star-electric' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'star_empty_section',
			array( 'label' => __( 'When no offer matches', 'star-electric' ) )
		);

		$this->text( 'empty_title', __( 'Heading', 'star-electric' ), __( 'No offers match those filters', 'star-electric' ) );
		$this->area( 'empty_text', __( 'Text', 'star-electric' ), __( 'Clear a filter to see the rest of the current offers.', 'star-electric' ), 3 );
		$this->text( 'empty_button', __( 'Button', 'star-electric' ), __( 'Clear all filters', 'star-electric' ) );

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
		$query = Star_Electric_Sections::deals_query( $paged );

		/*
		 * Filter selections arrive as cat[]=, brand[]= and so on, so the carried
		 * arguments have to survive being arrays; running the whole of $_GET
		 * through rawurlencode() would fatal on the first checkbox group a
		 * shopper ticks.
		 */
		$carry = array();
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		foreach ( wp_unslash( $_GET ) as $key => $value ) {
			$key = sanitize_key( (string) $key );
			if ( '' === $key || 'paged' === $key || 'page' === $key ) {
				continue;
			}
			$carry[ $key ] = is_array( $value )
				? array_map( 'sanitize_text_field', $value )
				: sanitize_text_field( (string) $value );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		/*
		 * "Featured" is dropped here and biggest discount leads, exactly as on
		 * the approved deals page: a list of reductions is ordered by the size
		 * of the reduction.
		 */
		$sorts = class_exists( 'Star_Electric_Filters' ) ? Star_Electric_Filters::sorts() : array();
		unset( $sorts['relevance'] );
		$sorts = array( 'discount' => (string) ( $s['sort_first'] ?? '' ) ) + $sorts;

		Star_Electric_Sections::catalogue(
			array(
				'query'          => $query,
				'prefix'         => 'd',
				'carry'          => array( 'view' ),
				'section_style'  => 'padding-top:0',
				'filters_label'  => __( 'Deal filters', 'star-electric-child' ),
				'filters_button' => (string) ( $s['filters_button'] ?? '' ),
				'count_none'     => (string) ( $s['count_none'] ?? '' ),
				'sort_label'     => __( 'Sort offers', 'star-electric-child' ),
				'sorts'          => $sorts,
				'sort_default'   => 'discount',
				'pager_label'    => __( 'Offer pages', 'star-electric-child' ),
				'prev'           => __( 'Previous', 'star-electric-child' ),
				'next'           => __( 'Next', 'star-electric-child' ),
				'pager_edges'    => false,
				'pager_args'     => array(
					'base'     => trailingslashit( (string) get_permalink() ) . '%_%',
					'format'   => 'page/%#%/',
					'add_args' => $carry,
				),
				'empty_icon'     => 'tag',
				'empty_title'    => (string) ( $s['empty_title'] ?? '' ),
				'empty_text'     => (string) ( $s['empty_text'] ?? '' ),
				'empty_button'   => (string) ( $s['empty_button'] ?? '' ),
				'empty_url'      => (string) get_permalink(),
				'show_results'   => (string) ( $s['show_results'] ?? '' ),
			)
		);
	}
}
