<?php
/**
 * The global shell: icons, navigation, and the map from the approved
 * storefront's page names to their WordPress equivalents.
 *
 * The static site defines its header, drawer and footer once in
 * js/components.js and injects them into every page. header.php and footer.php
 * are the direct port of those three functions, and this class carries the
 * pieces they share - the icon set, the nav definition and the URL resolver -
 * so the markup stays identical to the approved design.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared shell helpers.
 */
class Star_Electric_Shell {

	/**
	 * The inline SVG icon set, copied from the approved storefront.
	 *
	 * Inline rather than a sprite because the static site had to work from
	 * file://, and keeping them identical means the CSS needs no changes.
	 *
	 * @var array<string,string>
	 */
	private const ICONS = array(
		'search'     => '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/>',
		'user'       => '<circle cx="12" cy="8.5" r="3.6"/><path d="M4.8 20a7.2 7.2 0 0 1 14.4 0"/>',
		'heart'      => '<path d="M12 20s-7.3-4.4-7.3-9.3A4.2 4.2 0 0 1 12 8.2a4.2 4.2 0 0 1 7.3 2.5C19.3 15.6 12 20 12 20Z"/>',
		'cart'       => '<path d="M3 5h2.3l2.2 9.5h9.2L19 8H7"/><circle cx="9.5" cy="18.5" r="1.6"/><circle cx="16.5" cy="18.5" r="1.6"/>',
		'menu'       => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'      => '<path d="m6 6 12 12M18 6 6 18"/>',
		'chevdown'   => '<path d="m7 10 5 5 5-5"/>',
		'chevright'  => '<path d="m10 7 5 5-5 5"/>',
		'chevleft'   => '<path d="m14 7-5 5 5 5"/>',
		'arrowright' => '<path d="M5 12h13M13 6l6 6-6 6"/>',
		'arrowup'    => '<path d="M12 19V6M6 12l6-6 6 6"/>',
		'phone'      => '<path d="M5 4h4l1.6 4-2.2 1.6a12 12 0 0 0 6 6L16 13.4l4 1.6v4a1 1 0 0 1-1.1 1A16.5 16.5 0 0 1 4 6.1 1 1 0 0 1 5 4Z"/>',
		'whatsapp'   => '<path d="M20 11.8a8 8 0 0 1-11.9 7L4 20l1.3-3.9A8 8 0 1 1 20 11.8Z"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5"/>',
		'pin'        => '<path d="M12 21s7-6.1 7-11a7 7 0 1 0-14 0c0 4.9 7 11 7 11Z"/><circle cx="12" cy="10" r="2.6"/>',
		'clock'      => '<circle cx="12" cy="12" r="9"/><path d="M12 6.5V12l4 2"/>',
		'truck'      => '<path d="M3 7h11v9H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>',
		'shield'     => '<path d="M12 3.5 19 6v6c0 4.2-2.9 7.4-7 8.5-4.1-1.1-7-4.3-7-8.5V6Z"/><path d="m9 12 2.2 2.2L15.5 10"/>',
		'headset'    => '<path d="M12 3a7 7 0 0 0-7 7v4a3 3 0 0 0 3 3h1v-7H7"/><path d="M19 14v-4a7 7 0 0 0-7-7"/><path d="M19 14a3 3 0 0 1-3 3h-1v-7h1a3 3 0 0 1 3 3Z"/><path d="M16 17v1a3 3 0 0 1-3 3h-1"/>',
		'lock'       => '<rect x="4.5" y="10" width="15" height="10" rx="3"/><path d="M8.5 10V7.5a3.5 3.5 0 0 1 7 0V10"/><path d="M12 14v2"/>',
		'tag'        => '<path d="M11 3H5a2 2 0 0 0-2 2v6l10 10 8-8L11 3Z"/><circle cx="7.8" cy="7.8" r="1.4"/>',
		'grid'       => '<rect x="4" y="4" width="7" height="7" rx="1.5"/><rect x="13" y="4" width="7" height="7" rx="1.5"/><rect x="4" y="13" width="7" height="7" rx="1.5"/><rect x="13" y="13" width="7" height="7" rx="1.5"/>',
		'list'       => '<path d="M4 6h16M4 12h16M4 18h16"/>',
		'eye'        => '<path d="M2.8 12S6.6 5.8 12 5.8 21.2 12 21.2 12 17.4 18.2 12 18.2 2.8 12 2.8 12Z"/><circle cx="12" cy="12" r="3"/>',
		'filter'     => '<path d="M4 6h16M7 12h10M10 18h4"/>',
		'check'      => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
		'plus'       => '<path d="M12 5v14M5 12h14"/>',
		'minus'      => '<path d="M5 12h14"/>',
		'trash'      => '<path d="M4 7h16M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7"/><path d="M6 7v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7"/>',
		'box'        => '<path d="m12 3 8 4.2v9.6L12 21l-8-4.2V7.2Z"/><path d="m4 7.2 8 4.3 8-4.3M12 21v-9.5"/>',
		'doc'        => '<path d="M6 3h7l5 5v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/><path d="M13 3v5h5"/>',
		'mail'       => '<rect x="3" y="5.5" width="18" height="13" rx="2.5"/><path d="m3.5 7 8.5 6 8.5-6"/>',
		'upload'     => '<path d="M12 16V5M8 9l4-4 4 4"/><path d="M4 16v2.5A1.5 1.5 0 0 0 5.5 20h13a1.5 1.5 0 0 0 1.5-1.5V16"/>',
		'star'       => '<path d="m12 3.6 2.6 5.4 5.9.8-4.3 4.2 1 5.9L12 17.1 6.8 19.9l1-5.9L3.5 9.8l5.9-.8z"/>',
		'bolt'       => '<path d="M13.5 3 6 13h5l-1.5 8L18 11h-5.5z"/>',
		'info'       => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/>',
		'map'        => '<path d="m4 20 6-2.2 4 2.2 6-2.4V4l-6 2.4-4-2.2L4 6.4Z"/><path d="M10 4.2v13.6M14 6.4V20"/>',
		'logout'     => '<path d="M15 12H4M8 8l-4 4 4 4"/><path d="M11 4h7a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-7"/>',
		'building'   => '<path d="M4 21V6a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v15"/><path d="M14 10h5a1 1 0 0 1 1 1v10"/><path d="M7 9h3M7 13h3M7 17h3M17 14h1M17 18h1"/>',
		'refresh'    => '<path d="M4 12a8 8 0 0 1 13.7-5.6L20 8"/><path d="M20 4v4h-4"/><path d="M20 12a8 8 0 0 1-13.7 5.6L4 16"/><path d="M4 20v-4h4"/>',
	);

