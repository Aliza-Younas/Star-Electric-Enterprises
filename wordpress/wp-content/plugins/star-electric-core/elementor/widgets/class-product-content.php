<?php
/**
 * Product Content.
 *
 * The whole job of the Single Product shell: draw the product's own Elementor
 * document.
 *
 * Every product owns its own document, so what a product page looks like is
 * that product's business - editing one cannot touch another. This widget only
 * asks the product being viewed for its layout and prints it.
 *
 * A product that has not been given a document yet - a brand-new import, or a
 * seeding run that has not reached it - falls back to the approved default
 * layout rather than showing nothing. That is why this is a widget of ours
 * rather than Elementor's Post Content: Post Content would print the product's
 * raw description instead.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product content widget.
 */
class Star_Electric_Widget_Product_Content extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-product-content';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Product Content', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-post-content';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Product content', 'star-electric' ) )
		);

		$this->note(
			__( 'This draws the product\'s own Elementor layout. To change one product\'s page, edit that product: Products, choose it, Edit with Elementor. Nothing typed here would apply to a single product - it would apply to all of them.', 'star-electric' )
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		Star_Electric_Sections::product_content();
	}
}
