<?php
/**
 * Merchandising navigation.
 *
 * The approved homepage does not navigate by taxonomy alone. Its department
 * strip, hero rail and product rails are a curated list of twenty selections,
 * some of which are a category, some a subcategory, some a cross-category
 * department, and one a search within a category. That list is a business
 * decision rather than a consequence of the data, so it is configuration here
 * rather than something rebuilt from the taxonomy each time.
 *
 * Every entry resolves to a real URL and a real count. Nothing is invented: a
 * selection with nothing behind it reports zero and says so.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Curated navigation.
 */
class Star_Electric_Navigation {

	/** Cache key for the resolved counts. */
	private const COUNT_KEY = 'star_electric_nav_counts';

	/** How long resolved counts stay cached. */
	private const COUNT_TTL = HOUR_IN_SECONDS;

	/**
	 * The department strip, in the approved order.
	 *
	 * 'art' names the picture built for that department; every one is a real
	 * photograph of something inside it, never an icon standing in for a
	 * product.
	 */
	public static function departments(): array {
		return array(
			array( 'label' => 'Wires & Cables', 'art' => 'wires-cables', 'sel' => array( 'cat' => 'wires-cables' ) ),
			array( 'label' => 'Power Cables', 'art' => 'power-cables', 'sel' => array( 'cat' => 'wires-cables', 'sub' => 'power-cables' ) ),
			array( 'label' => 'Solar Panel Cables', 'art' => 'solar-cables', 'sel' => array( 'cat' => 'wires-cables', 'sub' => 'solar-cables' ) ),
			array( 'label' => 'PVC Pipe & Conduit', 'art' => 'pvc-conduit', 'sel' => array( 'cat' => 'wires-cables', 'sub' => 'cable-management' ) ),
			array( 'label' => 'Switches & Sockets', 'art' => 'switches-sockets', 'sel' => array( 'cat' => 'switches-sockets' ) ),
			array( 'label' => 'Smart Switches', 'art' => 'smart-switches', 'sel' => array( 'cat' => 'smart-home', 'sub' => 'smart-switches' ) ),
			array( 'label' => 'Data & Telephone Outlets', 'art' => 'data-outlets', 'sel' => array( 'cat' => 'switches-sockets', 'query' => 'data socket' ) ),
			array( 'label' => 'Circuit Protection', 'art' => 'circuit-protection', 'sel' => array( 'cat' => 'circuit-protection' ) ),
			array( 'label' => 'Distribution Boards', 'art' => 'distribution-boards', 'sel' => array( 'cat' => 'electrical-accessories', 'sub' => 'distribution-boards' ) ),
			array( 'label' => 'Lighting & Fixtures', 'art' => 'lighting', 'sel' => array( 'cat' => 'lighting' ) ),
			array( 'label' => 'LED Lighting', 'art' => 'led-lighting', 'sel' => array( 'cat' => 'lighting', 'sub' => 'led-lighting' ) ),
			array( 'label' => 'Fans & Ventilation', 'art' => 'fans-ventilation', 'sel' => array( 'cat' => 'fans-ventilation' ) ),
			array( 'label' => 'Wiring Accessories', 'art' => 'wiring-accessories', 'sel' => array( 'cat' => 'electrical-accessories' ) ),
			array( 'label' => 'Smart Home', 'art' => 'smart-home', 'sel' => array( 'cat' => 'smart-home' ) ),
			array( 'label' => 'Industrial Control', 'art' => 'industrial-control', 'sel' => array( 'cat' => 'industrial-control' ) ),
			array( 'label' => 'Power & Energy', 'art' => 'power-energy', 'sel' => array( 'cat' => 'power-energy' ) ),
			array( 'label' => 'Earthing Material', 'art' => 'earthing-material', 'sel' => array( 'cat' => 'earthing-material' ) ),
			array( 'label' => 'Networking Solutions', 'art' => 'networking-solutions', 'sel' => array( 'dept' => 'networking-solutions' ) ),
			array( 'label' => 'Home Automation', 'art' => 'home-automation', 'sel' => array( 'dept' => 'home-automation' ) ),
			array( 'label' => 'Office Automation Solutions', 'art' => 'office-automation', 'sel' => array( 'dept' => 'office-automation' ) ),
		);
	}

