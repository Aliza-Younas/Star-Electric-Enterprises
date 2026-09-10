<?php
/**
 * Product Purchase / Quote.
 *
 * The variants, the buying controls and the row of onward links.
 *
 * Which of them appears is the catalogue's decision and cannot be set here:
 * a product whose source published no price gets a quotation route and no
 * cart, an out-of-stock product gets neither, and a variable product gets
 * WooCommerce's own variation form.
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
 * Product Purchase / Quote widget.
 */
class Star_Electric_Widget_Product_Purchase extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-product-purchase';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Product Purchase / Quote', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-cart-medium';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
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

		$this->end_controls_section();

		$this->start_controls_section(
			'star_links',
			array( 'label' => __( 'Onward links', 'star-electric' ) )
		);

		$this->text( 'ask_label', __( 'Ask about this product', 'star-electric' ), __( 'Ask about this product', 'star-electric' ) );
		$this->text( 'wish_label', __( 'Wishlist button', 'star-electric' ), __( 'Add to Wishlist', 'star-electric' ) );
		$this->text( 'bulk_label', __( 'Bulk quote button', 'star-electric' ), __( 'Request Bulk Quote', 'star-electric' ) );

		$this->end_controls_section();

	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s    = $this->get_settings_for_display();
		$args = array();
		foreach ( array( 'quote_button', 'quote_hint', 'qty_label', 'add_label', 'buy_label', 'oos_label', 'ask_label', 'wish_label', 'bulk_label' ) as $key ) {
			$args[ $key ] = (string) ( $s[ $key ] ?? '' );
		}

		Star_Electric_Sections::product_purchase( $args );
	}
}