	/**
	 * Page slugs behind each shell link, matching the approved sitemap.
	 *
	 * WooCommerce owns shop, cart, checkout and account; everything else is an
	 * ordinary page.
	 *
	 * @var array<string,string>
	 */
	private const PAGES = array(
		'category'    => 'categories',
		'brands'      => 'brands',
		'deals'       => 'deals',
		'quote'       => 'quote-request',
		'about'       => 'about',
		'contact'     => 'contact',
		'faq'         => 'faq',
		'track-order' => 'track-order',
		'returns'     => 'returns',
		'complaint'   => 'complaint',
		'privacy'     => 'privacy-policy',
		'terms'       => 'terms-and-conditions',
		'shipping'    => 'shipping',
		'wishlist'    => 'wishlist',
	);

	/** Resolved permalinks, so a page with eight footer links is looked up once. */
	private static $cache = array();

	/**
	 * Render one inline SVG icon.
	 *
	 * @param string $name Icon key.
	 * @param string $cls  Extra class.
	 */
	public static function icon( string $name, string $cls = '' ): string {
		$body = self::ICONS[ $name ] ?? '';
		return '<svg class="ico ' . esc_attr( $cls ) . '" viewBox="0 0 24 24" aria-hidden="true">' . $body . '</svg>';
	}

	/**
	 * Resolve a shell link to a real URL on this site.
	 *
	 * A page that has not been created yet falls back to the home page rather
	 * than emitting a link that 404s.
	 *
	 * @param string $key Link key.
	 */
	public static function url( string $key ): string {
		if ( isset( self::$cache[ $key ] ) ) {
			return self::$cache[ $key ];
		}

		$url = '';

		switch ( $key ) {
			case 'home':
				$url = home_url( '/' );
				break;
			case 'shop':
				$url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
				break;
			case 'cart':
				$url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
				break;
			case 'checkout':
				$url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/' );
				break;
			case 'account':
				$url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
				break;
			case 'search':
				$url = home_url( '/?post_type=product' );
				break;
			default:
				$slug = self::PAGES[ $key ] ?? '';
				if ( '' !== $slug ) {
					$page = get_page_by_path( $slug );
					$url  = $page instanceof WP_Post ? (string) get_permalink( $page ) : '';
				}
				break;
		}

		if ( '' === $url ) {
			$url = home_url( '/' );
		}

		self::$cache[ $key ] = $url;
		return $url;
	}