	/**
	 * Shortcuts appended to the hero rail after the category tree.
	 */
	public static function shortcuts(): array {
		return array(
			array( 'label' => 'Solar Panel Cables', 'sel' => array( 'cat' => 'wires-cables', 'sub' => 'solar-cables' ) ),
			array( 'label' => 'PVC Pipe & Conduit', 'sel' => array( 'cat' => 'wires-cables', 'sub' => 'cable-management' ) ),
			array( 'label' => 'Distribution Boards', 'sel' => array( 'cat' => 'electrical-accessories', 'sub' => 'distribution-boards' ) ),
			array( 'label' => 'LED Lighting', 'sel' => array( 'cat' => 'lighting', 'sub' => 'led-lighting' ) ),
			array( 'label' => 'Smart Switches', 'sel' => array( 'cat' => 'smart-home', 'sub' => 'smart-switches' ) ),
			array( 'label' => 'Data & Telephone Outlets', 'sel' => array( 'cat' => 'switches-sockets', 'query' => 'data socket' ) ),
			array( 'label' => 'Earthing Material', 'sel' => array( 'cat' => 'earthing-material' ) ),
			array( 'label' => 'Networking Solutions', 'sel' => array( 'dept' => 'networking-solutions' ) ),
			array( 'label' => 'Home Automation', 'sel' => array( 'dept' => 'home-automation' ) ),
			array( 'label' => 'Office Automation Solutions', 'sel' => array( 'dept' => 'office-automation' ) ),
		);
	}

	/**
	 * The homepage product rails, in the approved order.
	 *
	 * Industrial Control and Power & Energy are absent deliberately: their
	 * sources publish ranges only, with no individual products to fill a row.
	 */
	public static function rails(): array {
		return array(
			array( 'title' => 'Wires & Cables', 'sel' => array( 'cat' => 'wires-cables' ) ),
			array( 'title' => 'Switches & Sockets', 'sel' => array( 'cat' => 'switches-sockets' ) ),
			array( 'title' => 'Lighting & Fixtures', 'sel' => array( 'cat' => 'lighting' ) ),
			array( 'title' => 'Fans & Ventilation', 'sel' => array( 'cat' => 'fans-ventilation' ) ),
			array( 'title' => 'Circuit Protection', 'sel' => array( 'cat' => 'circuit-protection' ) ),
			array( 'title' => 'Wiring Accessories', 'sel' => array( 'cat' => 'electrical-accessories' ) ),
			array( 'title' => 'Solar Panel Cables', 'sel' => array( 'cat' => 'wires-cables', 'sub' => 'solar-cables' ) ),
			array( 'title' => 'Home Automation', 'sel' => array( 'dept' => 'home-automation' ) ),
			array( 'title' => 'Office Automation Solutions', 'sel' => array( 'dept' => 'office-automation' ) ),
			array( 'title' => 'Networking Solutions', 'sel' => array( 'dept' => 'networking-solutions' ) ),
		);
	}

	/**
	 * The term a selection points at, or null.
	 *
	 * @param array $sel Selection.
	 */
	private static function term( array $sel ): ?WP_Term {
		if ( ! empty( $sel['dept'] ) ) {
			$term = get_term_by( 'slug', $sel['dept'], Star_Electric_Taxonomies::DEPARTMENT );
		} elseif ( ! empty( $sel['sub'] ) ) {
			$term = get_term_by( 'slug', $sel['sub'], 'product_cat' );
		} elseif ( ! empty( $sel['cat'] ) ) {
			$term = get_term_by( 'slug', $sel['cat'], 'product_cat' );
		} else {
			return null;
		}

		return $term instanceof WP_Term ? $term : null;
	}

