<?php
/**
 * Search engine metadata.
 *
 * Titles, descriptions, canonicals, Open Graph tags and robots directives, all
 * pointed at this WordPress site rather than at the GitHub Pages original.
 * Getting that wrong is not cosmetic: canonicals still naming the static site
 * would hand every page's ranking to a copy nobody is going to maintain.
 *
 * Descriptions are the approved pages' own, with four exceptions where the
 * static copy is no longer true of this site and repeating it would be a claim
 * rather than a port:
 *
 * - Contact offered "call the store, message on WhatsApp". No phone or WhatsApp
 *   number has ever been supplied, so neither route exists to offer.
 * - Complaint offered attachments. This build's complaint form has no
 *   attachment field, deliberately.
 * - Track Order accepted a "billing email or phone". WooCommerce matches on the
 *   email only.
 * - Shipping and Returns advertised delivery charges, timeframes, refund
 *   eligibility and a returns process. None is published, and the pages
 *   themselves say so.
 *
 * Rank Math is active on this site, and two plugins writing the same head is
 * how a page ends up with two canonicals disagreeing with each other. So when
 * Rank Math is present this class writes nothing of its own: it feeds its
 * titles, descriptions, canonicals and robots directives through Rank Math's
 * own filters and lets Rank Math print them. Everything is still derived from
 * the catalogue and the page itself, so there is no second place where a title
 * can be set by hand and forgotten - and if Rank Math is ever deactivated, the
 * same values are emitted directly instead of the site silently losing them.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Emits the document head metadata.
 */
class Star_Electric_SEO {

	/** Google truncates around here; longer descriptions are trimmed to it. */
	private const MAX_DESCRIPTION = 300;

	/**
	 * Hook up.
	 */
	public static function init(): void {
		add_action( 'after_setup_theme', array( __CLASS__, 'support' ) );
		add_filter( 'document_title_parts', array( __CLASS__, 'title' ) );
		add_filter( 'document_title_separator', array( __CLASS__, 'separator' ) );
		add_filter( 'wp_robots', array( __CLASS__, 'robots' ) );

		// Late, so that Rank Math has finished loading and can be detected.
		add_action( 'template_redirect', array( __CLASS__, 'route' ), 1 );
	}

	/**
	 * True when Rank Math is going to write the head itself.
	 */
	private static function delegating(): bool {
		return class_exists( 'RankMath' ) || defined( 'RANK_MATH_VERSION' );
	}

	/**
	 * Decide who prints the metadata, and hook up accordingly.
	 */
	public static function route(): void {
		if ( self::delegating() ) {
			/*
			 * The title is deliberately left to Rank Math. Handing it one from
			 * wp_get_document_title() would call straight back into Rank Math's
			 * own title generation and round the loop again; the title format
			 * belongs in its Titles & Meta settings, not in a filter here.
			 */
			add_filter( 'rank_math/frontend/description', array( __CLASS__, 'rm_description' ) );
			add_filter( 'rank_math/frontend/canonical', array( __CLASS__, 'rm_canonical' ) );
			add_filter( 'rank_math/frontend/robots', array( __CLASS__, 'rm_robots' ) );
			add_filter( 'rank_math/opengraph/facebook/og_site_name', array( __CLASS__, 'rm_site_name' ) );
			return;
		}

		/*
		 * Core emits its own canonical for singular views only. Emitting one for
		 * archives too means core's has to go, or the head carries two.
		 */
		remove_action( 'wp_head', 'rel_canonical' );
		add_action( 'wp_head', array( __CLASS__, 'head' ), 2 );
	}

	/**
	 * Hand Rank Math the description.
	 *
	 * Rank Math's own value wins where an editor has written one by hand on the
	 * page itself - that is a deliberate human decision and should not be
	 * overwritten by a generated line.
	 *
	 * @param string $description Rank Math's description.
	 */
	public static function rm_description( $description ) {
		$ours = self::description();

		if ( is_singular() ) {
			$manual = get_post_meta( get_queried_object_id(), 'rank_math_description', true );
			if ( '' !== (string) $manual ) {
				return $description;
			}
		}

		return '' !== $ours ? $ours : $description;
	}

	/**
	 * Hand Rank Math the canonical.
	 *
	 * @param string $canonical Rank Math's canonical.
	 */
	public static function rm_canonical( $canonical ) {
		$ours = self::canonical();
		return '' !== $ours ? $ours : $canonical;
	}