	/**
	 * The quotation link for one product, matching the approved storefront.
	 *
	 * Not ?product=: WordPress owns that name as WooCommerce's post-type query
	 * var, so ?product=2333 is read as a request for a product called "2333" and
	 * returns 404 before any template runs. Every Request a Quote button on a
	 * product page was dead because of it.
	 *
	 * @param int $product_id Product ID.
	 */
	public static function quote_url( int $product_id = 0 ): string {
		$url = self::url( 'quote' );
		return $product_id ? add_query_arg( 'quote_product', $product_id, $url ) : $url;
	}

	/**
	 * Primary navigation - the single source of truth, as in the static site.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public static function nav(): array {
		return array(
			array(
				'key'   => 'home',
				'label' => __( 'Home', 'star-electric-child' ),
				'url'   => self::url( 'home' ),
			),
			array(
				'key'   => 'shop',
				'label' => __( 'Shop', 'star-electric-child' ),
				'url'   => self::url( 'shop' ),
			),
			array(
				'key'   => 'category',
				'label' => __( 'Categories', 'star-electric-child' ),
				'url'   => self::url( 'category' ),
				'mega'  => true,
			),
			array(
				'key'   => 'brands',
				'label' => __( 'Brands', 'star-electric-child' ),
				'url'   => self::url( 'brands' ),
			),
			array(
				'key'   => 'deals',
				'label' => __( 'Deals', 'star-electric-child' ),
				'url'   => self::url( 'deals' ),
			),
			array(
				'key'   => 'quote',
				'label' => __( 'Bulk / Project Orders', 'star-electric-child' ),
				'url'   => self::url( 'quote' ),
			),
			array(
				'key'   => 'about',
				'label' => __( 'About', 'star-electric-child' ),
				'url'   => self::url( 'about' ),
			),
			array(
				'key'   => 'contact',
				'label' => __( 'Contact', 'star-electric-child' ),
				'url'   => self::url( 'contact' ),
			),
		);
	}

	/**
	 * Which nav item should be lit for the page being viewed.
	 */
	public static function active(): string {
		if ( is_front_page() ) {
			return 'home';
		}
		if ( function_exists( 'is_shop' ) && ( is_shop() || is_product() ) ) {
			return 'shop';
		}
		if ( is_tax( 'product_cat' ) || is_tax( 'star_department' ) ) {
			return 'category';
		}
		if ( is_tax( 'star_brand' ) ) {
			return 'brands';
		}

		if ( is_page() ) {
			$slug = (string) get_post_field( 'post_name', get_queried_object_id() );
			foreach ( self::PAGES as $key => $page_slug ) {
				if ( $page_slug === $slug ) {
					return $key;
				}
			}
		}

		return '';
	}

