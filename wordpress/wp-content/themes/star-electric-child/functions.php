<?php
/**
 * Star Electric Child theme.
 *
 * Presentation only. Every piece of business logic - quote-only products, the
 * brand and department taxonomies, ranges, provenance, the catalogue rails -
 * lives in the Star Electric Core plugin so it survives a theme change.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_stylesheet_directory() . '/inc/class-shell.php';

/**
 * Enqueue the approved design system.
 *
 * The three stylesheets are the same files the static storefront ships and they
 * must load in this order: tokens and components, then page layouts, then
 * breakpoints. Versions are file modification times so a deploy busts the cache
 * without anyone remembering to bump a number.
 */
function star_electric_child_assets(): void {
	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	/*
	 * Hello Elementor's own stylesheets are dropped, not layered under ours.
	 *
	 * Its reset.css is not a reset: it paints every bare <button> with a 1px
	 * #c36 border and #c36 text, inverts it to a solid #c36 block on hover, and
	 * styles tables, inputs and links to match. The approved storefront ships a
	 * complete design system - it runs the static site with no other CSS at all
	 * - so none of that is needed, and all of it has to be fought off rule by
	 * rule if it stays. The parent theme is still the parent for template
	 * inheritance; only its paint is removed.
	 */
	foreach ( array( 'hello-elementor', 'hello-elementor-theme-style', 'hello-elementor-header-footer' ) as $handle ) {
		wp_dequeue_style( $handle );
		wp_deregister_style( $handle );
	}

	/*
	 * The first three are the approved storefront's own stylesheets, in the
	 * order they load there. The fourth dresses the markup WooCommerce emits
	 * that the static site never had, and must come last so it can lean on the
	 * tokens the first one defines.
	 */
	foreach ( array( 'styles', 'pages', 'responsive', 'woocommerce' ) as $i => $handle ) {
		$path = $dir . '/assets/css/' . $handle . '.css';
		if ( ! file_exists( $path ) ) {
			continue;
		}
		wp_enqueue_style(
			'star-electric-' . $handle,
			$uri . '/assets/css/' . $handle . '.css',
			0 === $i ? array() : array( 'star-electric-styles' ),
			(string) filemtime( $path )
		);
	}

	$shell = $dir . '/assets/js/shell.js';
	if ( file_exists( $shell ) ) {
		wp_enqueue_script(
			'star-electric-shell',
			$uri . '/assets/js/shell.js',
			array(),
			(string) filemtime( $shell ),
			true
		);
	}

	/*
	 * The filter panel, the mobile filter drawer and the grid/list toggle. The
	 * deals page is not a WooCommerce archive but it wears the same toolbar, so
	 * it needs the same script - without this its filter drawer opens onto
	 * nothing.
	 */
	$is_archive = function_exists( 'is_shop' )
		&& ( is_shop() || is_product_taxonomy() || ( is_search() && 'product' === get_query_var( 'post_type' ) ) );
	$is_archive = $is_archive || is_page_template( 'page-deals.php' ) || is_page( 'deals' );

	$shop = $dir . '/assets/js/shop.js';
	if ( $is_archive && file_exists( $shop ) ) {
		wp_enqueue_script(
			'star-electric-shop',
			$uri . '/assets/js/shop.js',
			array(),
			(string) filemtime( $shop ),
			true
		);
	}

	/*
	 * The wishlist runs everywhere, because the heart control lives on the
	 * product card and the product card appears on nearly every page. It is
	 * also what keeps the header's saved-items badge truthful, and that badge
	 * is in the header on every page.
	 */
	$wish = $dir . '/assets/js/wishlist.js';
	if ( file_exists( $wish ) ) {
		wp_enqueue_script(
			'star-electric-wishlist',
			$uri . '/assets/js/wishlist.js',
			array(),
			(string) filemtime( $wish ),
			true
		);
		wp_localize_script(
			'star-electric-wishlist',
			'starWishlist',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => class_exists( 'Star_Electric_Wishlist' ) ? Star_Electric_Wishlist::ACTION : 'star_electric_wishlist',
			)
		);
	}

	$brands = $dir . '/assets/js/brands.js';
	if ( is_page( 'brands' ) && file_exists( $brands ) ) {
		wp_enqueue_script(
			'star-electric-brands',
			$uri . '/assets/js/brands.js',
			array(),
			(string) filemtime( $brands ),
			true
		);
	}

	/*
	 * Recently viewed, on the product page only - the one page that both adds
	 * to the visitor's history and shows it back to them.
	 */
	$recent = $dir . '/assets/js/recent.js';
	if ( function_exists( 'is_product' ) && is_product() && file_exists( $recent ) ) {
		wp_enqueue_script(
			'star-electric-recent',
			$uri . '/assets/js/recent.js',
			array(),
			(string) filemtime( $recent ),
			true
		);
		wp_localize_script(
			'star-electric-recent',
			'starRecent',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => class_exists( 'Star_Electric_Wishlist' ) ? Star_Electric_Wishlist::CARDS : 'star_electric_cards',
			)
		);
	}

	$home = $dir . '/assets/js/home.js';
	if ( is_front_page() && file_exists( $home ) ) {
		wp_enqueue_script(
			'star-electric-home',
			$uri . '/assets/js/home.js',
			array(),
			(string) filemtime( $home ),
			true
		);
	}

	$single = $dir . '/assets/js/product.js';
	if ( function_exists( 'is_product' ) && is_product() && file_exists( $single ) ) {
		wp_enqueue_script(
			'star-electric-product',
			$uri . '/assets/js/product.js',
			array(),
			(string) filemtime( $single ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'star_electric_child_assets', 20 );

/**
 * Render the rupee the way the approved storefront does.
 *
 * WooCommerce ships "₨" for PKR; every price on the approved site reads
 * "Rs. 1,234". With the symbol position set to left-with-space, this makes the
 * WordPress build match it exactly rather than approximately.
 *
 * @param string $symbol   Currency symbol.
 * @param string $currency Currency code.
 */
function star_electric_child_currency_symbol( string $symbol, string $currency ): string {
	return 'PKR' === $currency ? 'Rs.' : $symbol;
}
add_filter( 'woocommerce_currency_symbol', 'star_electric_child_currency_symbol', 10, 2 );

/**
 * The number of items in the cart, for the header badge.
 *
 * Returns zero rather than failing when WooCommerce is not loaded, so the
 * header still renders on a site where the plugin has been switched off.
 */
function star_electric_child_cart_count(): int {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		return (int) WC()->cart->get_cart_contents_count();
	}
	return 0;
}

/**
 * The cart subtotal, for the header.
 */
function star_electric_child_cart_total(): string {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		return (string) WC()->cart->get_cart_subtotal();
	}
	return '';
}

