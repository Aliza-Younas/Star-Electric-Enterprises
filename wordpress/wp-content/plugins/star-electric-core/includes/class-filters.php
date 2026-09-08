<?php
/**
 * Catalogue filtering and sorting.
 *
 * The approved shop filters on six groups - categories, brands, price,
 * availability, how the product is sold, and product type - and offers five
 * sorts. None of that is WooCommerce's default, and all of it is catalogue
 * behaviour rather than presentation, so it lives here and survives a theme
 * change.
 *
 * The query parameter names are the ones the approved storefront already uses,
 * so a link copied from the static site keeps working.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shop filters.
 */
class Star_Electric_Filters {

	/** How long the facet counts stay cached. */
	private const COUNT_TTL = HOUR_IN_SECONDS;

	/** Cache key for the facet counts. */
	private const COUNT_KEY = 'star_electric_filter_counts';

	/** Availability values, as published by the sources. */
	private const AVAILABILITY = array(
		'in'      => 'In Stock',
		'out'     => 'Out of Stock',
		'unknown' => 'Not specified',
	);

	/**
	 * Hook up.
	 */
	public static function init(): void {
		add_action( 'woocommerce_product_query', array( __CLASS__, 'apply' ) );
		add_filter( 'woocommerce_get_catalog_ordering_args', array( __CLASS__, 'ordering' ) );
		add_action( 'save_post_product', array( __CLASS__, 'flush_counts' ) );
		add_action( 'deleted_post', array( __CLASS__, 'flush_counts' ) );
	}

	/**
	 * The sort options the approved shop offers.
	 */
	public static function sorts(): array {
		return array(
			'relevance'  => __( 'Sort: Featured', 'star-electric' ),
			'price-asc'  => __( 'Price: Low to High', 'star-electric' ),
			'price-desc' => __( 'Price: High to Low', 'star-electric' ),
			'name'       => __( 'Name: A to Z', 'star-electric' ),
			'discount'   => __( 'Biggest Discount', 'star-electric' ),
		);
	}

