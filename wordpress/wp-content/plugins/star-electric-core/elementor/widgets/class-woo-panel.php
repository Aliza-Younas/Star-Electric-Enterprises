<?php
/**
 * WooCommerce Panel.
 *
 * The cart, the checkout or the account area, inside the approved page shell.
 *
 * What is inside it is WooCommerce's own and stays WooCommerce's: the cart
 * totals, the checkout fields, the order history and every calculation behind
 * them. This widget only says which of the three to show and which of the
 * approved layouts to wrap it in.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce panel widget.
 */
class Star_Electric_Widget_Woo_Panel extends Star_Electric_Widget_Base {

	/**
	 * Widget slug.
	 */
	public function get_name(): string {
		return 'star-woo-panel';
	}

	/**
	 * Panel and Navigator title.
	 */
	public function get_title(): string {
		return __( 'WooCommerce Panel', 'star-electric' );
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
			'star_section',
			array( 'label' => __( 'Panel', 'star-electric' ) )
		);

		$this->note(
			__( 'The cart, the checkout and the account area are WooCommerce\'s own. Nothing inside them is edited here, and nothing here touches an order or a total.', 'star-electric' )
		);

		$this->add_control(
			'shortcode',
			array(
				'label'   => __( 'Show', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'woocommerce_cart',
				'options' => array(
					'woocommerce_cart'       => __( 'Cart', 'star-electric' ),
					'woocommerce_checkout'   => __( 'Checkout', 'star-electric' ),
					'woocommerce_my_account' => __( 'Customer account', 'star-electric' ),
				),
			)
		);
		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'star-electric' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'cart-layout',
				'options' => array(
					'cart-layout'     => __( 'Cart: items beside the summary', 'star-electric' ),
					'checkout-layout' => __( 'Checkout: form beside the order', 'star-electric' ),
					''                => __( 'Full width', 'star-electric' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		Star_Electric_Sections::woo_panel(
			array(
				'shortcode' => (string) ( $s['shortcode'] ?? 'woocommerce_cart' ),
				'layout'    => (string) ( $s['layout'] ?? '' ),
			)
		);
	}
}