	/**
	 * The product category tree that fills the mega menu and the footer.
	 *
	 * Cached for the request; the mega menu, the drawer and the footer all ask
	 * for it.
	 *
	 * @return array<int,array{term:WP_Term,children:array<int,WP_Term>}>
	 */
	public static function category_tree(): array {
		static $tree = null;
		if ( null !== $tree ) {
			return $tree;
		}

		$tree    = array();
		/*
		 * Ordered by how much of the catalogue sits behind each department, which
		 * is the order the approved storefront navigates in - Wires & Cables
		 * first at 2,390 products, Earthing Material last. Alphabetical would
		 * bury the biggest department in the middle of the list.
		 */
		$parents = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => 0,
				'hide_empty' => false,
				'orderby'    => 'count',
				'order'      => 'DESC',
			)
		);

		if ( is_wp_error( $parents ) ) {
			return $tree;
		}

		foreach ( $parents as $parent ) {
			if ( 'uncategorized' === $parent->slug ) {
				continue;
			}
			$children = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'parent'     => $parent->term_id,
					'hide_empty' => false,
					'orderby'    => 'count',
					'order'      => 'DESC',
				)
			);
			$tree[]   = array(
				'term'     => $parent,
				'children' => is_wp_error( $children ) ? array() : $children,
			);
		}

		return $tree;
	}

	/**
	 * The photograph for a product category, from the media library.
	 *
	 * The importer stores each category image as the term's thumbnail, so this
	 * is the same file the approved storefront uses. A category without one
	 * renders nothing rather than a broken image.
	 *
	 * @param string $slug   Category slug.
	 * @param int    $width  Width attribute.
	 * @param int    $height Height attribute.
	 */
	/**
	 * The line-art icon the approved subcategory tiles use.
	 *
	 * Every tile in a department shows that department's icon, not a picture per
	 * subcategory - only departments have a photograph, and shrinking one to
	 * 46x36 turns it into a dark rectangle.
	 */
	private const CATEGORY_ICON = array(
		'wires-cables'           => 'cable-coil',
		'switches-sockets'       => 'switch-plate',
		'circuit-protection'     => 'mcb',
		'industrial-control'     => 'relay',
		'fans-ventilation'       => 'ceiling-fan',
		'lighting'               => 'led-bulb',
		'power-energy'           => 'energy-meter',
		'smart-home'             => 'smart-plug',
		'electrical-accessories' => 'tape',
	);

	/**
	 * The icon for a department, or for a subcategory's department.
	 *
	 * @param WP_Term $term   Term.
	 * @param int     $width  Display width.
	 * @param int     $height Display height.
	 */
	public static function category_icon( WP_Term $term, int $width = 46, int $height = 36 ): string {
		$slug = $term->slug;
		if ( $term->parent ) {
			$parent = get_term( (int) $term->parent, $term->taxonomy );
			if ( $parent instanceof WP_Term ) {
				$slug = $parent->slug;
			}
		}

		$icon = self::CATEGORY_ICON[ $slug ] ?? 'cable-coil';

		return sprintf(
			'<img src="%s" alt="" width="%d" height="%d" loading="lazy" decoding="async" aria-hidden="true">',
			esc_url( get_stylesheet_directory_uri() . '/assets/images/icons/' . $icon . '.svg' ),
			$width,
			$height
		);
	}

	/**
	 * A term's picture, falling back to its department's.
	 *
	 * Only departments carry a thumbnail: the approved subcategory tiles show
	 * the department's own art rather than a picture per subcategory, and a
	 * tile with no image at all collapses to half the approved height.
	 *
	 * @param WP_Term $term   Term.
	 * @param int     $width  Display width.
	 * @param int     $height Display height.
	 */
	public static function term_image( WP_Term $term, int $width, int $height ): string {
		$art = self::category_image( $term->slug, $width, $height );
		if ( '' !== $art || ! $term->parent ) {
			return $art;
		}

		$parent = get_term( (int) $term->parent, $term->taxonomy );
		return $parent instanceof WP_Term ? self::category_image( $parent->slug, $width, $height ) : '';
	}

	public static function category_image( string $slug, int $width, int $height ): string {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term instanceof WP_Term ) {
			return '';
		}

		$thumb = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
		if ( ! $thumb ) {
			return '';
		}

		return (string) wp_get_attachment_image(
			$thumb,
			'large',
			false,
			array(
				'alt'     => '',
				'width'   => $width,
				'height'  => $height,
				'loading' => 'lazy',
			)
		);
	}

	/**
	 * The availability pill for a product.
	 *
	 * Read from the source availability rather than from WooCommerce's stock
	 * status, because WooCommerce has two states and the catalogue has three.
	 * 2,382 products publish no availability at all; showing those as "In Stock"
	 * would be a claim about the shop's shelves that no source supports, so they
	 * say so instead.
	 *
	 * @param WC_Product $product Product.
	 * @return array{0:string,1:string} Class and label.
	 */
	public static function availability_pill( WC_Product $product ): array {
		$source = (string) $product->get_meta( '_star_electric_availability' );

		if ( '' === $source ) {
			// Imported before the availability meta existed, or set by hand.
			$source = $product->is_in_stock() ? 'In Stock' : 'Out of Stock';
		}

		switch ( $source ) {
			case 'In Stock':
				return array( 'pill--in', __( 'In Stock', 'star-electric-child' ) );
			case 'Out of Stock':
				return array( 'pill--out', __( 'Out of Stock', 'star-electric-child' ) );
			case 'Low Stock':
				return array( 'pill--low', __( 'Low Stock', 'star-electric-child' ) );
			default:
				return array( 'pill--unknown', __( 'Availability not specified', 'star-electric-child' ) );
		}
	}

	/**
	 * The site logo, which is a real asset and must never be redrawn.
	 *
	 * @param string $cls Extra class.
	 */
	public static function logo( string $cls = '' ): string {
		return sprintf(
			'<a class="logo %s" href="%s" aria-label="%s"><img src="%s" alt="%s"></a>',
			esc_attr( $cls ),
			esc_url( self::url( 'home' ) ),
			esc_attr__( 'Star Electric Enterprises — home', 'star-electric-child' ),
			esc_url( get_stylesheet_directory_uri() . '/assets/logo/star-electric-logo-trimmed.png' ),
			esc_attr__( 'Star Electric Enterprises', 'star-electric-child' )
		);
	}

	/**
	 * The page head: breadcrumb, title and optional sub-line.
	 *
	 * Fourteen ported pages open with the same block. Keeping it in one place
	 * means the breadcrumb markup cannot drift between them the way it did
	 * across the static site's individual HTML files.
	 *
	 * @param array  $crumbs Ordered [ label => url ] pairs; the last entry is
	 *                       the current page and takes an empty url.
	 * @param string $title  Page title.
	 * @param string $sub    Optional sub-line.
	 */
	public static function page_head( array $crumbs, string $title, string $sub = '' ): void {
		?>
		<div class="page-head">
			<div class="container">
				<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'star-electric-child' ); ?>">
					<ol class="breadcrumb">
						<?php
						$last = count( $crumbs ) - 1;
						$i    = 0;
						foreach ( $crumbs as $label => $url ) {
							if ( $i > 0 ) {
								echo '<li class="sep" aria-hidden="true">/</li>';
							}
							if ( $i === $last || '' === $url ) {
								printf( '<li aria-current="page">%s</li>', esc_html( (string) $label ) );
							} else {
								printf(
									'<li><a href="%s">%s</a></li>',
									esc_url( (string) $url ),
									esc_html( (string) $label )
								);
							}
							++$i;
						}
						?>
					</ol>
				</nav>
				<h1 class="page-head__title"><?php echo esc_html( $title ); ?></h1>
				<?php if ( '' !== $sub ) : ?>
					<p class="page-head__sub"><?php echo esc_html( $sub ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * The "these terms are not published yet" panel.
	 *
	 * Privacy, terms, shipping and returns all say the same thing in the same
	 * shape on the approved site, and they say it deliberately: the store has
	 * agreed no policy, and showing invented terms would be worse than saying
	 * so. The two paragraphs differ per page, nothing else does.
	 *
	 * @param string $heading Panel heading.
	 * @param string $lead    First paragraph.
	 * @param string $detail  Second paragraph.
	 */
	public static function policy_panel( string $heading, string $lead, string $detail ): void {
		?>
		<section class="section section--sm">
			<div class="container">
				<div class="doc-single">
					<div class="panel">
						<h2 class="panel__title" style="margin-bottom:12px"><?php echo esc_html( $heading ); ?></h2>
						<p class="t-sm t-muted" style="margin-bottom:16px"><?php echo esc_html( $lead ); ?></p>
						<p class="t-sm t-muted" style="margin-bottom:20px"><?php echo esc_html( $detail ); ?></p>
						<div class="btn-row">
							<a class="btn btn--accent btn--sm" href="<?php echo esc_url( self::url( 'contact' ) ); ?>">
								<?php esc_html_e( 'Contact the store', 'star-electric-child' ); ?>
							</a>
							<a class="btn btn--ghost btn--sm" href="<?php echo esc_url( self::url( 'quote' ) ); ?>">
								<?php esc_html_e( 'Request a quotation', 'star-electric-child' ); ?>
							</a>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * The three-step cart / checkout / complete indicator.
	 *
	 * @param string $at Which step is current: cart, checkout or done.
	 */
	public static function steps( string $at ): void {
		$order = array( 'cart', 'checkout', 'done' );
		$now   = array_search( $at, $order, true );
		$now   = false === $now ? 0 : (int) $now;

		$labels = array(
			'cart'     => __( 'Cart', 'star-electric-child' ),
			'checkout' => __( 'Checkout', 'star-electric-child' ),
			'done'     => __( 'Order Complete', 'star-electric-child' ),
		);
		?>
		<div class="steps" style="margin-top:16px">
			<?php foreach ( $order as $i => $key ) : ?>
				<?php if ( $i > 0 ) : ?>
					<span class="step__line" aria-hidden="true"></span>
				<?php endif; ?>
				<span class="step <?php echo esc_attr( $i < $now ? 'is-done' : ( $i === $now ? 'is-active' : '' ) ); ?>">
					<span class="step__num">
						<?php if ( $i < $now ) : ?>
							<?php echo self::icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php else : ?>
							<?php echo esc_html( (string) ( $i + 1 ) ); ?>
						<?php endif; ?>
					</span>
					<?php echo esc_html( $labels[ $key ] ); ?>
				</span>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
