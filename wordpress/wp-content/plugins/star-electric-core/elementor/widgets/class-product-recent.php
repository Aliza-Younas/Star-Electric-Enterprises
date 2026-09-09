<?php
/**
 * Recently Viewed.
 *
 * The visitor's own browsing history.
 *
 * The list lives in the visitor's browser for the length of their session,
 * exactly as the approved page keeps it, so nothing about who looked at what
 * is stored on the server. The section stays hidden until there is something
 * in it.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Recently Viewed widget.
 */
class Star_Electric_Widget_Product_Recent extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-product-recent';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Recently Viewed', 'star-electric' );
	}

	/**
	 * Panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-time-line';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'star_section',
			array( 'label' => __( 'Section', 'star-electric' ) )
		);

		$this->note(
			__( 'Nothing about a visitor\'s browsing is stored on the server; the list lives in their own browser.', 'star-electric' )
		);

		$this->text( 'title', __( 'Heading', 'star-electric' ), __( 'Recently Viewed', 'star-electric' ) );
		$this->text( 'sub', __( 'Supporting line', 'star-electric' ), __( 'Products you looked at in this browsing session.', 'star-electric' ) );

		$this->end_controls_section();

	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s    = $this->get_settings_for_display();
		$args = array();
		foreach ( array( 'title', 'sub' ) as $key ) {
			$args[ $key ] = (string) ( $s[ $key ] ?? '' );
		}

		Star_Electric_Sections::product_recent( $args );
	}
}
