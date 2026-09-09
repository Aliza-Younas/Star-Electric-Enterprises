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

		if ( ! $term ) {
			return 0;
		}

		$cache = get_transient( self::COUNT_KEY );
		$cache = is_array( $cache ) ? $cache : array();
		$key   = md5( wp_json_encode( $sel ) );

		if ( isset( $cache[ $key ] ) ) {
			return (int) $cache[ $key ];
		}

		if ( ! empty( $sel['query'] ) ) {
			$query = new WP_Query(
				array(
					'post_type'              => 'product',
					'post_status'            => 'publish',
					'posts_per_page'         => 1,
					's'                      => (string) $sel['query'],
					'fields'                 => 'ids',
					'no_found_rows'          => false,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
					'tax_query'              => array( // phpcs:ignore WordPress.DB.SlowDBQuery
						array(
							'taxonomy' => $term->taxonomy,
							'field'    => 'term_id',
							'terms'    => $term->term_id,
						),
					),
				)
			);
			$found = (int) $query->found_posts;
		} else {
			/*
			 * Not $term->count. A term's own count is every object attached to
			 * it, and the department taxonomy also carries ranges - the two
			 * networking ranges turned the approved page's "4" into "6". The
			 * rail counts what the rail can show, which is products.
			 */
			global $wpdb;

			// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$found = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*)
					   FROM {$wpdb->posts} p
					   INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
					  WHERE p.post_type = 'product'
					    AND p.post_status = 'publish'
					    AND tr.term_taxonomy_id = %d",
					(int) $term->term_taxonomy_id
				)
			);
			// phpcs:enable
		}

		$cache[ $key ] = $found;
		set_transient( self::COUNT_KEY, $cache, self::COUNT_TTL );

		return $found;
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
	 * Every published product in one term, in the approved storefront's order.
	 *
	 * data.js sorts a selection by a score before anything else looks at it:
	 * four points for a published price, two for stock the source actually
	 * confirms, two for a photograph of the item itself and one for a genuine
	 * source discount. Products that tie keep catalogue order, which the import
	 * file preserves, so ascending post id is the same tie-break here.
	 *
	 * The score is four meta values, so it is computed in the query rather than
	 * by loading a department's worth of products into memory to sort them: the
	 * Wires and Cables rail selects from 2,390 records.
	 *
	 * @param WP_Term $term Term the rail selects.
	 * @return int[] Post ids, best first.
	 */
	private static function ranked( WP_Term $term ): array {
		global $wpdb;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT p.ID
				   FROM {$wpdb->posts} p
				   INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
				   LEFT JOIN {$wpdb->postmeta} qm ON qm.post_id = p.ID AND qm.meta_key = %s
				   LEFT JOIN {$wpdb->postmeta} av ON av.post_id = p.ID AND av.meta_key = '_star_electric_availability'
				   LEFT JOIN {$wpdb->postmeta} it ON it.post_id = p.ID AND it.meta_key = '_star_electric_image_type'
				   LEFT JOIN {$wpdb->postmeta} dc ON dc.post_id = p.ID AND dc.meta_key = '_star_electric_discount'
				  WHERE p.post_type = 'product'
				    AND p.post_status = 'publish'
				    AND tr.term_taxonomy_id = %d
				  ORDER BY ( CASE WHEN qm.meta_value = 'no' THEN 4 ELSE 0 END
				           + CASE WHEN av.meta_value = 'In Stock' THEN 2 ELSE 0 END
				           + CASE WHEN it.meta_value = 'exact-image' THEN 2 ELSE 0 END
				           + CASE WHEN dc.meta_value + 0 > 0 THEN 1 ELSE 0 END ) DESC,
				           p.ID ASC",
				STAR_ELECTRIC_QUOTE_META,
				(int) $term->term_taxonomy_id
			)
		);
		// phpcs:enable

		return array_map( 'intval', (array) $ids );
	}

	/**
	 * Which subcategory each product sits in, for one department.
	 *
	 * One query for the whole department rather than one per product. A record
	 * carries a single subcategory, so the last row wins if a product were ever
	 * filed under two - which the importer does not do.
	 *
	 * @param int $parent_id Department term id.
	 * @return array<int,string> Post id => subcategory slug.
	 */
	private static function subcategory_map( int $parent_id ): array {
		global $wpdb;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT tr.object_id AS id, t.slug AS slug
				   FROM {$wpdb->term_relationships} tr
				   INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
				   INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
				  WHERE tt.taxonomy = 'product_cat'
				    AND tt.parent = %d",
				$parent_id
			)
		);
		// phpcs:enable

		$map = array();
		foreach ( (array) $rows as $row ) {
			$map[ (int) $row->id ] = (string) $row->slug;
		}

		return $map;
	}

	/**
	 * A department's rail: spread across its subcategories.
	 *
	 * spreadProducts() in the approved data.js. Group the ranked selection by
	 * subcategory, keeping the order the groups first appear in; put the
	 * biggest group first; then take one product from each group in rotation
	 * until the rail is full. Products filed straight on the department and not
	 * in any subcategory are a group of their own, keyed "_" there and here.
	 *
	 * Without this a rail is just the top of the department, which is why the
	 * Wires and Cables rail used to open on four near-identical PVC boxes.
	 *
	 * @param WP_Term $term  Department term.
	 * @param int     $limit How many products the rail shows.
	 * @return int[] Post ids.
	 */
	private static function spread_ids( WP_Term $term, int $limit ): array {
		$ranked = self::ranked( $term );
		if ( count( $ranked ) <= $limit ) {
			return array_slice( $ranked, 0, $limit );
		}

		$subs   = self::subcategory_map( $term->term_id );
		$groups = array();
		foreach ( $ranked as $id ) {
			$key = $subs[ $id ] ?? '_';
			if ( ! isset( $groups[ $key ] ) ) {
				$groups[ $key ] = array();
			}
			$groups[ $key ][] = $id;
		}

		/*
		 * Biggest group leads. Groups of the same size keep the order they
		 * first appeared in, which is what the approved code's stable sort
		 * gives - so the index is carried into the comparison rather than
		 * trusted to usort, which is only stable from PHP 8.
		 */
		$order = array_keys( $groups );
		$rank  = array_flip( $order );
		usort(
			$order,
			static function ( string $a, string $b ) use ( $groups, $rank ): int {
				$size = count( $groups[ $b ] ) <=> count( $groups[ $a ] );
				return 0 !== $size ? $size : ( $rank[ $a ] <=> $rank[ $b ] );
			}
		);

		$ids = array();
		for ( $round = 0; count( $ids ) < $limit; $round++ ) {
			$added = false;
			foreach ( $order as $key ) {
				if ( count( $ids ) >= $limit ) {
					break;
				}
				if ( isset( $groups[ $key ][ $round ] ) ) {
					$ids[] = $groups[ $key ][ $round ];
					$added = true;
				}
			}
			if ( ! $added ) {
				break;
			}
		}

		return $ids;
	}

	/**
	 * The products behind a selection, for a homepage rail.
	 *
	 * The result is cached against the term's own product count, so importing
	 * or removing products retires the cache without anything having to
	 * remember to clear it.
	 *
	 * @param array $sel   Selection.
	 * @param int   $limit How many.
	 * @return WC_Product[]
	 */
	public static function products( array $sel, int $limit = 10 ): array {
		if ( ! function_exists( 'wc_get_products' ) ) {
			return array();
		}

		$term = self::term( $sel );

		/*
		 * A search-backed selection is the one rail shape the ranking query
		 * cannot express, so it stays with WooCommerce's own search.
		 */
		if ( ! $term || ! empty( $sel['query'] ) ) {
			$args = array(
				'status'  => 'publish',
				'limit'   => $limit,
				'orderby' => 'ID',
				'order'   => 'ASC',
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
			if ( ! empty( $sel['query'] ) ) {
				$args['s'] = (string) $sel['query'];
			}
			return (array) wc_get_products( $args );
		}

		$key    = 'star_rail_' . md5( $term->taxonomy . '|' . $term->term_id . '|' . $limit . '|' . (int) $term->count );
		$cached = get_transient( $key );

		if ( is_array( $cached ) ) {
			$ids = $cached;
		} else {
			/*
			 * Only a whole department is spread. A subcategory, or a department
			 * taxonomy view that crosses categories, has no subcategories of its
			 * own to rotate through and simply takes the top of the ranking -
			 * exactly what spreadProducts() does with the same selection.
			 */
			$spreadable = 'product_cat' === $term->taxonomy && 0 === (int) $term->parent;
			$ids        = $spreadable
				? self::spread_ids( $term, $limit )
				: array_slice( self::ranked( $term ), 0, $limit );

			set_transient( $key, $ids, DAY_IN_SECONDS );
		}

		$products = array();
		foreach ( $ids as $id ) {
			$product = wc_get_product( $id );
			if ( $product instanceof WC_Product && $product->is_visible() ) {
				$products[] = $product;
			}
		}

		return $products;
	}

	/**
	 * Where a brand tile sends the shopper.
	 *
	 * The approved brand directory links to the shop filtered by that brand,
	 * not to a taxonomy archive - so the shopper lands somewhere they can
	 * filter further rather than in a dead end. The term archive still exists
	 * and still resolves; nothing links to it by default.
	 *
	 * @param WP_Term $term Brand term.
	 */
	public static function brand_url( WP_Term $term ): string {
		$shop = wc_get_page_permalink( 'shop' );
		if ( ! $shop ) {
			$shop = home_url( '/shop/' );
		}
		return add_query_arg( 'brand', rawurlencode( $term->slug ), $shop );
	}

	/**
	 * The products shown beneath a product page.
	 *
	 * The approved page's rule, in its order: everything in the same
	 * subcategory first, then the rest of the department, then anything else
	 * from the same brand - each in catalogue order, which is ascending post id
	 * here. WooCommerce's own wc_get_related_products() picks at random from
	 * shared categories and tags, so it returned a different four on every
	 * cache miss and never matched the approved page.
	 *
	 * @param int $product_id Product being viewed.
	 * @param int $limit      How many to return.
	 * @return int[] Post ids.
	 */
	public static function related( int $product_id, int $limit = 4 ): array {
		$terms = get_the_terms( $product_id, 'product_cat' );
		$terms = is_array( $terms ) ? $terms : array();

		$parent = 0;
		$child  = 0;
		foreach ( $terms as $term ) {
			if ( 0 === (int) $term->parent ) {
				$parent = (int) $term->term_id;
			} else {
				$child = (int) $term->term_id;
			}
		}

		$brands = get_the_terms( $product_id, Star_Electric_Taxonomies::BRAND );
		$brand  = ( is_array( $brands ) && $brands ) ? (int) $brands[0]->term_id : 0;

		$pick = static function ( array $tax_query ) use ( $product_id, $limit ): array {
			return array_map(
				'intval',
				(array) get_posts(
					array(
						'post_type'      => 'product',
						'post_status'    => 'publish',
						'posts_per_page' => $limit + 1,
						'fields'         => 'ids',
						'orderby'        => 'ID',
						'order'          => 'ASC',
						'exclude'        => array( $product_id ),
						'tax_query'      => $tax_query, // phpcs:ignore WordPress.DB.SlowDBQuery
					)
				)
			);
		};

		$out = array();

		$rounds = array();
		if ( $child && $parent ) {
			$rounds[] = array(
				'relation' => 'AND',
				array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $child, 'include_children' => false ),
				array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $parent, 'include_children' => false ),
			);
		}
		if ( $parent ) {
			$rounds[] = array(
				array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $parent, 'include_children' => false ),
			);
		}
		if ( $brand ) {
			$rounds[] = array(
				array( 'taxonomy' => Star_Electric_Taxonomies::BRAND, 'field' => 'term_id', 'terms' => $brand ),
			);
		}

		foreach ( $rounds as $tax_query ) {
			if ( count( $out ) >= $limit ) {
				break;
			}
			foreach ( $pick( $tax_query ) as $id ) {
				if ( count( $out ) >= $limit ) {
					break;
				}
				if ( ! in_array( $id, $out, true ) ) {
					$out[] = $id;
				}
			}
		}

		return $out;
	}

	/**
	 * Products a source has genuinely reduced, biggest reduction first.
	 *
	 * The approved homepage takes every product carrying a discount and sorts
	 * by the size of it; equal reductions keep catalogue order. Ordering by the
	 * meta alone left the ties in whatever order MySQL returned them, so the
	 * id is the explicit second key here.
	 *
	 * The discount meta is written on every product at import, including 0, so
	 * "> 0" is the whole test - there is no separate on-sale flag to trust.
	 *
	 * @param int $limit How many to take, or 0 for all of them.
	 * @return int[] Post ids.
	 */
	public static function deal_ids( int $limit = 0 ): array {
		global $wpdb;

		$sql = "SELECT p.ID
				  FROM {$wpdb->posts} p
				  INNER JOIN {$wpdb->postmeta} dc ON dc.post_id = p.ID AND dc.meta_key = '_star_electric_discount'
				 WHERE p.post_type = 'product'
				   AND p.post_status = 'publish'
				   AND dc.meta_value + 0 > 0
				 ORDER BY dc.meta_value + 0 DESC, p.ID ASC";

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$ids = $limit > 0
			? $wpdb->get_col( $wpdb->prepare( $sql . ' LIMIT %d', $limit ) )
			: $wpdb->get_col( $sql );
		// phpcs:enable

		return array_map( 'intval', (array) $ids );
	}

	/**
	 * Drop the cached counts.
	 */
	public static function flush(): void {
		delete_transient( self::COUNT_KEY );
	}
}
