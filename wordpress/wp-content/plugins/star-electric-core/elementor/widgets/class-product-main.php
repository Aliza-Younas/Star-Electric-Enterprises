<?php
/**
 * Product Detail.
 *
 * The gallery and the buying column.
 *
 * Three things are decided by the catalogue and are not reachable from this
 * panel. A product whose source published no price never gets a cart - it
 * gets a quotation route, and no price appears anywhere on it. There is no
 * delivery, returns or warranty row, because the store has not supplied
 * those terms and a public product page must not imply a policy that is not
 * on record. And a product with no photograph says so rather than showing a
 * stand-in.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Detail widget.
 */
class Star_Electric_Widget_Product_Main extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-product-main';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Product Detail', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-single-product';
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
			__( 'A product with no photograph says so. Please do not put a stand-in picture in its place.', 'star-electric' )
		);

		$this->text( 'quote_tag', __( 'Quote badge', 'star-electric' ), __( 'Request Quote', 'star-electric' ) );
		$this->area( 'no_image', __( 'When there is no photograph', 'star-electric' ), __( 'Product image unavailable — the approved source for this product does not publish a photograph.', 'star-electric' ), 3 );

		$this->end_controls_section();

		$this->start_controls_section(
			'star_details',
			array( 'label' => __( 'Details', 'star-electric' ) )
		);

		$this->note(
			__( 'The brand, the department, the price and the availability all come from the catalogue.', 'star-electric' )
		);

		$this->text( 'in_label', __( '"in" before the department', 'star-electric' ), __( 'in', 'star-electric' ) );
		$this->text( 'no_ratings', __( 'Ratings line', 'star-electric' ), __( 'No customer ratings published', 'star-electric' ) );
		$this->text( 'sku_label', __( 'SKU label', 'star-electric' ), __( 'SKU:', 'star-electric' ) );
		$this->text( 'sku_missing', __( 'When there is no SKU', 'star-electric' ), __( 'Not specified', 'star-electric' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'star_buy',
			array( 'label' => __( 'Buying', 'star-electric' ) )
		);

		$this->note(
			__( 'Whether a product can be bought at all is the catalogue\'s decision, not this panel\'s.', 'star-electric' )
		);

		$this->text( 'quote_button', __( 'Quote button', 'star-electric' ), __( 'Request a Quote', 'star-electric' ) );
		$this->area( 'quote_hint', __( 'Line under the quote button', 'star-electric' ), __( 'This product’s source does not publish a price, so it is quoted on enquiry.', 'star-electric' ), 3 );
		$this->text( 'qty_label', __( 'Quantity label', 'star-electric' ), __( 'Quantity', 'star-electric' ) );
		$this->text( 'add_label', __( 'Add to cart button', 'star-electric' ), __( 'Add to Cart', 'star-electric' ) );
		$this->text( 'buy_label', __( 'Buy now button', 'star-electric' ), __( 'Buy Now', 'star-electric' ) );
		$this->text( 'oos_label', __( 'Out of stock button', 'star-electric' ), __( 'Out of Stock', 'star-electric' ) );
		$this->text( 'ask_label', __( 'Ask about this product', 'star-electric' ), __( 'Ask about this product', 'star-electric' ) );
		$this->text( 'wish_label', __( 'Wishlist button', 'star-electric' ), __( 'Add to Wishlist', 'star-electric' ) );
		$this->text( 'bulk_label', __( 'Bulk quote button', 'star-electric' ), __( 'Request Bulk Quote', 'star-electric' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'star_meta',
			array( 'label' => __( 'Record', 'star-electric' ) )
		);

		$this->note(
			__( 'Where the product record came from. The values are the importer\'s.', 'star-electric' )
		);

		$this->text( 'model_label', __( 'Model', 'star-electric' ), __( 'Model', 'star-electric' ) );
		$this->text( 'source_label', __( 'Source', 'star-electric' ), __( 'Source', 'star-electric' ) );
		$this->text( 'checked_label', __( 'Reference checked', 'star-electric' ), __( 'Reference checked', 'star-electric' ) );
		$this->text( 'series_label', __( 'Series', 'star-electric' ), __( 'Series', 'star-electric' ) );
		$this->text( 'cat_label', __( 'Category', 'star-electric' ), __( 'Category', 'star-electric' ) );

		$this->end_controls_section();

	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s    = $this->get_settings_for_display();
		$args = array();
		foreach ( array( 'quote_tag', 'no_image', 'in_label', 'no_ratings', 'sku_label', 'sku_missing', 'quote_button', 'quote_hint', 'qty_label', 'add_label', 'buy_label', 'oos_label', 'ask_label', 'wish_label', 'bulk_label', 'model_label', 'source_label', 'checked_label', 'series_label', 'cat_label' ) as $key ) {
			$args[ $key ] = (string) ( $s[ $key ] ?? '' );
		}

		Star_Electric_Sections::product_main( $args );
	}
}
