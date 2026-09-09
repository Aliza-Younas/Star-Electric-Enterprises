<?php
/**
 * Brand and department taxonomies.
 *
 * The static catalogue let one product belong to several departments at once -
 * an Aqua Wi-Fi switch is Switches & Sockets, Smart Switches, Home Automation
 * and Office Automation Solutions simultaneously - without a duplicate record
 * existing anywhere. WordPress taxonomies model that natively, so the
 * relationship survives the migration exactly: one product, many terms, no
 * duplication.
 *
 * Brand is its own taxonomy rather than a product category, so a brand archive
 * never competes with the category tree and no product is duplicated per brand.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the catalogue's non-WooCommerce taxonomies.
 */
class Star_Electric_Taxonomies {

	public const BRAND      = 'star_brand';
	public const DEPARTMENT = 'star_department';

	/**
	 * Hook registration.
	 */
	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'register' ), 5 );
	}

	/**
	 * Register both taxonomies against products.
	 */
	public static function register(): void {
		register_taxonomy(
			self::BRAND,
			array( 'product' ),
			array(
				'labels'            => array(
					'name'          => __( 'Brands', 'star-electric' ),
					'singular_name' => __( 'Brand', 'star-electric' ),
					'menu_name'     => __( 'Brands', 'star-electric' ),
					'search_items'  => __( 'Search brands', 'star-electric' ),
					'all_items'     => __( 'All brands', 'star-electric' ),
					'edit_item'     => __( 'Edit brand', 'star-electric' ),
					'add_new_item'  => __( 'Add brand', 'star-electric' ),
				),
				'public'            => true,
				'hierarchical'      => false,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'query_var'         => true,
				/*
				 * Not "brand". WooCommerce registers its own product_brand
				 * taxonomy on that base, and whichever loses the race serves a
				 * 404 - which is what /brand/aqua/ was doing while the brand
				 * directory linked to it.
				 */
				'rewrite'           => array(
					'slug'       => 'product-brand',
					'with_front' => false,
				),
			)
		);

		register_taxonomy(
			self::DEPARTMENT,
			array( 'product', Star_Electric_Ranges::POST_TYPE ),
			array(
				'labels'            => array(
					'name'          => __( 'Departments', 'star-electric' ),
					'singular_name' => __( 'Department', 'star-electric' ),
					'menu_name'     => __( 'Departments', 'star-electric' ),
				),
				/*
				 * A department is a merchandising grouping that cuts across the
				 * category tree, which is exactly why it is flat and separate
				 * from product_cat rather than folded into it.
				 */
				'public'            => true,
				'hierarchical'      => false,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'query_var'         => true,
				'rewrite'           => array(
					'slug'       => 'department',
					'with_front' => false,
				),
			)
		);
	}

	/**
	 * Put terms back into the catalogue's own order.
	 *
	 * The importer records each term's position in the payload. A term without
	 * one sorts last rather than disappearing, which is why this is a sort over
	 * a fetched list and not an ordered query - a meta_key query silently drops
	 * every term that has no such meta.
	 *
	 * @param WP_Term[] $terms Terms.
	 * @return WP_Term[]
	 */
	public static function in_catalogue_order( array $terms ): array {
		usort(
			$terms,
			static function ( WP_Term $a, WP_Term $b ): int {
				$oa = get_term_meta( $a->term_id, '_star_electric_order', true );
				$ob = get_term_meta( $b->term_id, '_star_electric_order', true );
				$oa = '' === $oa ? PHP_INT_MAX : (int) $oa;
				$ob = '' === $ob ? PHP_INT_MAX : (int) $ob;
				return $oa === $ob ? strcmp( $a->name, $b->name ) : ( $oa <=> $ob );
			}
		);
		return $terms;
	}

	/**
	 * Human label for a department slug.
	 *
	 * @param string $slug Department slug.
	 */
	public static function department_label( string $slug ): string {
		$known = array(
			'networking-solutions' => __( 'Networking Solutions', 'star-electric' ),
			'home-automation'      => __( 'Home Automation', 'star-electric' ),
			'office-automation'    => __( 'Office Automation Solutions', 'star-electric' ),
			'earthing-material'    => __( 'Earthing Material', 'star-electric' ),
		);

		return $known[ $slug ] ?? ucwords( str_replace( '-', ' ', $slug ) );
	}
}