	/**
	 * Add the noindex directives to Rank Math's robots array.
	 *
	 * @param array $robots Rank Math's robots directives.
	 */
	public static function rm_robots( $robots ) {
		$robots = is_array( $robots ) ? $robots : array();

		if ( self::is_private() ) {
			$robots['index']  = 'noindex';
			$robots['follow'] = 'follow';
		}

		return $robots;
	}

	/**
	 * The store's name, not the hosting subdomain Rank Math defaulted to.
	 *
	 * @param string $name Site name.
	 */
	public static function rm_site_name( $name ) {
		return get_bloginfo( 'name' );
	}

	/**
	 * Let WordPress own the title tag.
	 */
	public static function support(): void {
		add_theme_support( 'title-tag' );
	}

	/**
	 * The em dash the approved storefront titles with.
	 */
	public static function separator(): string {
		return '—';
	}

	/**
	 * Title parts.
	 *
	 * @param array $parts Title parts.
	 */
	public static function title( array $parts ): array {
		if ( function_exists( 'is_shop' ) && is_shop() && ! is_search() ) {
			$parts['title'] = __( 'Shop All Products', 'star-electric' );
		}

		/*
		 * The approved titles carry the store's location, which is how a local
		 * search for "electrical supplies Rawalpindi" finds it at all. Only the
		 * home page gets it appended twice over, so it is added to the site name
		 * rather than to every title.
		 */
		if ( isset( $parts['site'] ) ) {
			$parts['site'] = get_bloginfo( 'name' ) . ' | ' . __( 'Saddar, Rawalpindi', 'star-electric' );
		}

		return $parts;
	}

	/**
	 * The description, canonical and Open Graph block.
	 */
	public static function head(): void {
		$description = self::description();
		$canonical   = self::canonical();

		if ( '' !== $description ) {
			printf( "<meta name=\"description\" content=\"%s\">\n", esc_attr( $description ) );
		}

		if ( '' !== $canonical ) {
			printf( "<link rel=\"canonical\" href=\"%s\">\n", esc_url( $canonical ) );
		}

		$title = wp_get_document_title();

		printf( "<meta property=\"og:type\" content=\"%s\">\n", esc_attr( is_singular( 'product' ) ? 'product' : 'website' ) );
		printf( "<meta property=\"og:site_name\" content=\"%s\">\n", esc_attr( get_bloginfo( 'name' ) ) );
		printf( "<meta property=\"og:title\" content=\"%s\">\n", esc_attr( $title ) );
		if ( '' !== $description ) {
			printf( "<meta property=\"og:description\" content=\"%s\">\n", esc_attr( $description ) );
		}
		if ( '' !== $canonical ) {
			printf( "<meta property=\"og:url\" content=\"%s\">\n", esc_url( $canonical ) );
		}

		$image = self::image();
		if ( '' !== $image ) {
			printf( "<meta property=\"og:image\" content=\"%s\">\n", esc_url( $image ) );
			printf( "<meta name=\"twitter:card\" content=\"%s\">\n", 'summary_large_image' );
		}
	}

	/**
	 * Keep the private parts of the shop out of the index.
	 *
	 * The approved storefront marks cart, checkout, account, wishlist and search
	 * results "noindex, follow" - they are personal views of the catalogue, not
	 * pages anyone should arrive on from a search. WooCommerce sets some of
	 * these itself; the wishlist is this build's own and would otherwise be
	 * indexed as an empty table.
	 *
	 * @param array $robots Robots directives.
	 */
	public static function robots( array $robots ): array {
		if ( self::is_private() ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
			unset( $robots['index'] );
		}

		return $robots;
	}

	/**
	 * Is this a personal view of the shop rather than a page to be found?
	 */
	private static function is_private(): bool {
		$private = false;

		if ( function_exists( 'is_cart' ) ) {
			$private = is_cart() || is_checkout() || is_account_page();
		}

		return $private || is_search() || is_page( 'wishlist' );
	}

