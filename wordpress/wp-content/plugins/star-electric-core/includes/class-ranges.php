<?php
/**
 * Product ranges and families.
 *
 * 181 records in the catalogue are ranges, not products: ABB Furse's three
 * Furse ranges, Himel's 132 series, Hyundai's eight families, Electro Traders'
 * 32 ranges and a handful of Pakistan Cables entries. Their sources publish a
 * range name and nothing item-level - no model, no specification, no price.
 *
 * Turning them into WooCommerce products would mean inventing a SKU and a price
 * for something the manufacturer never published, so they are a separate post
 * type instead. They can be browsed, filtered by brand and department and
 * enquired about, and they can never be added to a cart because they are not
 * products at all.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The range/family content type.
 */
class Star_Electric_Ranges {

	public const POST_TYPE = 'star_range';

	/**
	 * Hook registration.
	 */
	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'register' ), 4 );
	}

	/**
	 * Register the post type.
	 */
	public static function register(): void {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'        => array(
					'name'          => __( 'Product Ranges', 'star-electric' ),
					'singular_name' => __( 'Product Range', 'star-electric' ),
					'menu_name'     => __( 'Product Ranges', 'star-electric' ),
					'add_new_item'  => __( 'Add range', 'star-electric' ),
					'edit_item'     => __( 'Edit range', 'star-electric' ),
					'search_items'  => __( 'Search ranges', 'star-electric' ),
				),
				'public'        => true,
				'has_archive'   => 'ranges',
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-portfolio',
				'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
				'rewrite'       => array(
					'slug'       => 'range',
					'with_front' => false,
				),
				'taxonomies'    => array( Star_Electric_Taxonomies::DEPARTMENT ),
				'capabilities'  => array(
					// Ranges are catalogue data, not something an editor drafts by hand.
					'create_posts' => 'manage_woocommerce',
				),
				'map_meta_cap'  => true,
			)
		);
	}

	/**
	 * The enquiry URL for a range.
	 *
	 * A range has no SKU, so the form is prefilled by name and post ID only.
	 *
	 * @param int $post_id Range post ID.
	 */
	public static function enquiry_url( int $post_id ): string {
		/*
		 * The page is 'quote-request'. Looking it up as 'request-a-quote'
		 * found nothing and fell through to a URL that has never existed,
		 * so every Request a Quote button on a quote-only product and on a
		 * range landed on a 404 - the one route those products have.
		 */
		$page = get_page_by_path( 'quote-request' );
		$base = $page ? get_permalink( $page ) : home_url( '/quote-request/' );

		return add_query_arg(
			array(
				'range'      => $post_id,
				'range_name' => rawurlencode( get_the_title( $post_id ) ),
			),
			$base
		);
	}
}
