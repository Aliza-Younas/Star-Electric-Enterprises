<?php
/**
 * Product Gallery.
 *
 * The thumbnail rail, the main photograph and the badges over it.
 *
 * A product with no photograph says so rather than showing a stand-in, and
 * the reduction badge appears only where the product's own source published
 * both a previous and a current price.
 *
 * The product is whichever product the page is showing. Nothing here is given a
 * product id, a price, a title, a SKU, a stock state or a brand: those come
 * from WooCommerce and from Star Electric Core every time the page renders.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Gallery widget.
 */
class Star_Electric_Widget_Product_Gallery extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-product-gallery';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Product Gallery', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-product-images';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_gallery',
			array( 'label' => __( 'Gallery', 'star-electric' ) )
		);

		$this->note(
			__( 'The photographs are the product\'s own. A product without one says so; please do not put a stand-in in its place.', 'star-electric' )
		);

		$this->text( 'quote_tag', __( 'Quote badge', 'star-electric' ), __( 'Request Quote', 'star-electric' ) );
		$this->area( 'no_image', __( 'When there is no photograph', 'star-electric' ), __( 'Product image unavailable — the approved source for this product does not publish a photograph.', 'star-electric' ), 3 );

		$this->end_controls_section();

	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s    = $this->get_settings_for_display();
		$args = array();
		foreach ( array( 'quote_tag', 'no_image' ) as $key ) {
			$args[ $key ] = (string) ( $s[ $key ] ?? '' );
		}

		Star_Electric_Sections::product_gallery( $args );
	}
}
