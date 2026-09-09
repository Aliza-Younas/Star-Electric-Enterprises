<?php
/**
 * Archive Head.
 *
 * The top of a product archive: a breadcrumb and a page head for the shop, a
 * brand or a search; a department hero with its own photograph and a row of
 * subcategory tiles for a department or category.
 *
 * Which of the two is shown is the query's decision, not an editor's, because
 * that is how the approved storefront is built. The department name, its
 * description, its picture and its subcategories all come from the catalogue.
 * Only the wording around them is here.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Archive head widget.
 */
class Star_Electric_Widget_Archive_Head extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-archive-head';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Archive Head', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-archive-title';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_shop',
			array( 'label' => __( 'Shop, brand and search', 'star-electric' ) )
		);

		$this->note(
			__( 'A department or category archive shows the hero below instead. Which one appears is decided by the page being viewed.', 'star-electric' )
		);

		$this->text( 'crumb_home', __( 'Breadcrumb: home', 'star-electric' ), __( 'Home', 'star-electric' ) );
		$this->text( 'crumb_shop', __( 'Breadcrumb: shop', 'star-electric' ), __( 'Shop', 'star-electric' ) );
		$this->text( 'shop_title', __( 'Shop heading', 'star-electric' ), __( 'Shop All Products', 'star-electric' ) );
		$this->area(
			'shop_sub',
			__( 'Shop supporting line', 'star-electric' ),
			__( 'The complete Star Electric Enterprises catalogue. Filter by category, brand, price, availability or product type to narrow the range.', 'star-electric' ),
			4
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'star_department',
			array( 'label' => __( 'Department hero', 'star-electric' ) )
		);

		$this->note(
			__( 'The department\'s name, description, photograph and subcategories come from the catalogue.', 'star-electric' )
		);

		$this->text( 'eyebrow', __( 'Small label above', 'star-electric' ), __( 'Department', 'star-electric' ) );
		$this->text( 'quote_label', __( 'First button', 'star-electric' ), __( 'Request a Quote', 'star-electric' ) );
		$this->text( 'all_label', __( 'Second button', 'star-electric' ), __( 'All departments', 'star-electric' ) );
		$this->text( 'subcat_title', __( 'Subcategories heading', 'star-electric' ), __( 'Browse subcategories', 'star-electric' ) );

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		$args = array();
		foreach ( array( 'crumb_home', 'crumb_shop', 'shop_title', 'shop_sub', 'eyebrow', 'quote_label', 'all_label', 'subcat_title' ) as $key ) {
			$args[ $key ] = (string) ( $s[ $key ] ?? '' );
		}

		Star_Electric_Sections::archive_head( $args );
	}
}
