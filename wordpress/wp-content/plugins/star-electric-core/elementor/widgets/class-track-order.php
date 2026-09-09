<?php
/**
 * Track Order.
 *
 * WooCommerce's own order-tracking form, the note under it, and the two cards
 * of onward links below.
 *
 * The lookup itself is WooCommerce's shortcode, so an order is found exactly as
 * WooCommerce means it to be. Nothing here touches an order.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Track order widget.
 */
class Star_Electric_Widget_Track_Order extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-track-order';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'Track Order', 'star-electric' );
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
			'star_section',
			array( 'label' => __( 'Panel', 'star-electric' ) )
		);

		$this->note(
			__( 'The lookup form is WooCommerce\'s own and cannot be edited here. Only the note under it can.', 'star-electric' )
		);

		$this->add_control(
			'hint',
			array(
				'label'       => __( 'Note under the form', 'star-electric' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'default'     => __( 'Can’t find your order number? It appears on your order confirmation. You can also [contact the store]({contact}) for help.', 'star-electric' ),
				'description' => __( 'Write a link as [the words](https://the-url). {contact} becomes the contact page.', 'star-electric' ),
			)
		);

		$this->end_controls_section();

		$this->sidebar_controls( 2 );
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		Star_Electric_Sections::track_order(
			array(
				'hint'  => (string) ( $s['hint'] ?? '' ),
				'cards' => $this->sidebar_cards( $s, 2 ),
			)
		);
	}
}