/**
 * Keep the header badge and total in step with AJAX add-to-cart.
 *
 * Without this the header would still read the count from the page it was
 * rendered with, which is how a cart silently appears empty after an add.
 *
 * @param array $fragments Fragments keyed by selector.
 */
function star_electric_child_cart_fragments( array $fragments ): array {
	ob_start();
	?>
	<span class="badge" data-count="cart"><?php echo esc_html( (string) star_electric_child_cart_count() ); ?></span>
	<?php
	$fragments['span.badge[data-count="cart"]'] = (string) ob_get_clean();

	ob_start();
	?>
	<strong data-cart-total><?php echo wp_kses_post( star_electric_child_cart_total() ); ?></strong>
	<?php
	$fragments['strong[data-cart-total]'] = (string) ob_get_clean();

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'star_electric_child_cart_fragments' );

/**
 * Declare WooCommerce support so the gallery and template hooks behave.
 */
function star_electric_child_woocommerce_support(): void {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'star_electric_child_woocommerce_support' );

/**
 * Four products per row, matching the approved grid.
 */
function star_electric_child_loop_columns(): int {
	return 4;
}
add_filter( 'loop_shop_columns', 'star_electric_child_loop_columns', 20 );

/**
 * Twelve products a page, as the approved shop archive shows.
 */
function star_electric_child_products_per_page(): int {
	return 12;
}
add_filter( 'loop_shop_per_page', 'star_electric_child_products_per_page', 20 );

/**
 * Remove the "Rated x out of 5" markup entirely.
 *
 * No approved source publishes ratings, and the static storefront removed star
 * ratings from the codebase rather than showing an empty control. The same rule
 * applies here: an unrated product shows nothing, never zero stars.
 */
function star_electric_child_strip_ratings(): void {
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
}
add_action( 'init', 'star_electric_child_strip_ratings' );

/**
 * Hide the reviews tab for the same reason.
 *
 * @param array $tabs Product tabs.
 */
function star_electric_child_product_tabs( array $tabs ): array {
	unset( $tabs['reviews'] );
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'star_electric_child_product_tabs', 98 );

/**
 * Add the imported specifications to the Additional Information tab.
 *
 * The importer stores them as JSON because the source publishes an arbitrary
 * set of keys per supplier. A product with none simply loses the tab rather
 * than showing an empty table.
 *
 * @param array $tabs Product tabs.
 */
function star_electric_child_specs_tab( array $tabs ): array {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return $tabs;
	}

	$raw = (string) $product->get_meta( '_star_electric_specifications' );
	if ( '' === $raw ) {
		return $tabs;
	}

	$specs = json_decode( $raw, true );
	if ( ! is_array( $specs ) || empty( $specs ) ) {
		return $tabs;
	}

	$tabs['star_specs'] = array(
		'title'    => __( 'Specifications', 'star-electric-child' ),
		'priority' => 15,
		'callback' => static function () use ( $specs ) {
			echo '<table class="table table--specs"><tbody>';
			foreach ( $specs as $key => $value ) {
				printf(
					'<tr><th scope="row">%s</th><td>%s</td></tr>',
					esc_html( ucwords( str_replace( array( '_', '-' ), ' ', (string) $key ) ) ),
					esc_html( is_scalar( $value ) ? (string) $value : wp_json_encode( $value ) )
				);
			}
			echo '</tbody></table>';
		},
	);

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'star_electric_child_specs_tab', 99 );

/**
 * Show the source the product was catalogued from on its page.
 *
 * Source honesty is a standing rule on this project: a shopper can always see
 * which approved supplier catalogue an item came from.
 */
function star_electric_child_source_meta(): void {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$domain = (string) $product->get_meta( '_star_electric_source_domain' );
	$url    = (string) $product->get_meta( '_star_electric_source_url' );
	if ( '' === $domain ) {
		return;
	}

	echo '<dl class="pdp__meta"><div><dt>' . esc_html__( 'Source', 'star-electric-child' ) . '</dt><dd>';
	if ( '' !== $url ) {
		printf(
			'<a class="link-inline" href="%s" target="_blank" rel="noopener nofollow">%s</a>',
			esc_url( $url ),
			esc_html( $domain )
		);
	} else {
		echo esc_html( $domain );
	}
	echo '</dd></div></dl>';
}
add_action( 'woocommerce_product_meta_end', 'star_electric_child_source_meta' );
