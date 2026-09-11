<?php
/**
 * Catalogue search.
 *
 * The static site searched name, model, SKU, brand, series, category and the
 * specification text. Core WordPress search only looks at the title, excerpt
 * and content, so a shopper searching a model number or a brand would find
 * nothing. This widens the query to the meta the importer stores.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widens product search to catalogue metadata.
 */
class Star_Electric_Search {

	/** Meta keys worth searching. */
	private const KEYS = array(
		'_sku',
		'_star_electric_model',
		'_star_electric_series',
		'_star_electric_specifications',
	);

	/**
	 * Hook the query filters.
	 */
	public static function init(): void {
		add_filter( 'posts_search', array( __CLASS__, 'search' ), 20, 2 );
		add_filter( 'posts_distinct', array( __CLASS__, 'distinct' ), 20, 2 );
		add_filter( 'posts_join', array( __CLASS__, 'join' ), 20, 2 );
	}

	/**
	 * Should this query be widened?
	 *
	 * @param WP_Query $query Query.
	 */
	private static function applies( $query ): bool {
		return $query instanceof WP_Query
			&& $query->is_search()
			&& $query->is_main_query()
			&& ! is_admin()
			&& '' !== (string) $query->get( 's' );
	}

	/**
	 * Join the meta table once.
	 *
	 * @param string   $join  JOIN clause.
	 * @param WP_Query $query Query.
	 */
	public static function join( $join, $query ) {
		global $wpdb;

		if ( self::applies( $query ) && false === strpos( $join, 'star_search_meta' ) ) {
			/*
			 * The key filter belongs in the ON clause, not the WHERE. Joined
			 * without it, every meta row of every product is brought back and
			 * then thrown away - on a 4,348 product catalogue that is the whole
			 * postmeta table. Restricted here, the join carries at most four
			 * rows per product.
			 */
			$join .= " LEFT JOIN {$wpdb->postmeta} AS star_search_meta"
				. " ON {$wpdb->posts}.ID = star_search_meta.post_id"
				. ' AND star_search_meta.meta_key IN ( ' . self::keys() . ' ) ';
		}

		return $join;
	}

	/**
	 * The searchable meta keys, as a quoted SQL list.
	 */
	private static function keys(): string {
		global $wpdb;

		return implode(
			',',
			array_map(
				static function ( $key ) use ( $wpdb ) {
					return $wpdb->prepare( '%s', $key );
				},
				self::KEYS
			)
		);
	}

	/**
	 * The join can multiply rows, so ask for distinct posts.
	 *
	 * @param string   $distinct DISTINCT clause.
	 * @param WP_Query $query    Query.
	 */
	public static function distinct( $distinct, $query ) {
		return self::applies( $query ) ? 'DISTINCT' : $distinct;
	}

	/**
	 * Add the meta match to the search clause.
	 *
	 * @param string   $search Search SQL.
	 * @param WP_Query $query  Query.
	 */
	public static function search( $search, $query ) {
		global $wpdb;

		if ( ! self::applies( $query ) || '' === $search ) {
			return $search;
		}

		$term = (string) $query->get( 's' );
		$like = '%' . $wpdb->esc_like( $term ) . '%';

		$meta = $wpdb->prepare( ' OR ( star_search_meta.meta_value LIKE %s ) ', $like );

		/*
		 * WordPress hands this filter the whole clause, and for a visitor who is
		 * not logged in that clause ends with its own group:
		 *
		 *   AND ( (post_title LIKE ..) OR (post_excerpt ..) OR (post_content ..) )
		 *   AND ( wp_posts.post_password = '' )
		 *
		 * Splicing at the last ")" therefore widened the *password* test rather
		 * than the search, so the title group still gated every row and a search
		 * for a SKU or a model number found nothing. Logged in there is no
		 * password clause, the splice landed correctly, and the fault was
		 * invisible to anyone testing from the dashboard.
		 *
		 * So the password clause is set aside, the meta condition goes into the
		 * group it belongs to, and the clause is put back together.
		 */
		$tail = '';
		$pos  = strpos( $search, $wpdb->posts . '.post_password' );
		if ( false !== $pos ) {
			$open = strrpos( substr( $search, 0, $pos ), ' AND (' );
			if ( false !== $open ) {
				$tail   = substr( $search, $open );
				$search = substr( $search, 0, $open );
			}
		}

		$widened = preg_replace( '/\)\s*$/', $meta . ')', $search, 1 );

		return ( null === $widened ? $search : $widened ) . $tail;
	}
}