	/**
	 * The canonical URL for whatever is being viewed.
	 *
	 * Paged views point at themselves rather than at page one, so that page
	 * three of a 2,390 product department is not declared a duplicate of page
	 * one and dropped. Filter parameters are deliberately excluded: a filtered
	 * view is a slice of the same catalogue, not a separate page.
	 */
	private static function canonical(): string {
		$url = '';

		if ( is_front_page() ) {
			$url = home_url( '/' );
		} elseif ( is_singular() ) {
			$url = (string) get_permalink();
		} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
			$url = (string) wc_get_page_permalink( 'shop' );
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$term = get_queried_object();
			if ( $term instanceof WP_Term ) {
				$link = get_term_link( $term );
				$url  = is_wp_error( $link ) ? '' : (string) $link;
			}
		}

		if ( '' === $url ) {
			return '';
		}

		$paged = max( (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
		if ( $paged > 1 ) {
			$url = trailingslashit( $url ) . 'page/' . $paged . '/';
		}

		return $url;
	}

	/**
	 * The description for whatever is being viewed.
	 */
	private static function description(): string {
		if ( is_front_page() ) {
			return __( 'Shop electrical supplies at Star Electric Enterprises, Saddar, Rawalpindi. Wires and cables, switches and sockets, circuit breakers, lighting, fans, power equipment and tools for homes, offices, commercial projects, electricians and contractors.', 'star-electric' );
		}

		if ( function_exists( 'is_shop' ) && is_shop() && ! is_search() ) {
			return __( 'Browse the full electrical catalogue at Star Electric Enterprises: wiring and cables, switches and sockets, circuit protection, lighting, fans, power equipment, smart devices and tools. Filter by category, brand, price and availability.', 'star-electric' );
		}

		if ( is_singular( 'product' ) ) {
			return self::product_description();
		}

		if ( is_tax() || is_category() || is_tag() ) {
			$term = get_queried_object();
			if ( ! $term instanceof WP_Term ) {
				return '';
			}
			$described = wp_strip_all_tags( (string) term_description( $term ) );
			if ( '' !== trim( $described ) ) {
				return self::trim( $described );
			}
			return self::trim(
				sprintf(
					/* translators: 1: term name, 2: product count */
					__( '%1$s at Star Electric Enterprises, Saddar, Rawalpindi. %2$s products, with brand, price and availability filters.', 'star-electric' ),
					$term->name,
					number_format_i18n( (int) $term->count )
				)
			);
		}

		if ( is_page() ) {
			$page = self::page_description( (string) get_post_field( 'post_name', get_queried_object_id() ) );
			if ( '' !== $page ) {
				return $page;
			}
		}

		if ( is_singular() && has_excerpt() ) {
			return self::trim( wp_strip_all_tags( (string) get_the_excerpt() ) );
		}

		return '';
	}

	/**
	 * A product's description.
	 *
	 * Its own short description where the source published one. Where it did
	 * not, a factual line assembled from the brand, category and quotation
	 * status - never marketing copy invented for the product.
	 */
	private static function product_description(): string {
		$product = function_exists( 'wc_get_product' ) ? wc_get_product( get_queried_object_id() ) : null;
		if ( ! $product instanceof WC_Product ) {
			return '';
		}

		$short = wp_strip_all_tags( (string) $product->get_short_description() );
		if ( '' !== trim( $short ) ) {
			return self::trim( $short );
		}

		$brand = wp_get_object_terms( $product->get_id(), 'star_brand', array( 'fields' => 'names' ) );
		$brand = ( ! is_wp_error( $brand ) && $brand ) ? $brand[0] : '';

		$cat = wp_get_object_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) );
		$cat = ( ! is_wp_error( $cat ) && $cat ) ? $cat[0] : '';

		$quote = class_exists( 'Star_Electric_Quote_Only' ) && Star_Electric_Quote_Only::is_quote( $product );

		$parts = array( $product->get_name() );
		if ( '' !== $brand ) {
			/* translators: %s: brand name */
			$parts[] = sprintf( __( 'by %s', 'star-electric' ), $brand );
		}
		if ( '' !== $cat ) {
			/* translators: %s: category name */
			$parts[] = sprintf( __( 'in %s', 'star-electric' ), $cat );
		}

		$line = implode( ' ', $parts ) . '. ';
		$line .= $quote
			? __( 'Priced on enquiry — request a written quotation from Star Electric Enterprises, Saddar, Rawalpindi.', 'star-electric' )
			: __( 'Available from Star Electric Enterprises, Saddar, Rawalpindi.', 'star-electric' );

