<?php
/**
 * Product Record.
 *
 * Where the product record came from, and the department it sits in.
 *
 * There is no delivery, returns or warranty row, because the store has not
 * supplied those terms and a public product page must not imply a policy
 * that is not on record. Please do not add one here.
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
 * Product Record widget.
 */
class Star_Electric_Widget_Product_Record extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-product-record';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Product Record', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-document-file';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_meta',
			array( 'label' => __( 'Labels', 'star-electric' ) )
		);

		$this->note(
			__( 'The values are the importer\'s record of where this product came from.', 'star-electric' )
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
		foreach ( array( 'model_label', 'source_label', 'checked_label', 'series_label', 'cat_label' ) as $key ) {
			$args[ $key ] = (string) ( $s[ $key ] ?? '' );
		}

		Star_Electric_Sections::product_record( $args );
	}
}
