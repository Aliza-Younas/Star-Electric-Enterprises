<?php
/**
 * Catalogue rails and range panels.
 *
 * The homepage shows ten product rails, a department strip and a deals rail.
 * None of that may be hardcoded into Elementor: the moment a price changes or a
 * product goes out of stock, a pasted card is a lie. These shortcodes query
 * WooCommerce live and render the approved card markup, so the page stays
 * editable in Elementor while the data stays real.
 *
 *   [star_products category="lighting" limit="10" title="LED Lighting"]
 *   [star_products department="home-automation" limit="10"]
 *   [star_products brand="aqua-electrical" limit="10"]
 *   [star_products on_sale="yes" limit="10" title="Discounted at source"]
 *   [star_departments]
 *   [star_ranges brand="ABB Furse"]
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Data-driven catalogue components.
 */
class Star_Electric_Shortcodes {

	/**
	 * Register the shortcodes.
	 */
	public static function init(): void {
		add_shortcode( 'star_products', array( __CLASS__, 'products' ) );
		add_shortcode( 'star_departments', array( __CLASS__, 'departments' ) );
		add_shortcode( 'star_ranges', array( __CLASS__, 'ranges' ) );
	}

	/**
	 * A horizontal rail of live WooCommerce products.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public static function products( $atts ): string {
		if ( ! function_exists( 'wc_get_products' ) ) {
			return '';
		}

		$a = shortcode_atts(
			array(
				'category'   => '',
				'department' => '',
				'brand'      => '',
				'on_sale'    => '',
				'limit'      => 10,
				'title'      => '',
				'view_all'   => '',
			),
			$atts,
			'star_products'
		);

		$args = array(
			'status'  => 'publish',
			'limit'   => max( 1, min( 24, (int) $a['limit'] ) ),
			'orderby' => 'date',
			'order'   => 'DESC',
		);

		if ( '' !== $a['category'] ) {
			$args['category'] = array_map( 'sanitize_title', explode( ',', $a['category'] ) );
		}
		if ( 'yes' === $a['on_sale'] ) {
			$args['include'] = wc_get_product_ids_on_sale();
			if ( empty( $args['include'] ) ) {
				return '';
			}
		}

		$tax_query = array();
		if ( '' !== $a['department'] ) {
			$tax_query[] = array(
				'taxonomy' => Star_Electric_Taxonomies::DEPARTMENT,
				'field'    => 'slug',
				'terms'    => array_map( 'sanitize_title', explode( ',', $a['department'] ) ),
			);
		}
		if ( '' !== $a['brand'] ) {
			$tax_query[] = array(
				'taxonomy' => Star_Electric_Taxonomies::BRAND,
				'field'    => 'slug',
				'terms'    => array_map( 'sanitize_title', explode( ',', $a['brand'] ) ),
			);
		}
		if ( $tax_query ) {
			$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery
		}

		$products = wc_get_products( $args );
		if ( empty( $products ) ) {
			return '';
		}

		ob_start();
		echo '<section class="rail">';
		if ( '' !== $a['title'] ) {
			echo '<div class="rail__head"><div><h2 class="rail__title">'
				. esc_html( $a['title'] ) . '</h2></div>';
			if ( '' !== $a['view_all'] ) {
				printf(
					'<a class="link-more" href="%s">%s</a>',
					esc_url( $a['view_all'] ),
					esc_html__( 'View all', 'star-electric' )
				);
			}
			echo '</div>';
		}
		echo '<ul class="rail__track">';
		foreach ( $products as $product ) {
			self::card( $product );
		}
		echo '</ul></section>';

		return (string) ob_get_clean();
	}

	/**
	 * One product card, matching the approved storefront card exactly.
	 *
	 * @param WC_Product $product Product.
	 */
	private static function card( WC_Product $product ): void {
		$quote = class_exists( 'Star_Electric_Quote_Only' )
			&& Star_Electric_Quote_Only::is_quote( $product );
		$link  = get_permalink( $product->get_id() );
		$brand = wp_get_object_terms(
			$product->get_id(),
			Star_Electric_Taxonomies::BRAND,
			array( 'fields' => 'names' )
		);

		echo '<li class="mcard">';
		printf( '<a class="mcard__media" href="%s" tabindex="-1" aria-hidden="true">', esc_url( $link ) );
		echo $product->get_image( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</a><div class="mcard__body">';
		printf(
			'<h3 class="mcard__name"><a href="%s">%s</a></h3>',
			esc_url( $link ),
			esc_html( $product->get_name() )
		);
		if ( ! is_wp_error( $brand ) && ! empty( $brand ) ) {
			printf(
				'<p class="mcard__brand">%s %s</p>',
				esc_html__( 'By:', 'star-electric' ),
				esc_html( $brand[0] )
			);
		}

		echo '<p class="mcard__price">';
		if ( $quote ) {
			echo '<span class="mcard__quote"><b>' . esc_html__( 'Request a Quote', 'star-electric' )
				. '</b><span>' . esc_html__( 'Priced on enquiry', 'star-electric' ) . '</span></span>';
		} else {
			echo wp_kses_post( $product->get_price_html() );
		}
		echo '</p><div class="mcard__actions">';

		if ( $quote ) {
			printf(
				'<a class="mcard__btn mcard__btn--quote" href="%s">%s</a>',
				esc_url( Star_Electric_Quote_Only::quote_url( $product ) ),
				esc_html__( 'Request Quote', 'star-electric' )
			);
		} elseif ( ! $product->is_in_stock() ) {
			printf(
				'<button class="mcard__btn mcard__btn--primary" type="button" disabled>%s</button>',
				esc_html__( 'Out of Stock', 'star-electric' )
			);
		} elseif ( $product->is_type( 'variable' ) ) {
			printf(
				'<a class="mcard__btn mcard__btn--primary" href="%s">%s</a>',
				esc_url( $link ),
				esc_html__( 'Select Options', 'star-electric' )
			);
		} else {
			printf(
				'<a class="mcard__btn mcard__btn--primary add_to_cart_button ajax_add_to_cart" href="%s" data-product_id="%d" data-quantity="1" rel="nofollow">%s</a>',
				esc_url( $product->add_to_cart_url() ),
				(int) $product->get_id(),
				esc_html__( 'Add to Cart', 'star-electric' )
			);
		}

		printf(
			'<a class="mcard__btn mcard__btn--ghost" href="%s"><span>%s</span></a>',
			esc_url( $link ),
			esc_html__( 'View', 'star-electric' )
		);
		echo '</div></div></li>';
	}

	/**
	 * The circular department strip.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public static function departments( $atts ): string {
		unset( $atts );

		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'parent'     => 0,
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return '';
		}

		ob_start();
		echo '<ul class="dept-track">';
		foreach ( $terms as $term ) {
			$thumb = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
			printf( '<li class="dept"><a href="%s">', esc_url( (string) get_term_link( $term ) ) );
			echo '<span class="dept__disc">';
			if ( $thumb ) {
				echo wp_get_attachment_image( $thumb, 'woocommerce_thumbnail', false, array( 'alt' => '' ) );
			}
			echo '</span>';
			printf( '<span class="dept__name">%s</span>', esc_html( $term->name ) );
			echo '</a></li>';
		}
		echo '</ul>';

		return (string) ob_get_clean();
	}

	/**
	 * Range panels - navigation entities, never products.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public static function ranges( $atts ): string {
		$a = shortcode_atts(
			array(
				'brand'      => '',
				'department' => '',
				'limit'      => 12,
			),
			$atts,
			'star_ranges'
		);

		$args = array(
			'post_type'      => Star_Electric_Ranges::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => max( 1, min( 60, (int) $a['limit'] ) ),
		);

		if ( '' !== $a['brand'] ) {
			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'key'   => '_star_electric_brand',
					'value' => sanitize_text_field( $a['brand'] ),
				),
			);
		}
		if ( '' !== $a['department'] ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => Star_Electric_Taxonomies::DEPARTMENT,
					'field'    => 'slug',
					'terms'    => sanitize_title( $a['department'] ),
				),
			);
		}

		$q = new WP_Query( $args );
		if ( ! $q->have_posts() ) {
			return '';
		}

		ob_start();
		echo '<div class="family-panel"><ul class="family-list">';
		while ( $q->have_posts() ) {
			$q->the_post();
			$id = (int) get_the_ID();

			echo '<li class="family-item"><span class="family-item__media">';
			if ( has_post_thumbnail( $id ) ) {
				echo get_the_post_thumbnail( $id, 'woocommerce_thumbnail', array( 'alt' => '' ) );
			}
			echo '</span><span class="family-item__body">';
			printf( '<span class="family-item__name">%s</span>', esc_html( get_the_title() ) );
			printf(
				'<span class="family-item__series">%s</span>',
				esc_html( (string) get_post_meta( $id, '_star_electric_series', true ) )
			);
			printf(
				'<a class="family-item__cta" href="%s">%s</a>',
				esc_url( Star_Electric_Ranges::enquiry_url( $id ) ),
				esc_html__( 'Ask about this range', 'star-electric' )
			);
			echo '</span></li>';
		}
		wp_reset_postdata();
		echo '</ul></div>';

		return (string) ob_get_clean();
	}
}
