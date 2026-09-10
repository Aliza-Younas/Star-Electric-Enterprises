<?php
/**
 * Product Main Info.
 *
 * The brand, the title, the SKU row, the price and the short description.
 *
 * The price block belongs to Star Electric Core: a product whose source
 * published no price shows a quotation route and never a number, and a
 * genuine reduction shows the previous price beside the current one.
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
 * Product Main Info widget.
 */
class Star_Electric_Widget_Product_Info extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-product-info';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Product Main Info', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-product-title';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_info',
			array( 'label' => __( 'Wording', 'star-electric' ) )
		);

		$this->note(
			__( 'The brand, title, SKU, price and availability are the product\'s own. Only the words around them are here.', 'star-electric' )
		);

		$this->text( 'in_label', __( '"in" before the department', 'star-electric' ), __( 'in', 'star-electric' ) );
		$this->text( 'no_ratings', __( 'Ratings line', 'star-electric' ), __( 'No customer ratings published', 'star-electric' ) );
		$this->text( 'sku_label', __( 'SKU label', 'star-electric' ), __( 'SKU:', 'star-electric' ) );
		$this->text( 'sku_missing', __( 'When there is no SKU', 'star-electric' ), __( 'Not specified', 'star-electric' ) );

		$this->end_controls_section();

	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s    = $this->get_settings_for_display();
		$args = array();
		foreach ( array( 'in_label', 'no_ratings', 'sku_label', 'sku_missing' ) as $key ) {
			$args[ $key ] = (string) ( $s[ $key ] ?? '' );
		}

		Star_Electric_Sections::product_info( $args );
	}
}
