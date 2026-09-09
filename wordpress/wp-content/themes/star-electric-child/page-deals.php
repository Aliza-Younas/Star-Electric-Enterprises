<?php
/**
 * Deals & Offers.
 *
 * A port of deals.html. The page lists products whose own supplier source
 * publishes a price below its previous price - nothing is marked down here,
 * no offer period is claimed and no countdown appears, which is the whole
 * point of the note at the foot of the page.
 *
 * The filter panel, the sort control and the chips are the shop's, driven
 * through Star_Electric_Filters::query_args(). A second implementation of the
 * six filter groups is how two views of one catalogue start disagreeing about
 * what is in stock, so there isn't one.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$star_art     = get_stylesheet_directory_uri() . '/assets/images';
$star_filters = class_exists( 'Star_Electric_Filters' );
$star_active  = $star_filters ? Star_Electric_Filters::active() : array( 'sort' => '' );

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$star_view = ( isset( $_GET['view'] ) && 'list' === $_GET['view'] ) ? 'list' : 'grid';

$star_paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

/*
 * A discount is only a discount when the importer recorded one, which it does
 * only where the source published both a previous and a current price. Products
 * priced on enquiry can never qualify, so nothing here can show a fake saving.
 */
$star_args = array(
	'post_type'           => 'product',
	'post_status'         => 'publish',
	'posts_per_page'      => 12,
	'paged'               => $star_paged,
	'ignore_sticky_posts' => true,
	'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery
		array(
			'key'     => '_star_electric_discount',
			'value'   => 0,
			'type'    => 'NUMERIC',
			'compare' => '>',
		),
	),
);

if ( $star_filters ) {
	$star_args = Star_Electric_Filters::query_args( $star_args, 'discount' );
} else {
	$star_args['meta_key'] = '_star_electric_discount'; // phpcs:ignore WordPress.DB.SlowDBQuery
	$star_args['orderby']  = 'meta_value_num';
	$star_args['order']    = 'DESC';
}

$star_query = new WP_Query( $star_args );
$star_found = (int) $star_query->found_posts;

Star_Electric_Shell::page_head(
	array(
		__( 'Home', 'star-electric-child' )  => home_url( '/' ),
		__( 'Deals', 'star-electric-child' ) => '',
	),
	__( 'Deals & Offers', 'star-electric-child' ),
	__( 'Products a supplier source currently lists below its previous price. Nothing here is a store promotion, and no offer period or quantity limit is implied.', 'star-electric-child' )
);
?>