		return self::trim( $line );
	}

	/**
	 * The approved description for a content page, by slug.
	 *
	 * @param string $slug Page slug.
	 */
	private static function page_description( string $slug ): string {
		$map = array(
			'about'                => __( 'About Star Electric Enterprises, an electrical products store in Saddar, Rawalpindi supplying homes, offices, commercial projects, electricians and contractors.', 'star-electric' ),
			'faq'                  => __( 'Frequently asked questions about ordering, products, payment, delivery, returns and bulk orders at Star Electric Enterprises, Saddar, Rawalpindi.', 'star-electric' ),
			'brands'               => __( 'Browse product brands stocked at Star Electric Enterprises, Saddar, Rawalpindi. Search the brand directory alphabetically and view the products available under each brand.', 'star-electric' ),
			'deals'                => __( 'Products currently listed below their previous price by their own supplier source, across lighting, circuit protection, wiring, fans and tools. Nothing here is a store promotion.', 'star-electric' ),
			'categories'           => __( 'Every department in the Star Electric Enterprises catalogue, from wires and cables to smart home devices, with the subcategories inside each one.', 'star-electric' ),
			'contact'              => __( 'Contact Star Electric Enterprises in Saddar, Rawalpindi. Send a message about electrical products, stock availability, an order or a project requirement.', 'star-electric' ),
			'quote-request'        => __( 'Request a written quotation for bulk electrical requirements from Star Electric Enterprises, Saddar, Rawalpindi. For contractors, electricians, builders and commercial projects.', 'star-electric' ),
			'complaint'            => __( 'Report a problem with an order or product from Star Electric Enterprises, Saddar, Rawalpindi. Tell us what happened and we will look into it.', 'star-electric' ),
			'track-order'          => __( 'Track an order from Star Electric Enterprises, Saddar, Rawalpindi. Enter your order number and the billing email the order was taken against.', 'star-electric' ),
			'privacy-policy'       => __( 'How Star Electric Enterprises, Saddar, Rawalpindi handles the information you provide. No privacy policy is published yet, and this page says so rather than stating terms that have not been agreed.', 'star-electric' ),
			'terms-and-conditions' => __( 'Terms for using the Star Electric Enterprises website and buying from the store in Saddar, Rawalpindi. No terms of sale are published yet, and this page says so rather than stating terms that have not been agreed.', 'star-electric' ),
			'shipping'             => __( 'Delivery and collection for orders from Star Electric Enterprises, Saddar, Rawalpindi. No delivery areas, charges or timeframes are published yet; the store confirms them with you before an order is placed.', 'star-electric' ),
			'returns'              => __( 'Returns and replacements for products bought from Star Electric Enterprises, Saddar, Rawalpindi. No returns or warranty policy is published yet; contact the store if something is faulty, damaged or not what you ordered.', 'star-electric' ),
			'wishlist'             => __( 'Products you have saved at Star Electric Enterprises. Saved items are stored in this browser only.', 'star-electric' ),
		);

		return $map[ $slug ] ?? '';
	}

	/**
	 * The sharing image.
	 */
	private static function image(): string {
		if ( is_singular( 'product' ) && has_post_thumbnail() ) {
			$src = wp_get_attachment_image_src( (int) get_post_thumbnail_id(), 'large' );
			if ( is_array( $src ) && ! empty( $src[0] ) ) {
				return (string) $src[0];
			}
		}

		$logo = (int) get_theme_mod( 'custom_logo' );
		if ( $logo ) {
			$src = wp_get_attachment_image_src( $logo, 'full' );
			if ( is_array( $src ) && ! empty( $src[0] ) ) {
				return (string) $src[0];
			}
		}

		return '';
	}

	/**
	 * Collapse whitespace and cut to length on a word boundary.
	 *
	 * @param string $text Description text.
	 */
	private static function trim( string $text ): string {
		$text = trim( (string) preg_replace( '/\s+/', ' ', $text ) );

		if ( mb_strlen( $text ) <= self::MAX_DESCRIPTION ) {
			return $text;
		}

		$cut   = mb_substr( $text, 0, self::MAX_DESCRIPTION );
		$space = mb_strrpos( $cut, ' ' );

		return ( false === $space ? $cut : mb_substr( $cut, 0, $space ) ) . '…';
	}
}
