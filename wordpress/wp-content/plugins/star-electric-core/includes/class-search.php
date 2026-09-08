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
			$join .= " LEFT JOIN {$wpdb->postmeta} AS star_search_meta ON {$wpdb->posts}.ID = star_search_meta.post_id ";
		}

		return $join;
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

		$keys = implode( ',', array_map( static function ( $k ) use ( $wpdb ) {
			return $wpdb->prepare( '%s', $k );
		}, self::KEYS ) );

		$meta = $wpdb->prepare(
			" OR ( star_search_meta.meta_key IN ( {$keys} ) AND star_search_meta.meta_value LIKE %s ) ",
			$like
		);

		// Splice the extra condition inside the existing parenthesised group.
		return preg_replace( '/\)\s*$/', $meta . ')', $search, 1 ) ?: $search;
	}
}