<section class="section section--sm">
	<div class="container">
		<div class="promo-grid">
			<article class="promo-card promo-card--dark">
				<?php echo Star_Electric_Shell::category_image( 'fans-ventilation', 1200, 620 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<h2 style="font-size:19px"><?php esc_html_e( 'Fans & Ventilation', 'star-electric-child' ); ?></h2>
				<p><?php esc_html_e( 'Ceiling, bracket, pedestal and exhaust fans.', 'star-electric-child' ); ?></p>
				<a class="btn btn--accent btn--sm" href="<?php echo esc_url( Star_Electric_Navigation::url( array( 'cat' => 'fans-ventilation' ) ) ); ?>">
					<?php esc_html_e( 'Shop Fans', 'star-electric-child' ); ?>
				</a>
			</article>
			<article class="promo-card">
				<img src="<?php echo esc_url( $star_art . '/banners/promo-lighting.webp' ); ?>" alt="" width="1200" height="620" loading="lazy" decoding="async">
				<h2 style="font-size:19px"><?php esc_html_e( 'Lighting Offers', 'star-electric-child' ); ?></h2>
				<p><?php esc_html_e( 'LED bulbs, panels, downlights and floodlights.', 'star-electric-child' ); ?></p>
				<a class="btn btn--primary btn--sm" href="<?php echo esc_url( Star_Electric_Navigation::url( array( 'cat' => 'lighting' ) ) ); ?>">
					<?php esc_html_e( 'Shop Lighting', 'star-electric-child' ); ?>
				</a>
			</article>
			<article class="promo-card promo-card--dark">
				<img src="<?php echo esc_url( $star_art . '/banners/promo-protection.webp' ); ?>" alt="" width="1200" height="620" loading="lazy" decoding="async">
				<h2 style="font-size:19px"><?php esc_html_e( 'Project Quantities', 'star-electric-child' ); ?></h2>
				<p><?php esc_html_e( 'Bulk pricing for contractors on request.', 'star-electric-child' ); ?></p>
				<a class="btn btn--accent btn--sm" href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>">
					<?php esc_html_e( 'Request a Quote', 'star-electric-child' ); ?>
				</a>
			</article>
		</div>
	</div>
</section>

<section class="section section--sm" style="padding-top:0">
	<form class="container shop-layout" method="get" id="shopFilters">
		<?php
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['view'] ) && '' !== $_GET['view'] ) {
			printf( '<input type="hidden" name="view" value="%s">', esc_attr( $star_view ) );
		}
		?>

		<aside class="filters" data-filters aria-label="<?php esc_attr_e( 'Deal filters', 'star-electric-child' ); ?>">
			<?php
			if ( $star_filters ) {
				echo Star_Electric_Filters::render( 'd0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</aside>

		<div>
			<div class="shop-toolbar">
				<div class="shop-toolbar__left">
					<button class="btn btn--ghost btn--sm filter-open" type="button" id="filterOpen">
						<?php echo Star_Electric_Shell::icon( 'filter' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><?php esc_html_e( 'Filters', 'star-electric-child' ); ?></span>
					</button>
					<span id="resultCount">
						<?php
						if ( $star_found > 0 ) {
							$star_first = ( ( $star_paged - 1 ) * 12 ) + 1;
							$star_last  = min( $star_found, $star_paged * 12 );
							printf(
								/* translators: 1: first result, 2: last result, 3: total */
								esc_html__( 'Showing %1$d–%2$d of %3$d products', 'star-electric-child' ),
								(int) $star_first,
								(int) $star_last,
								(int) $star_found
							);
						} else {
							esc_html_e( 'No offers found', 'star-electric-child' );
						}
						?>
					</span>
				</div>

				<div class="shop-toolbar__right">
					<label class="sr-only" for="sortSelect"><?php esc_html_e( 'Sort offers', 'star-electric-child' ); ?></label>
					<select class="select" id="sortSelect" name="sort">
						<?php
						/*
						 * "Featured" is dropped here and biggest discount leads,
						 * exactly as on the approved deals page: a list of
						 * reductions is ordered by the size of the reduction.
						 */
						$star_sorts = Star_Electric_Filters::sorts();
						unset( $star_sorts['relevance'] );
						// The leading option carries the "Sort:" prefix, exactly as
						// the approved deals page labels it.
						$star_sorts = array( 'discount' => __( 'Sort: Biggest Discount', 'star-electric-child' ) ) + $star_sorts;
						$star_now   = '' !== $star_active['sort'] ? $star_active['sort'] : 'discount';
						foreach ( $star_sorts as $star_key => $star_label ) :
							?>
							<option value="<?php echo esc_attr( $star_key ); ?>" <?php selected( $star_now, $star_key ); ?>>
								<?php echo esc_html( $star_label ); ?>
							</option>
						<?php endforeach; ?>
					</select>

					<div class="view-toggle" role="group" aria-label="<?php esc_attr_e( 'Product view', 'star-electric-child' ); ?>">
						<button type="button" data-view="grid" aria-pressed="<?php echo esc_attr( 'grid' === $star_view ? 'true' : 'false' ); ?>"
							aria-label="<?php esc_attr_e( 'Grid view', 'star-electric-child' ); ?>">
							<?php echo Star_Electric_Shell::icon( 'grid' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
						<button type="button" data-view="list" aria-pressed="<?php echo esc_attr( 'list' === $star_view ? 'true' : 'false' ); ?>"
							aria-label="<?php esc_attr_e( 'List view', 'star-electric-child' ); ?>">
							<?php echo Star_Electric_Shell::icon( 'list' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
					</div>
				</div>
			</div>

			<?php
			if ( $star_filters ) {
				get_template_part( 'template-parts/filter-chips' );
			}
			?>

			<?php if ( $star_query->have_posts() ) : ?>

				<ul class="product-grid<?php echo esc_attr( 'list' === $star_view ? ' product-grid--list' : '' ); ?>">
					<?php
					while ( $star_query->have_posts() ) {
						$star_query->the_post();
						wc_get_template_part( 'content', 'product' );
					}
					wp_reset_postdata();
					?>
				</ul>

				<?php
				/*
				 * Filter selections arrive as cat[]=, brand[]= and so on, so the
				 * carried arguments have to survive being arrays; running the
				 * whole of $_GET through rawurlencode() would fatal on the first
				 * checkbox group a shopper ticks.
				 */
				$star_carry = array();
				// phpcs:disable WordPress.Security.NonceVerification.Recommended
				foreach ( wp_unslash( $_GET ) as $star_key => $star_value ) {
					$star_key = sanitize_key( (string) $star_key );
					if ( '' === $star_key || 'paged' === $star_key || 'page' === $star_key ) {
						continue;
					}
					if ( is_array( $star_value ) ) {
						$star_carry[ $star_key ] = array_map( 'sanitize_text_field', $star_value );
					} else {
						$star_carry[ $star_key ] = sanitize_text_field( (string) $star_value );
					}
				}
				// phpcs:enable WordPress.Security.NonceVerification.Recommended

				$star_pages = paginate_links(
					array(
						'base'      => trailingslashit( get_permalink() ) . '%_%',
						'format'    => 'page/%#%/',
						'total'     => (int) $star_query->max_num_pages,
						'current'   => $star_paged,
						'add_args'  => $star_carry,
						'type'      => 'array',
						'prev_text' => __( 'Previous', 'star-electric-child' ),
						'next_text' => __( 'Next', 'star-electric-child' ),
					)
				);
				?>
				<?php if ( $star_pages ) : ?>
					<nav class="pagination" id="pagination" aria-label="<?php esc_attr_e( 'Offer pages', 'star-electric-child' ); ?>" style="margin-top:32px">
						<?php foreach ( $star_pages as $star_link ) : ?>
							<?php echo wp_kses_post( $star_link ); ?>
						<?php endforeach; ?>
					</nav>
				<?php endif; ?>

			<?php else : ?>

				<div id="catalogEmpty">
					<div class="empty-state">
						<span class="empty-state__ico">
							<?php echo Star_Electric_Shell::icon( 'tag' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<h2><?php esc_html_e( 'No offers match those filters', 'star-electric-child' ); ?></h2>
						<p><?php esc_html_e( 'Clear a filter to see the rest of the current offers.', 'star-electric-child' ); ?></p>
						<a class="btn btn--accent" href="<?php echo esc_url( get_permalink() ); ?>">
							<?php esc_html_e( 'Clear all filters', 'star-electric-child' ); ?>
						</a>
					</div>
				</div>

			<?php endif; ?>
		</div>

		<div class="drawer drawer--right" id="filterDrawer" hidden>
			<div class="drawer__scrim" data-side-close></div>
			<div class="drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Deal filters', 'star-electric-child' ); ?>">
				<div class="drawer__head">
					<span class="drawer__title"><?php esc_html_e( 'Filters', 'star-electric-child' ); ?></span>
					<button class="icon-btn" type="button" data-side-close aria-label="<?php esc_attr_e( 'Close filters', 'star-electric-child' ); ?>">
						<?php echo Star_Electric_Shell::icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</div>
				<div class="drawer__body" data-filters>
					<?php
					if ( $star_filters ) {
						echo Star_Electric_Filters::render( 'd1' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
				<div class="drawer__foot">
					<button class="btn btn--accent btn--block" type="submit">
						<?php esc_html_e( 'Show results', 'star-electric-child' ); ?>
					</button>
				</div>
			</div>
		</div>
	</form>
</section>

<section class="section section--tint section--sm">
	<div class="container">
		<div class="placeholder-note">
			<?php echo Star_Electric_Shell::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span>
				<strong><?php esc_html_e( 'No countdown timers or urgency claims are used on this page.', 'star-electric-child' ); ?></strong>
				<?php esc_html_e( 'Every discount shown is one the product’s own source publishes: the previous price and the current price are both taken from that source on the date recorded against the product. Nothing is marked down here, and no offer period is claimed.', 'star-electric-child' ); ?>
			</span>
		</div>
	</div>
</section>

<?php
get_footer();