	/**
	 * Read the active filters out of the request.
	 *
	 * @return array<string,mixed>
	 */
	public static function active(): array {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$list = static function ( string $key ): array {
			if ( ! isset( $_GET[ $key ] ) ) {
				return array();
			}
			$raw = wp_unslash( $_GET[ $key ] );
			$raw = is_array( $raw ) ? $raw : explode( ',', (string) $raw );
			// The panel is rendered twice - sidebar and mobile drawer - so the
			// same value can legitimately arrive twice.
			return array_values( array_unique( array_filter( array_map( 'sanitize_title', $raw ) ) ) );
		};

		$number = static function ( string $key ) {
			if ( ! isset( $_GET[ $key ] ) || '' === $_GET[ $key ] ) {
				return null;
			}
			return max( 0, (int) $_GET[ $key ] );
		};

		$sort = isset( $_GET['sort'] ) ? sanitize_key( wp_unslash( $_GET['sort'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		return array(
			'cat'     => $list( 'cat' ),
			'brand'   => $list( 'brand' ),
			'stock'   => array_values( array_intersect( $list( 'stock' ), array_keys( self::AVAILABILITY ) ) ),
			'pricing' => array_values( array_intersect( $list( 'pricing' ), array( 'priced', 'quote', 'discounted' ) ) ),
			'type'    => array_values( array_intersect( $list( 'type' ), array( 'simple', 'variable' ) ) ),
			'min'     => $number( 'min' ),
			'max'     => $number( 'max' ),
			'sort'    => isset( self::sorts()[ $sort ] ) ? $sort : '',
		);
	}

	/**
	 * True when anything is filtering the catalogue right now.
	 */
	public static function is_filtered(): bool {
		$a = self::active();
		unset( $a['sort'] );
		foreach ( $a as $value ) {
			if ( is_array( $value ) ? ! empty( $value ) : null !== $value ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Apply the filters to the shop query.
	 *
	 * @param WP_Query $q Product query.
	 */
	public static function apply( $q ): void {
		if ( ! $q instanceof WP_Query || is_admin() ) {
			return;
		}

		$a   = self::active();
		$tax = (array) $q->get( 'tax_query' );
		$met = (array) $q->get( 'meta_query' );

		if ( $a['cat'] ) {
			$tax[] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $a['cat'],
			);
		}
		if ( $a['brand'] ) {
			$tax[] = array(
				'taxonomy' => Star_Electric_Taxonomies::BRAND,
				'field'    => 'slug',
				'terms'    => $a['brand'],
			);
		}
		if ( $a['type'] ) {
			$tax[] = array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => $a['type'],
			);
		}

		if ( $a['stock'] ) {
			$values = array();
			foreach ( $a['stock'] as $key ) {
				$values[] = self::AVAILABILITY[ $key ];
			}
			$met[] = array(
				'key'     => '_star_electric_availability',
				'value'   => $values,
				'compare' => 'IN',
			);
		}

		/*
		 * "How it is sold". A quote-only product has no _price row at all, so
		 * "has a published price" is an EXISTS test rather than a comparison -
		 * comparing against 0 would be how a quote product becomes free.
		 */
		if ( $a['pricing'] ) {
			$clauses = array( 'relation' => 'OR' );
			foreach ( $a['pricing'] as $mode ) {
				if ( 'priced' === $mode ) {
					$clauses[] = array(
						'key'     => STAR_ELECTRIC_QUOTE_META,
						'value'   => 'no',
						'compare' => '=',
					);
				} elseif ( 'quote' === $mode ) {
					$clauses[] = array(
						'key'     => STAR_ELECTRIC_QUOTE_META,
						'value'   => 'yes',
						'compare' => '=',
					);
				} else {
					$clauses[] = array(
						'key'     => '_star_electric_discount',
						'value'   => 0,
						'type'    => 'NUMERIC',
						'compare' => '>',
					);
				}
			}
			$met[] = $clauses;
		}

		if ( null !== $a['min'] || null !== $a['max'] ) {
			$min   = null === $a['min'] ? 0 : $a['min'];
			$max   = null === $a['max'] ? PHP_INT_MAX : $a['max'];
			$met[] = array(
				'key'     => '_price',
				'value'   => array( $min, $max ),
				'type'    => 'NUMERIC',
				'compare' => 'BETWEEN',
			);
		}

		if ( $tax ) {
			$q->set( 'tax_query', $tax );
		}
		if ( $met ) {
			$q->set( 'meta_query', $met );
		}
	}

	/**
	 * Translate the approved sort options into query arguments.
	 *
	 * @param array $args Ordering args.
	 */
	public static function ordering( array $args ): array {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$sort = isset( $_GET['sort'] ) ? sanitize_key( wp_unslash( $_GET['sort'] ) ) : '';

		switch ( $sort ) {
			case 'price-asc':
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';
				$args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery
				break;
			case 'price-desc':
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				$args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery
				break;
			case 'name':
				$args['orderby']  = 'title';
				$args['order']    = 'ASC';
				$args['meta_key'] = ''; // phpcs:ignore WordPress.DB.SlowDBQuery
				break;
			case 'discount':
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				$args['meta_key'] = '_star_electric_discount'; // phpcs:ignore WordPress.DB.SlowDBQuery
				break;
			case 'relevance':
			default:
				break;
		}

		return $args;
	}

	/**
	 * Drop the cached facet counts.
	 */
	public static function flush_counts(): void {
		delete_transient( self::COUNT_KEY );
	}

	/**
	 * Facet counts for the panel.
	 *
	 * Counted once and cached: six groups over 4,348 products is too much work
	 * to repeat on every page view.
	 *
	 * @return array<string,array<string,int>>
	 */
	public static function counts(): array {
		$cached = get_transient( self::COUNT_KEY );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		global $wpdb;

		$counts = array(
			'stock'   => array(),
			'pricing' => array(),
			'type'    => array(),
		);

		$rows = $wpdb->get_results(
			"SELECT pm.meta_value AS v, COUNT(*) AS n
			 FROM {$wpdb->postmeta} pm
			 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			 WHERE pm.meta_key = '_star_electric_availability'
			   AND p.post_type = 'product' AND p.post_status = 'publish'
			 GROUP BY pm.meta_value"
		);
		$by_value = array();
		foreach ( (array) $rows as $row ) {
			$by_value[ (string) $row->v ] = (int) $row->n;
		}
		foreach ( self::AVAILABILITY as $key => $label ) {
			$counts['stock'][ $key ] = $by_value[ $label ] ?? 0;
		}

		$quote = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->postmeta} pm
				 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				 WHERE pm.meta_key = %s AND pm.meta_value = 'yes'
				   AND p.post_type = 'product' AND p.post_status = 'publish'",
				STAR_ELECTRIC_QUOTE_META
			)
		);
		$priced = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->postmeta} pm
				 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				 WHERE pm.meta_key = %s AND pm.meta_value = 'no'
				   AND p.post_type = 'product' AND p.post_status = 'publish'",
				STAR_ELECTRIC_QUOTE_META
			)
		);
		$discounted = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->postmeta} pm
			 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			 WHERE pm.meta_key = '_star_electric_discount' AND CAST(pm.meta_value AS UNSIGNED) > 0
			   AND p.post_type = 'product' AND p.post_status = 'publish'"
		);

		$counts['pricing'] = array(
			'priced'     => $priced,
			'quote'      => $quote,
			'discounted' => $discounted,
		);

		foreach ( array( 'simple', 'variable' ) as $type ) {
			$term                     = get_term_by( 'slug', $type, 'product_type' );
			$counts['type'][ $type ] = $term instanceof WP_Term ? (int) $term->count : 0;
		}

		set_transient( self::COUNT_KEY, $counts, self::COUNT_TTL );
		return $counts;
	}

	/**
	 * The filter panel, matching the approved storefront exactly.
	 *
	 * @param string $uid Unique prefix, because the panel is rendered twice -
	 *                    once in the sidebar and once in the mobile drawer.
	 */
	public static function render( string $uid = 'f0' ): string {
		$a      = self::active();
		$counts = self::counts();

		ob_start();
		echo '<div class="filters__head"><h2>' . esc_html__( 'Filters', 'star-electric' ) . '</h2>';
		echo '<a class="btn btn--quiet btn--sm" href="' . esc_url( self::clear_url() ) . '" data-clear-all>'
			. esc_html__( 'Clear all', 'star-electric' ) . '</a></div>';

		// Categories.
		$cats = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => 0,
				'hide_empty' => true,
			)
		);
		$body = '';
		foreach ( (array) $cats as $term ) {
			if ( ! $term instanceof WP_Term || 'uncategorized' === $term->slug ) {
				continue;
			}
			$body .= self::option( 'cat', $term->slug, $term->name, (int) $term->count, $a['cat'] );
		}
		self::group( $uid, 'cat', __( 'Categories', 'star-electric' ), $body, true );

		// Brands.
		$brands = get_terms(
			array(
				'taxonomy'   => Star_Electric_Taxonomies::BRAND,
				'hide_empty' => true,
			)
		);
		$body = '';
		foreach ( (array) $brands as $term ) {
			if ( $term instanceof WP_Term ) {
				$body .= self::option( 'brand', $term->slug, $term->name, (int) $term->count, $a['brand'] );
			}
		}
		self::group( $uid, 'brand', __( 'Brands', 'star-electric' ), $body, true );

		// Price.
		ob_start();
		?>
		<div class="price-range">
			<label class="sr-only" for="<?php echo esc_attr( $uid ); ?>-min"><?php esc_html_e( 'Minimum price', 'star-electric' ); ?></label>
			<input class="input" id="<?php echo esc_attr( $uid ); ?>-min" type="number" min="0" name="min"
				placeholder="<?php esc_attr_e( 'Min', 'star-electric' ); ?>" data-filter="min"
				value="<?php echo esc_attr( null === $a['min'] ? '' : (string) $a['min'] ); ?>">
			<span>&ndash;</span>
			<label class="sr-only" for="<?php echo esc_attr( $uid ); ?>-max"><?php esc_html_e( 'Maximum price', 'star-electric' ); ?></label>
			<input class="input" id="<?php echo esc_attr( $uid ); ?>-max" type="number" min="0" name="max"
				placeholder="<?php esc_attr_e( 'Max', 'star-electric' ); ?>" data-filter="max"
				value="<?php echo esc_attr( null === $a['max'] ? '' : (string) $a['max'] ); ?>">
		</div>
		<p class="field__hint" style="margin-top:8px">
			<?php esc_html_e( 'Prices in PKR, exactly as published by each product’s own source.', 'star-electric' ); ?>
		</p>
		<?php
		self::group( $uid, 'price', __( 'Price', 'star-electric' ), (string) ob_get_clean(), true );

		// Availability.
		$labels = array(
			'in'      => __( 'In Stock', 'star-electric' ),
			'out'     => __( 'Out of Stock', 'star-electric' ),
			'unknown' => __( 'Availability not specified', 'star-electric' ),
		);
		$body   = '';
		foreach ( $labels as $key => $label ) {
			$body .= self::option( 'stock', $key, $label, (int) ( $counts['stock'][ $key ] ?? 0 ), $a['stock'] );
		}
		self::group( $uid, 'stock', __( 'Availability', 'star-electric' ), $body, true );

		// How it is sold. No rating filter: no approved source publishes ratings.
		$labels = array(
			'priced'     => __( 'Has a published price', 'star-electric' ),
			'quote'      => __( 'Request a Quote', 'star-electric' ),
			'discounted' => __( 'Discounted at source', 'star-electric' ),
		);
		$body   = '';
		foreach ( $labels as $key => $label ) {
			$body .= self::option( 'pricing', $key, $label, (int) ( $counts['pricing'][ $key ] ?? 0 ), $a['pricing'] );
		}
		self::group( $uid, 'pricing', __( 'How it is sold', 'star-electric' ), $body, false );

		// Product type.
		$labels = array(
			'simple'   => __( 'Simple products', 'star-electric' ),
			'variable' => __( 'Variable products', 'star-electric' ),
		);
		$body   = '';
		foreach ( $labels as $key => $label ) {
			$body .= self::option( 'type', $key, $label, (int) ( $counts['type'][ $key ] ?? 0 ), $a['type'] );
		}
		self::group( $uid, 'type', __( 'Product type', 'star-electric' ), $body, false );

		return (string) ob_get_clean();
	}

	/**
	 * One collapsible group.
	 *
	 * @param string $uid   Panel prefix.
	 * @param string $id    Group id.
	 * @param string $title Group title.
	 * @param string $body  Group body HTML.
	 * @param bool   $open  Whether it starts open.
	 */
	private static function group( string $uid, string $id, string $title, string $body, bool $open ): void {
		$pid = $uid . '-' . $id;
		printf(
			'<div class="filter-group"><button class="filter-group__btn" type="button" data-toggle-panel aria-expanded="%s" aria-controls="%s">%s%s</button><div class="filter-group__body" id="%s"%s>%s</div></div>',
			$open ? 'true' : 'false',
			esc_attr( $pid ),
			esc_html( $title ),
			'<svg class="ico " viewBox="0 0 24 24" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg>',
			esc_attr( $pid ),
			$open ? '' : ' hidden',
			$body // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}

	/**
	 * One checkbox option.
	 *
	 * @param string $group  Filter group.
	 * @param string $value  Option value.
	 * @param string $label  Option label.
	 * @param int    $count  Facet count.
	 * @param array  $active Active values in this group.
	 */
	private static function option( string $group, string $value, string $label, int $count, array $active ): string {
		return sprintf(
			'<label class="filter-opt"><input type="checkbox" data-filter="%s" name="%s[]" value="%s"%s><span>%s</span><span class="count">%d</span></label>',
			esc_attr( $group ),
			esc_attr( $group ),
			esc_attr( $value ),
			in_array( $value, $active, true ) ? ' checked' : '',
			esc_html( $label ),
			$count
		);
	}

	/**
	 * The current URL with one filter value removed.
	 *
	 * @param string $group Filter group.
	 * @param string $value Value to drop, or '' to drop the whole group.
	 */
	public static function remove_url( string $group, string $value = '' ): string {
		global $wp;

		$base = home_url( $wp->request ? user_trailingslashit( $wp->request ) : '/' );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$args = array_map( 'sanitize_text_field', wp_unslash( (array) $_GET ) );
		unset( $args['paged'], $args['product-page'] );

		if ( '' === $value ) {
			unset( $args[ $group ] );
		} elseif ( isset( $args[ $group ] ) ) {
			$kept = array_values( array_diff( (array) $args[ $group ], array( $value ) ) );
			if ( $kept ) {
				$args[ $group ] = $kept;
			} else {
				unset( $args[ $group ] );
			}
		}

		return $args ? add_query_arg( $args, $base ) : $base;
	}

	/**
	 * The active filters as chip labels, in the approved order.
	 *
	 * @return array<int,array{label:string,url:string}>
	 */
	public static function chips(): array {
		$a     = self::active();
		$chips = array();

		foreach ( $a['cat'] as $slug ) {
			$term = get_term_by( 'slug', $slug, 'product_cat' );
			if ( $term instanceof WP_Term ) {
				$chips[] = array(
					'label' => $term->name,
					'url'   => self::remove_url( 'cat', $slug ),
				);
			}
		}
		foreach ( $a['brand'] as $slug ) {
			$term = get_term_by( 'slug', $slug, Star_Electric_Taxonomies::BRAND );
			if ( $term instanceof WP_Term ) {
				$chips[] = array(
					'label' => $term->name,
					'url'   => self::remove_url( 'brand', $slug ),
				);
			}
		}

		$stock_labels = array(
			'in'      => __( 'In Stock', 'star-electric' ),
			'out'     => __( 'Out of Stock', 'star-electric' ),
			'unknown' => __( 'Availability not specified', 'star-electric' ),
		);
		foreach ( $a['stock'] as $key ) {
			$chips[] = array(
				'label' => $stock_labels[ $key ] ?? $key,
				'url'   => self::remove_url( 'stock', $key ),
			);
		}

		$pricing_labels = array(
			'priced'     => __( 'Has a published price', 'star-electric' ),
			'quote'      => __( 'Request a Quote', 'star-electric' ),
			'discounted' => __( 'Discounted at source', 'star-electric' ),
		);
		foreach ( $a['pricing'] as $key ) {
			$chips[] = array(
				'label' => $pricing_labels[ $key ] ?? $key,
				'url'   => self::remove_url( 'pricing', $key ),
			);
		}

		$type_labels = array(
			'simple'   => __( 'Simple products', 'star-electric' ),
			'variable' => __( 'Variable products', 'star-electric' ),
		);
		foreach ( $a['type'] as $key ) {
			$chips[] = array(
				'label' => $type_labels[ $key ] ?? $key,
				'url'   => self::remove_url( 'type', $key ),
			);
		}

		// One chip covers the whole range, so removing it must drop both bounds.
		if ( null !== $a['min'] || null !== $a['max'] ) {
			$chips[] = array(
				'label' => sprintf(
					'Rs. %s – %s',
					number_format_i18n( (float) ( $a['min'] ?? 0 ) ),
					null === $a['max'] ? __( 'any', 'star-electric' ) : number_format_i18n( (float) $a['max'] )
				),
				'url'   => remove_query_arg( array( 'min', 'max' ), self::remove_url( 'min' ) ),
			);
		}

		return $chips;
	}

	/**
	 * The URL with every filter removed, keeping the page itself.
	 */
	public static function clear_url(): string {
		global $wp;

		$base = home_url( $wp->request ? user_trailingslashit( $wp->request ) : '/' );
		$drop = array( 'cat', 'brand', 'stock', 'pricing', 'type', 'min', 'max', 'paged', 'product-page' );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$keep = array_diff_key( (array) $_GET, array_flip( $drop ) );

		return $keep ? add_query_arg( array_map( 'sanitize_text_field', wp_unslash( $keep ) ), $base ) : $base;
	}
}
