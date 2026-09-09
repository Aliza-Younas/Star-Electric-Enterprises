<?php
/**
 * Wishlist.
 *
 * The saved-products table, its empty state, and the two messages that stand in
 * for it when something goes wrong.
 *
 * Saved products live in the visitor's own browser, not on this website, so
 * nothing about what anyone has saved is stored on the server. The table is
 * filled in by script from the ids the browser holds.
 *
 * The failure message and the empty state are deliberately two different
 * things: a lookup that fails must never be shown as an empty wishlist, because
 * a shopper would read that as their saved items having been thrown away.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wishlist widget.
 */
class Star_Electric_Widget_Wishlist extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-wishlist';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Wishlist', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-heart-o';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_table',
			array( 'label' => __( 'Table', 'star-electric' ) )
		);

		$this->note(
			__( 'Saved products are kept in the visitor\'s own browser. Nothing about what anyone saved is stored on this website.', 'star-electric' )
		);

		$this->text( 'col_product', __( 'Product column', 'star-electric' ), __( 'Product', 'star-electric' ) );
		$this->text( 'col_price', __( 'Price column', 'star-electric' ), __( 'Price', 'star-electric' ) );
		$this->text( 'col_stock', __( 'Stock column', 'star-electric' ), __( 'Stock', 'star-electric' ) );
		$this->text( 'col_actions', __( 'Actions column', 'star-electric' ), __( 'Actions', 'star-electric' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'star_empty',
			array( 'label' => __( 'When nothing is saved', 'star-electric' ) )
		);

		$this->text( 'empty_title', __( 'Heading', 'star-electric' ), __( 'Your wishlist is empty', 'star-electric' ) );
		$this->area( 'empty_text', __( 'Text', 'star-electric' ), __( 'Use the heart icon on any product to save it here for later. Saved items are handy when you are pricing up a job over several visits.', 'star-electric' ), 4 );
		$this->text( 'shop_label', __( 'First button', 'star-electric' ), __( 'Browse the Shop', 'star-electric' ) );
		$this->text( 'deals_label', __( 'Second button', 'star-electric' ), __( 'See Current Deals', 'star-electric' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'star_messages',
			array( 'label' => __( 'When something goes wrong', 'star-electric' ) )
		);

		$this->note(
			__( 'A lookup that fails must never read as an empty wishlist. Please keep these two messages saying different things.', 'star-electric' )
		);

		$this->area( 'error_text', __( 'Could not load', 'star-electric' ), __( 'Your saved products could not be loaded just now. They are still saved in this browser — reload the page to try again.', 'star-electric' ), 4 );
		$this->area( 'noscript', __( 'Without JavaScript', 'star-electric' ), __( 'The wishlist needs JavaScript, because saved products are stored in your browser rather than on this website.', 'star-electric' ), 4 );

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s    = $this->get_settings_for_display();
		$args = array();
		foreach ( array( 'col_product', 'col_price', 'col_stock', 'col_actions', 'empty_title', 'empty_text', 'shop_label', 'deals_label', 'error_text', 'noscript' ) as $key ) {
			$args[ $key ] = (string) ( $s[ $key ] ?? '' );
		}

		Star_Electric_Sections::wishlist( $args );
	}
}
