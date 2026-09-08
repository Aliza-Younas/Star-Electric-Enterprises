<?php
/**
 * Catalogue rails, department strips, brand grids and range panels.
 *
 * The homepage shows ten product rails, two department navigations, a deals
 * rail and a brand grid. None of that may be pasted into a page builder: the
 * moment a price changes or a product goes out of stock, a hardcoded card is a
 * lie. Everything here queries WooCommerce live and renders the approved
 * markup, so the page stays editable while the data stays real.
 *
 *   [star_products category="lighting" limit="10" title="LED Lighting"]
 *   [star_products department="home-automation" limit="10"]
 *   [star_products on_sale="yes" limit="10" title="Discounted at source"]
 *   [star_departments]
 *   [star_hero_rail]
 *   [star_brands]
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
		add_shortcode( 'star_hero_rail', array( __CLASS__, 'hero_rail' ) );
		add_shortcode( 'star_brands', array( __CLASS__, 'brands' ) );
		add_shortcode( 'star_ranges', array( __CLASS__, 'ranges' ) );
	}

	/**
	 * One inline icon, matching the approved storefront's set.
	 *
	 * @param string $name Icon key.
	 * @param string $cls  Extra class.
	 */
	private static function icon( string $name, string $cls = '' ): string {
		$paths = array(
			'chevleft'   => '<path d="m14 7-5 5 5 5"/>',
			'chevright'  => '<path d="m10 7 5 5-5 5"/>',
			'arrowright' => '<path d="M5 12h13M13 6l6 6-6 6"/>',
			'eye'        => '<path d="M2.8 12S6.6 5.8 12 5.8 21.2 12 21.2 12 17.4 18.2 12 18.2 2.8 12 2.8 12Z"/><circle cx="12" cy="12" r="3"/>',
		);

		return '<svg class="ico ' . esc_attr( $cls ) . '" viewBox="0 0 24 24" aria-hidden="true">'
			. ( $paths[ $name ] ?? '' ) . '</svg>';
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
				'count'      => '',
				'id'         => '',
			),
			$atts,
			'star_products'
		);

		$args = array(
			'status'  => 'publish',
			'limit'   => max( 1, min( 24, (int) $a['limit'] ) ),
			'orderby' => 'ID',
			'order'   => 'ASC',
		);

		if ( '' !== $a['category'] ) {
			$args['category'] = array_map( 'sanitize_title', explode( ',', $a['category'] ) );
		}
		if ( 'yes' === $a['on_sale'] ) {
			$ids = wc_get_product_ids_on_sale();
			if ( empty( $ids ) ) {
				return '';
			}
			$args['include'] = $ids;
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

		return self::rail(
			(string) ( '' !== $a['id'] ? $a['id'] : 'rail-' . sanitize_title( (string) $a['title'] ) ),
			(string) $a['title'],
			(array) wc_get_products( $args ),
			(string) $a['view_all'],
			'' === $a['count'] ? 0 : (int) $a['count']
		);
	}

	/**
	 * Render a rail from an already-resolved product list.
	 *
	 * @param string       $id       DOM id for the track.
	 * @param string       $title    Rail title.
	 * @param WC_Product[] $products Products.
	 * @param string       $view_all "View All" URL, or ''.
	 * @param int          $count    Total behind the rail, or 0 to omit.
	 */
	public static function rail( string $id, string $title, array $products, string $view_all = '', int $count = 0 ): string {
		if ( empty( $products ) ) {
			return '';
		}

		ob_start();
		?>
		<section class="rail" aria-labelledby="<?php echo esc_attr( $id ); ?>-t">
			<div class="rail__head">
				<h2 class="rail__title" id="<?php echo esc_attr( $id ); ?>-t"><?php echo esc_html( $title ); ?></h2>
				<div class="rail__tools">
					<?php if ( '' !== $view_all ) : ?>
						<a class="rail__all" href="<?php echo esc_url( $view_all ); ?>">
							<?php esc_html_e( 'View All', 'star-electric' ); ?>
							<?php if ( $count > 0 ) : ?>
								<span class="rail__count"><?php echo esc_html( (string) $count ); ?></span>
							<?php endif; ?>
						</a>
					<?php endif; ?>
					<div class="rail__arrows">
						<button class="rail__arrow" type="button" data-rail-prev
							aria-label="<?php echo esc_attr( sprintf( /* translators: %s: rail title */ __( 'Previous %s products', 'star-electric' ), $title ) ); ?>">
							<?php echo self::icon( 'chevleft' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
						<button class="rail__arrow" type="button" data-rail-next
							aria-label="<?php echo esc_attr( sprintf( /* translators: %s: rail title */ __( 'More %s products', 'star-electric' ), $title ) ); ?>">
							<?php echo self::icon( 'chevright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
					</div>
				</div>
			</div>
			<ul class="rail__track" id="<?php echo esc_attr( $id ); ?>" tabindex="0" role="list">
				<?php
				foreach ( $products as $product ) {
					if ( $product instanceof WC_Product ) {
						self::mini_card( $product );
					}
				}
				?>
			</ul>
		</section>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * One rail card, matching the approved storefront exactly.
	 *
	 * Navy carries the everyday cart action and red is reserved for quotation,
	 * so a rail of quote-only products does not become a wall of red.
	 *
	 * @param WC_Product $product Product.
	 */
	private static function mini_card( WC_Product $product ): void {
		$id    = $product->get_id();
		$link  = (string) get_permalink( $id );
		$quote = class_exists( 'Star_Electric_Quote_Only' ) && Star_Electric_Quote_Only::is_quote( $product );

		$brands     = wp_get_object_terms( $id, Star_Electric_Taxonomies::BRAND );
		$brand_term = ( ! is_wp_error( $brands ) && $brands ) ? $brands[0] : null;

		$off = 0;
		if ( ! $quote ) {
			$regular = (float) $product->get_regular_price();
			$sale    = (float) $product->get_price();
			if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
				$off = (int) round( ( 1 - ( $sale / $regular ) ) * 100 );
			}
		}
		?>
		<li class="mcard" data-id="<?php echo esc_attr( (string) $id ); ?>">
			<a class="mcard__media" href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true">
				<?php if ( $off > 0 ) : ?>
					<span class="mcard__off">-<?php echo esc_html( (string) $off ); ?>%</span>
				<?php endif; ?>
				<?php if ( has_post_thumbnail( $id ) ) : ?>
					<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ) ) ); ?>
				<?php else : ?>
					<span class="thumb-none"><?php esc_html_e( 'Product image unavailable', 'star-electric' ); ?></span>
				<?php endif; ?>
			</a>
			<div class="mcard__body">
				<h3 class="mcard__name"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
				<p class="mcard__brand">
					<?php esc_html_e( 'By:', 'star-electric' ); ?>
					<?php if ( $brand_term instanceof WP_Term ) : ?>
						<a href="<?php echo esc_url( (string) get_term_link( $brand_term ) ); ?>"><?php echo esc_html( $brand_term->name ); ?></a>
					<?php else : ?>
						<?php esc_html_e( 'Not specified', 'star-electric' ); ?>
					<?php endif; ?>
				</p>
				<p class="mcard__price">
					<?php if ( $quote ) : ?>
						<span class="mcard__quote">
							<b><?php esc_html_e( 'Request a Quote', 'star-electric' ); ?></b>
							<span><?php esc_html_e( 'Priced on enquiry', 'star-electric' ); ?></span>
						</span>
					<?php else : ?>
						<?php echo wp_kses_post( $product->get_price_html() ); ?>
					<?php endif; ?>
				</p>
				<div class="mcard__actions">
					<?php if ( $quote ) : ?>
						<a class="mcard__btn mcard__btn--quote" href="<?php echo esc_url( Star_Electric_Quote_Only::quote_url( $product ) ); ?>">
							<?php esc_html_e( 'Request Quote', 'star-electric' ); ?>
						</a>
					<?php elseif ( ! $product->is_in_stock() ) : ?>
						<button class="mcard__btn mcard__btn--primary" type="button" disabled>
							<?php esc_html_e( 'Out of Stock', 'star-electric' ); ?>
						</button>
					<?php elseif ( $product->is_type( 'variable' ) ) : ?>
						<a class="mcard__btn mcard__btn--primary" href="<?php echo esc_url( $link ); ?>">
							<?php esc_html_e( 'Select Options', 'star-electric' ); ?>
						</a>
					<?php else : ?>
						<a class="mcard__btn mcard__btn--primary add_to_cart_button ajax_add_to_cart"
							href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
							data-product_id="<?php echo esc_attr( (string) $id ); ?>"
							data-quantity="1" rel="nofollow">
							<?php esc_html_e( 'Add to Cart', 'star-electric' ); ?>
						</a>
					<?php endif; ?>
					<a class="mcard__btn mcard__btn--ghost" href="<?php echo esc_url( $link ); ?>"
						aria-label="<?php echo esc_attr( sprintf( /* translators: %s: product name */ __( 'View %s', 'star-electric' ), $product->get_name() ) ); ?>">
						<?php echo self::icon( 'eye' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><?php esc_html_e( 'View', 'star-electric' ); ?></span>
					</a>
				</div>
			</div>
		</li>
		<?php
	}

	/**
	 * The circular department strip.
	 *
	 * A department whose sources publish ranges rather than individual products
	 * shows its range count, so the tile never reads as empty when it is not.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public static function departments( $atts ): string {
		unset( $atts );

		$uri = get_stylesheet_directory_uri() . '/assets/images/departments/';

		ob_start();
		echo '<ul class="dept-track" id="deptStrip" tabindex="0" role="list">';
		foreach ( Star_Electric_Navigation::departments() as $dept ) {
			$count = Star_Electric_Navigation::count( $dept['sel'] );
			$meta  = '';

			if ( $count > 0 ) {
				$meta = sprintf(
					/* translators: %s: product count */
					_n( '%s product', '%s products', $count, 'star-electric' ),
					number_format_i18n( $count )
				);
			} else {
				$ranges = Star_Electric_Navigation::range_count( $dept['sel'] );
				if ( $ranges > 0 ) {
					$meta = sprintf(
						/* translators: %s: range count */
						_n( '%s range', '%s ranges', $ranges, 'star-electric' ),
						number_format_i18n( $ranges )
					);
				}
			}
			?>
			<li class="dept">
				<a class="dept__link" href="<?php echo esc_url( Star_Electric_Navigation::url( $dept['sel'] ) ); ?>">
					<span class="dept__disc">
						<img src="<?php echo esc_url( $uri . $dept['art'] . '.webp' ); ?>" alt=""
							loading="lazy" decoding="async" width="220" height="220">
					</span>
					<span class="dept__name"><?php echo esc_html( $dept['label'] ); ?></span>
					<?php if ( '' !== $meta ) : ?>
						<span class="dept__meta"><?php echo esc_html( $meta ); ?></span>
					<?php endif; ?>
				</a>
			</li>
			<?php
		}
		echo '</ul>';

		return (string) ob_get_clean();
	}

	/**
	 * The department rail beside the hero.
	 *
	 * Categories with subcategories open a flyout; the rest are a plain link.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public static function hero_rail( $atts ): string {
		unset( $atts );

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
			return '';
		}

		$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

		ob_start();
		echo '<nav class="hrail" aria-label="' . esc_attr__( 'Product departments', 'star-electric' ) . '">';
		echo '<p class="hrail__head">' . esc_html__( 'All Departments', 'star-electric' ) . '</p>';
		echo '<ul class="hrail__list">';

		$rows = 0;

		foreach ( $parents as $term ) {
			if ( ! $term instanceof WP_Term || 'uncategorized' === $term->slug ) {
				continue;
			}
			++$rows;

			$children = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'parent'     => $term->term_id,
					'hide_empty' => true,
					'orderby'    => 'count',
					'order'      => 'DESC',
				)
			);
			$children = is_wp_error( $children ) ? array() : $children;
			$link     = (string) get_term_link( $term );

			if ( ! $children ) {
				printf(
					'<li class="hrail__item"><a class="hrail__link" href="%s">%s</a></li>',
					esc_url( $link ),
					esc_html( $term->name )
				);
				continue;
			}
			?>
			<li class="hrail__item hrail__item--has-sub">
				<a class="hrail__link" href="<?php echo esc_url( $link ); ?>">
					<?php echo esc_html( $term->name ); ?>
					<?php echo self::icon( 'chevright', 'hrail__chev' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<div class="hrail__flyout">
					<p class="hrail__flyhead">
						<?php echo esc_html( $term->name ); ?>
						<span>
							<?php
							printf(
								/* translators: %s: product count */
								esc_html__( '%s products', 'star-electric' ),
								esc_html( number_format_i18n( (int) $term->count ) )
							);
							?>
						</span>
					</p>
					<ul>
						<?php foreach ( $children as $child ) : ?>
							<li>
								<a href="<?php echo esc_url( (string) get_term_link( $child ) ); ?>">
									<?php echo esc_html( $child->name ); ?>
									<span><?php echo esc_html( (string) (int) $child->count ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
					<a class="hrail__flyall" href="<?php echo esc_url( $link ); ?>">
						<?php
						printf(
							/* translators: %s: category name */
							esc_html__( 'Browse %s', 'star-electric' ),
							esc_html( $term->name )
						);
						?>
					</a>
				</div>
			</li>
			<?php
		}

		// Shortcuts fill the rail out to the thirteen rows the approved page shows.
		foreach ( Star_Electric_Navigation::shortcuts() as $shortcut ) {
			if ( $rows >= 13 ) {
				break;
			}
			++$rows;
			printf(
				'<li class="hrail__item"><a class="hrail__link" href="%s">%s</a></li>',
				esc_url( Star_Electric_Navigation::url( $shortcut['sel'] ) ),
				esc_html( $shortcut['label'] )
			);
		}

		echo '</ul>';
		printf(
			'<a class="hrail__all" href="%s">%s%s</a>',
			esc_url( $shop ),
			esc_html__( 'View All Categories', 'star-electric' ),
			self::icon( 'arrowright' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
		echo '</nav>';

		return (string) ob_get_clean();
	}

	/**
	 * The brand grid.
	 *
	 * Monogram marks, never manufacturer logos: a real logo would imply a
	 * dealership or distribution relationship that is not on record.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public static function brands( $atts ): string {
		$a = shortcode_atts( array( 'hide_empty' => 'yes' ), $atts, 'star_brands' );

		$terms = get_terms(
			array(
				'taxonomy'   => Star_Electric_Taxonomies::BRAND,
				'hide_empty' => 'yes' === $a['hide_empty'],
				'orderby'    => 'count',
				'order'      => 'DESC',
			)
		);
		if ( is_wp_error( $terms ) || ! $terms ) {
			return '';
		}

		ob_start();
		echo '<ul class="brand-grid" id="brandGrid">';
		foreach ( $terms as $term ) {
			$mark = (string) get_term_meta( $term->term_id, '_star_electric_mark', true );
			if ( '' === $mark ) {
				$mark = strtoupper( substr( $term->name, 0, 1 ) );
			}
			?>
			<li>
				<a class="brand-card" href="<?php echo esc_url( (string) get_term_link( $term ) ); ?>">
					<span class="brand-card__mark" aria-hidden="true"><?php echo esc_html( $mark ); ?></span>
					<span class="brand-card__name"><?php echo esc_html( $term->name ); ?></span>
					<span class="brand-card__note">
						<?php
						printf(
							/* translators: %s: product count */
							esc_html( _n( '%s product', '%s products', (int) $term->count, 'star-electric' ) ),
							esc_html( number_format_i18n( (int) $term->count ) )
						);
						?>
					</span>
				</a>
			</li>
			<?php
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
			$id     = (int) get_the_ID();
			$series = (string) get_post_meta( $id, '_star_electric_series', true );

			echo '<li class="family-item"><span class="family-item__media">';
			if ( has_post_thumbnail( $id ) ) {
				echo get_the_post_thumbnail( $id, 'woocommerce_thumbnail', array( 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo '<span class="thumb-none">' . esc_html__( 'No image', 'star-electric' ) . '</span>';
			}
			echo '</span><span class="family-item__body">';
			printf( '<span class="family-item__name">%s</span>', esc_html( get_the_title() ) );
			if ( '' !== $series ) {
				printf(
					'<span class="family-item__series">%s %s</span>',
					esc_html__( 'Series:', 'star-electric' ),
					esc_html( $series )
				);
			}
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