	/**
	 * Where a selection links to.
	 *
	 * @param array $sel Selection.
	 */
	public static function url( array $sel ): string {
		$term = self::term( $sel );

		if ( ! empty( $sel['query'] ) ) {
			$base = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
			return add_query_arg(
				array(
					's'         => rawurlencode( (string) $sel['query'] ),
					'post_type' => 'product',
					'cat'       => $sel['cat'] ?? '',
				),
				$base
			);
		}

		if ( $term ) {
			$link = get_term_link( $term );
			if ( ! is_wp_error( $link ) ) {
				return (string) $link;
			}
		}

		return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	}

	/**
	 * How many products a selection holds.
	 *
	 * Term counts answer this for free. Only the one search-backed selection
	 * needs a query, and that result is cached with the rest.
	 *
	 * @param array $sel Selection.
	 */
	public static function count( array $sel ): int {
		$term = self::term( $sel );

		if ( empty( $sel['query'] ) ) {
			return $term ? (int) $term->count : 0;
		}

		$cache = get_transient( self::COUNT_KEY );
		$cache = is_array( $cache ) ? $cache : array();
		$key   = md5( wp_json_encode( $sel ) );

		if ( isset( $cache[ $key ] ) ) {
			return (int) $cache[ $key ];
		}

		$args = array(
			'post_type'              => 'product',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			's'                      => (string) $sel['query'],
			'fields'                 => 'ids',
			'no_found_rows'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);
		if ( $term ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => $term->taxonomy,
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				),
			);
		}

		$query           = new WP_Query( $args );
		$cache[ $key ]   = (int) $query->found_posts;
		set_transient( self::COUNT_KEY, $cache, self::COUNT_TTL );

		return (int) $cache[ $key ];
	}

	/**
	 * How many ranges sit behind a selection.
	 *
	 * Some departments - Industrial Control, Power & Energy, Earthing Material -
	 * are published by their sources at range level only. The strip shows the
	 * range count for those rather than a bare zero.
	 *
	 * @param array $sel Selection.
	 */
	public static function range_count( array $sel ): int {
		$term = self::term( $sel );
		if ( ! $term || ! post_type_exists( Star_Electric_Ranges::POST_TYPE ) ) {
			return 0;
		}

		$args = array(
			'post_type'              => Star_Electric_Ranges::POST_TYPE,
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		if ( Star_Electric_Taxonomies::DEPARTMENT === $term->taxonomy ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => $term->taxonomy,
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				),
			);
		} else {
			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'key'   => '_star_electric_category',
					'value' => $term->slug,
				),
			);
		}

		$query = new WP_Query( $args );
		return (int) $query->found_posts;
	}

	/**
	 * The products behind a selection, for a homepage rail.
	 *
	 * @param array $sel   Selection.
	 * @param int   $limit How many.
	 * @return WC_Product[]
	 */
	public static function products( array $sel, int $limit = 10 ): array {
		if ( ! function_exists( 'wc_get_products' ) ) {
			return array();
		}

		$args = array(
			'status'  => 'publish',
			'limit'   => $limit,
			'orderby' => 'ID',
			'order'   => 'ASC',
		);

		$term = self::term( $sel );
		if ( $term ) {
			if ( 'product_cat' === $term->taxonomy ) {
				$args['category'] = array( $term->slug );
			} else {
				$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
					array(
						'taxonomy' => $term->taxonomy,
						'field'    => 'term_id',
						'terms'    => $term->term_id,
					),
				);
			}
		}
		if ( ! empty( $sel['query'] ) ) {
			$args['s'] = (string) $sel['query'];
		}

		return (array) wc_get_products( $args );
	}

	/**
	 * Drop the cached counts.
	 */
	public static function flush(): void {
		delete_transient( self::COUNT_KEY );
	}
}
