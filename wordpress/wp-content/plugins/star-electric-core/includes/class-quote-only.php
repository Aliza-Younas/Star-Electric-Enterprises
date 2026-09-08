<?php
/**
 * Quote-only products.
 *
 * 2,548 of the 4,348 products in this catalogue have no published price. The
 * static site had a bug where one surface rendered such a product as "Rs. 0"
 * while the card beside it correctly said "Request a Quote"; the fix there was
 * a single source of truth, and the same discipline applies here.
 *
 * WooCommerce has no native concept of "priced on enquiry", so a quote-only
 * product is stored with NO price at all - not zero, not one. Every consequence
 * of that is handled below: what the price column shows, what the button says,
 * whether the product can be purchased, and whether it can reach the cart.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Quote-only product behaviour.
 */
class Star_Electric_Quote_Only {

	/**
	 * Hook everything up.
	 */
	public static function init(): void {
		add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'price_html' ), 10, 2 );
		add_filter( 'woocommerce_is_purchasable', array( __CLASS__, 'not_purchasable' ), 10, 2 );

		// Loop button: replace Add to Cart with the quotation link.
		add_filter( 'woocommerce_loop_add_to_cart_link', array( __CLASS__, 'loop_button' ), 10, 2 );

		// Single product: drop the cart form, put the quotation action in its place.
		add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'single_button' ), 30 );

		// Belt and braces: even if a button were somehow rendered, nothing
		// unpriceable may enter the cart.
		add_filter( 'woocommerce_add_to_cart_validation', array( __CLASS__, 'block_add_to_cart' ), 10, 2 );

		// Structured data must not advertise a price that does not exist.
		add_filter( 'woocommerce_structured_data_product_offer', array( __CLASS__, 'strip_offer' ), 10, 2 );

		add_filter( 'woocommerce_product_get_price', array( __CLASS__, 'empty_price' ), 10, 2 );
		add_filter( 'woocommerce_product_get_regular_price', array( __CLASS__, 'empty_price' ), 10, 2 );
	}

	/**
	 * Is this product priced on enquiry?
	 *
	 * The meta is authoritative, but a product that somehow has no price is
	 * treated as quote-only too - failing towards "ask us" is always safe,
	 * failing towards "free" is not.
	 *
	 * @param WC_Product|int|null $product Product or ID.
	 */
	public static function is_quote( $product ): bool {
		if ( is_numeric( $product ) ) {
			$product = wc_get_product( $product );
		}
		if ( ! $product instanceof WC_Product ) {
			return false;
		}
		if ( 'yes' === $product->get_meta( STAR_ELECTRIC_QUOTE_META ) ) {
			return true;
		}
		return '' === (string) $product->get_price( 'edit' );
	}

	/**
	 * The quotation URL for a product, carrying enough to prefill the form.
	 *
	 * @param WC_Product $product Product.
	 */
	public static function quote_url( WC_Product $product ): string {
		$page = get_page_by_path( 'request-a-quote' );
		$base = $page ? get_permalink( $page ) : home_url( '/request-a-quote/' );

		return add_query_arg(
			array(
				'product'      => $product->get_id(),
				'product_name' => rawurlencode( $product->get_name() ),
			),
			$base
		);
	}

	/**
	 * Show the quotation state instead of a figure.
	 *
	 * @param string     $html    Existing price HTML.
	 * @param WC_Product $product Product.
	 */
	public static function price_html( $html, $product ) {
		if ( ! self::is_quote( $product ) ) {
			return $html;
		}

		return '<span class="price price--quote"><span class="price__quote">'
			. esc_html__( 'Request a Quote', 'star-electric' )
			. '</span></span>';
	}

	/**
	 * A product with no price cannot be bought.
	 *
	 * @param bool       $purchasable Current state.
	 * @param WC_Product $product     Product.
	 */
	public static function not_purchasable( $purchasable, $product ) {
		return self::is_quote( $product ) ? false : $purchasable;
	}

	/**
	 * Never let a price filter invent a number for a quote-only product.
	 *
	 * @param string     $price   Price.
	 * @param WC_Product $product Product.
	 */
	public static function empty_price( $price, $product ) {
		if ( $product instanceof WC_Product
			&& 'yes' === $product->get_meta( STAR_ELECTRIC_QUOTE_META ) ) {
			return '';
		}
		return $price;
	}

	/**
	 * Catalogue button.
	 *
	 * @param string     $html    Existing button HTML.
	 * @param WC_Product $product Product.
	 */
	public static function loop_button( $html, $product ) {
		if ( ! self::is_quote( $product ) ) {
			return $html;
		}

		return sprintf(
			'<a href="%s" class="btn btn--accent btn--block pcard__add">%s</a>',
			esc_url( self::quote_url( $product ) ),
			esc_html__( 'Request a Quote', 'star-electric' )
		);
	}

	/**
	 * Single product action.
	 */
	public static function single_button(): void {
		global $product;

		if ( ! $product instanceof WC_Product || ! self::is_quote( $product ) ) {
			return;
		}

		printf(
			'<div class="pdp__quote"><a class="btn btn--accent btn--lg" href="%s">%s</a></div>',
			esc_url( self::quote_url( $product ) ),
			esc_html__( 'Request a Quote', 'star-electric' )
		);
	}

	/**
	 * Refuse the cart, whatever asked.
	 *
	 * @param bool $valid      Current validity.
	 * @param int  $product_id Product ID.
	 */
	public static function block_add_to_cart( $valid, $product_id ) {
		if ( ! self::is_quote( $product_id ) ) {
			return $valid;
		}

		wc_add_notice(
			esc_html__( 'This product is priced on enquiry. Please request a quotation for it.', 'star-electric' ),
			'notice'
		);

		return false;
	}

	/**
	 * Keep the offer out of structured data rather than publishing a zero price.
	 *
	 * @param array      $offer   Offer data.
	 * @param WC_Product $product Product.
	 */
	public static function strip_offer( $offer, $product ) {
		if ( ! self::is_quote( $product ) ) {
			return $offer;
		}

		unset( $offer['price'], $offer['priceSpecification'] );
		$offer['availability'] = 'https://schema.org/InStoreOnly';

		return $offer;
	}
}
