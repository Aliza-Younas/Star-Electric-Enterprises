<?php
/**
 * Product Tabs.
 *
 * The description, specification, notes and reviews tabs.
 *
 * The reviews tab says there are none rather than inventing a rating, and the
 * specification and notes tabs are only offered where the product record
 * carries them. Please do not write a review or a rating into any of them.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Tabs widget.
 */
class Star_Electric_Widget_Product_Tabs extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-product-tabs';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Product Tabs', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-product-tabs';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_tabs',
			array( 'label' => __( 'Tab labels', 'star-electric' ) )
		);

		$this->note(
			__( 'A tab appears only where the product record has something to put in it.', 'star-electric' )
		);

		$this->text( 'tab_description', __( 'Description', 'star-electric' ), __( 'Description', 'star-electric' ) );
		$this->text( 'tab_specs', __( 'Specifications', 'star-electric' ), __( 'Specifications', 'star-electric' ) );
		$this->text( 'tab_notes', __( 'Additional information', 'star-electric' ), __( 'Additional Information', 'star-electric' ) );
		$this->text( 'tab_reviews', __( 'Reviews', 'star-electric' ), __( 'Reviews', 'star-electric' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'star_copy',
			array( 'label' => __( 'Wording', 'star-electric' ) )
		);

		$this->note(
			__( 'No review, rating or review count may be written here. None has been published.', 'star-electric' )
		);

		$this->text( 'key_points', __( 'Feature list heading', 'star-electric' ), __( 'Key points', 'star-electric' ) );
		$this->area( 'no_description', __( 'When there is no description', 'star-electric' ), __( 'This product’s source does not publish a description.', 'star-electric' ), 3 );
		$this->text( 'reviews_title', __( 'Reviews heading', 'star-electric' ), __( 'No reviews yet', 'star-electric' ) );
		$this->area( 'reviews_text', __( 'Reviews text', 'star-electric' ), __( 'No customer reviews have been published for this product.', 'star-electric' ), 3 );

		$this->end_controls_section();

	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s    = $this->get_settings_for_display();
		$args = array();
		foreach ( array( 'tab_description', 'tab_specs', 'tab_notes', 'tab_reviews', 'key_points', 'no_description', 'reviews_title', 'reviews_text' ) as $key ) {
			$args[ $key ] = (string) ( $s[ $key ] ?? '' );
		}

		Star_Electric_Sections::product_tabs( $args );
	}
}
