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
		/*
		 * Star_Electric_Shell::quote_url() is the single source of truth for
		 * this link, and it is the only builder that gets the query string
		 * right: the form prefills from quote_product, and ?product= is
		 * WooCommerce's own post-type query var, which answers 404 before any
		 * template runs. Rolling a second URL here is what produced a dead
		 * button on every quote-only product card.
		 */
		if ( class_exists( 'Star_Electric_Shell' ) ) {
			return Star_Electric_Shell::quote_url( $product->get_id() );
		}

		$page = get_page_by_path( 'quote-request' );
		$base = $page ? get_permalink( $page ) : home_url( '/quote-request/' );

		return add_query_arg( 'quote_product', $product->get_id(), $base );
	}

	/**
	 * The approved storefront's price block.
	 *
	 * WooCommerce renders a reduced price as <del>old</del> <ins>new</ins>: the
	 * price the shopper is no longer paying comes first, and the saving is never
	 * stated. The approved design puts the current price first, the previous
	 * price after it and "Save 46%" beside them, and that ordering is the whole
	 * point of a deals page - so the block is built here rather than taken from
	 * get_price_html().
	 *
	 * A quote-only product returns the quotation wording. It can never fall
	 * through to a number, which is how such a product would otherwise end up
	 * priced at zero.
	 *
	 * @param WC_Product $product Product.
	 * @param string     $variant card | lg | mini.
	 */
	public static function price_block( WC_Product $product, string $variant = 'card' ): string {
		$mini = 'mini' === $variant;

		if ( self::is_quote( $product ) ) {
			return $mini
				? '<span class="mcard__quote"><b>' . esc_html__( 'Request a Quote', 'star-electric' ) . '</b><span>' .
					esc_html__( 'Priced on enquiry', 'star-electric' ) . '</span></span>'
				: '<span class="price__quote">' . esc_html__( 'Request a Quote', 'star-electric' ) . '</span>';
		}

		/*
		 * A variable product has a range rather than a price. The approved card
		 * shows the lowest with a "from" marker, which is what its own source
		 * publishes for those lines.
		 */
		$from = false;
		if ( $product->is_type( 'variable' ) ) {
			$prices  = $product->get_variation_prices( true );
			$values  = isset( $prices['price'] ) ? array_map( 'floatval', (array) $prices['price'] ) : array();
			$values  = array_filter( $values );
			$current = $values ? min( $values ) : (float) $product->get_price();
			$regular = $current;
			$from    = count( array_unique( $values ) ) > 1;
		} else {
			$current = (float) $product->get_price();
			$regular = (float) $product->get_regular_price();
		}

		if ( $current <= 0 ) {
			// No published price and not flagged quote-only: say nothing rather
			// than print a zero.
			return '';
		}

		$off = ( $regular > 0 && $current < $regular ) ? (int) round( ( 1 - ( $current / $regular ) ) * 100 ) : 0;

		if ( $mini ) {
			$html = '<span class="mcard__now">' . wp_kses_post( wc_price( $current ) ) . '</span>';
			if ( $off > 0 ) {
				$html .= '<span class="mcard__was">' . wp_kses_post( wc_price( $regular ) ) . '</span>';
			}
			return $html;
		}

		$html = '<span class="price__now">' . wp_kses_post( wc_price( $current ) ) . '</span>';
		if ( $off > 0 ) {
			$html .= '<span class="price__old">' . wp_kses_post( wc_price( $regular ) ) . '</span>';
			$html .= '<span class="price__off">' . sprintf(
				/* translators: %d: discount percentage */
				esc_html__( 'Save %d%%', 'star-electric' ),
				$off
			) . '</span>';
		}
		if ( $from ) {
			$html .= '<span class="price__from">' . esc_html__( 'from', 'star-electric' ) . '</span>';
		}

		return $html;
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
