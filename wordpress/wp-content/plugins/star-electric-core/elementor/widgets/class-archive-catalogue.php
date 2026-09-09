<?php
/**
 * Archive Catalogue.
 *
 * The filter sidebar, toolbar, product grid and pager of the shop and of every
 * department, category, brand and search archive.
 *
 * Which products appear, in what order, and how the filters count are the
 * catalogue's business and stay in the plugin's own classes. Quote-only
 * products keep their quotation route and never show a price. Only the wording
 * is here.
 *
 * A department whose source publishes ranges rather than individual products
 * also gets those ranges, below the grid: navigation and enquiry only, never
 * priced and never added to a cart.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Archive catalogue widget.
 */
class Star_Electric_Widget_Archive_Catalogue extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-archive-catalogue';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Archive Catalogue', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-products';
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
			__( 'The products, their order, their prices and the filter counts all come from the catalogue. Wording only here.', 'star-electric' )
		);

		$this->text( 'filters_button', __( 'Filters button', 'star-electric' ), __( 'Filters', 'star-electric' ) );
		$this->text( 'count_none', __( 'When nothing matches', 'star-electric' ), __( 'No products found', 'star-electric' ) );
		$this->text( 'show_results', __( 'Drawer button', 'star-electric' ), __( 'Show results', 'star-electric' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'star_empty_section',
			array( 'label' => __( 'When no product matches', 'star-electric' ) )
		);

		$this->text( 'empty_title', __( 'Heading', 'star-electric' ), __( 'No products match those filters', 'star-electric' ) );
		$this->area( 'empty_text', __( 'Text', 'star-electric' ), __( 'Try removing a filter or widening the price range to see more of the catalogue.', 'star-electric' ), 3 );
		$this->text( 'empty_button', __( 'Button', 'star-electric' ), __( 'Clear all filters', 'star-electric' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'star_ranges_section',
			array( 'label' => __( 'Ranges listed at family level', 'star-electric' ) )
		);

		$this->note(
			__( 'A range is not a product and carries no price. This block only appears where a department has them.', 'star-electric' )
		);

		$this->text( 'ranges_title', __( 'Heading', 'star-electric' ), __( 'Ranges listed at family level', 'star-electric' ) );
		$this->area(
			'ranges_text',
			__( 'Text', 'star-electric' ),
			__( 'For these ranges the approved source does not publish individual product pages, so they are not listed as products. Tell us the rating or size you need and we will quote against it.', 'star-electric' ),
			4
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		$args = array();
		foreach ( array( 'filters_button', 'count_none', 'show_results', 'empty_title', 'empty_text', 'empty_button', 'ranges_title', 'ranges_text' ) as $key ) {
			$args[ $key ] = (string) ( $s[ $key ] ?? '' );
		}

		Star_Electric_Sections::archive_catalogue( $args );
	}
}
