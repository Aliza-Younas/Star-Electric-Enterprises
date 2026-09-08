<?php
/**
 * The shop, category, brand and department archives.
 *
 * A port of shop.html: page head, filter sidebar, toolbar, product grid,
 * pagination. The filters are a plain GET form so the page works without
 * JavaScript and every filtered view has a real, shareable URL - which the
 * static site's in-page filtering could not offer.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

global $wp_query;

$star_filters = class_exists( 'Star_Electric_Filters' );
$star_active  = $star_filters ? Star_Electric_Filters::active() : array( 'sort' => '' );
$star_found   = (int) $wp_query->found_posts;
$star_object  = get_queried_object();

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$star_view = ( isset( $_GET['view'] ) && 'list' === $_GET['view'] ) ? 'list' : 'grid';

/* The subtitle is the term's own description where there is one, and the
   approved shop copy otherwise. Nothing is invented to fill the space. */
$star_sub = '';
if ( $star_object instanceof WP_Term ) {
	$star_sub = wp_strip_all_tags( (string) term_description( $star_object ) );
} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
	$star_sub = __( 'The complete Star Electric Enterprises catalogue. Filter by category, brand, price, availability or product type to narrow the range.', 'star-electric-child' );
}
?>

<div class="page-head">
	<div class="container">
		<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'star-electric-child' ); ?>">
			<ol class="breadcrumb">
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'star-electric-child' ); ?></a></li>
				<li class="sep" aria-hidden="true">/</li>
				<?php if ( $star_object instanceof WP_Term ) : ?>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'shop' ) ); ?>"><?php esc_html_e( 'Shop', 'star-electric-child' ); ?></a></li>
					<li class="sep" aria-hidden="true">/</li>
					<li aria-current="page"><?php echo esc_html( $star_object->name ); ?></li>
				<?php else : ?>
					<li aria-current="page"><?php esc_html_e( 'Shop', 'star-electric-child' ); ?></li>
				<?php endif; ?>
			</ol>
		</nav>
		<h1 class="page-head__title">
			<?php
			if ( $star_object instanceof WP_Term ) {
				echo esc_html( $star_object->name );
			} else {
				esc_html_e( 'Shop All Products', 'star-electric-child' );
			}
			?>
		</h1>
		<?php if ( '' !== $star_sub ) : ?>
			<p class="page-head__sub"><?php echo esc_html( $star_sub ); ?></p>
		<?php endif; ?>
	</div>
</div>

<section class="section section--sm">
	<form class="container shop-layout" method="get" id="shopFilters">
		<?php
		/*
		 * A GET form replaces the whole query string, so anything the current
		 * view depends on has to be carried through explicitly.
		 */
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		foreach ( array( 's', 'post_type', 'product_cat', 'star_brand', 'star_department', 'view' ) as $star_keep ) {
			if ( isset( $_GET[ $star_keep ] ) && '' !== $_GET[ $star_keep ] ) {
				printf(
					'<input type="hidden" name="%s" value="%s">',
					esc_attr( $star_keep ),
					esc_attr( sanitize_text_field( wp_unslash( $_GET[ $star_keep ] ) ) )
				);
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		?>

		<aside class="filters" data-filters aria-label="<?php esc_attr_e( 'Product filters', 'star-electric-child' ); ?>">
			<?php
			if ( $star_filters ) {
				echo Star_Electric_Filters::render( 'f0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
						$star_per   = max( 1, (int) $wp_query->get( 'posts_per_page' ) );
						$star_page  = max( 1, (int) get_query_var( 'paged' ) );
						$star_first = ( ( $star_page - 1 ) * $star_per ) + 1;
						$star_last  = min( $star_found, $star_page * $star_per );

						if ( $star_found > 0 ) {
							printf(
								/* translators: 1: first result, 2: last result, 3: total */
								esc_html__( 'Showing %1$d–%2$d of %3$d products', 'star-electric-child' ),
								(int) $star_first,
								(int) $star_last,
								(int) $star_found
							);
						} else {
							esc_html_e( 'No products found', 'star-electric-child' );
						}
						?>
					</span>
				</div>

				<div class="shop-toolbar__right">
					<label class="sr-only" for="sortSelect"><?php esc_html_e( 'Sort products', 'star-electric-child' ); ?></label>
					<select class="select" id="sortSelect" name="sort">
						<?php foreach ( Star_Electric_Filters::sorts() as $star_key => $star_label ) : ?>
							<option value="<?php echo esc_attr( $star_key ); ?>"
								<?php selected( $star_active['sort'], $star_key ); ?>>
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

			<?php if ( woocommerce_product_loop() && have_posts() ) : ?>

				<ul class="product-grid<?php echo esc_attr( 'list' === $star_view ? ' product-grid--list' : '' ); ?>">
					<?php
					while ( have_posts() ) {
						the_post();
						wc_get_template_part( 'content', 'product' );
					}
					?>
				</ul>

				<?php
				$star_pages = paginate_links(
					array(
						'total'     => (int) $wp_query->max_num_pages,
						'current'   => max( 1, (int) get_query_var( 'paged' ) ),
						'type'      => 'array',
						'prev_text' => __( 'Previous', 'star-electric-child' ),
						'next_text' => __( 'Next', 'star-electric-child' ),
					)
				);
				?>
				<?php if ( $star_pages ) : ?>
					<nav class="pagination" id="pagination" aria-label="<?php esc_attr_e( 'Product pages', 'star-electric-child' ); ?>" style="margin-top:32px">
						<?php foreach ( $star_pages as $star_link ) : ?>
							<?php echo wp_kses_post( $star_link ); ?>
						<?php endforeach; ?>
					</nav>
				<?php endif; ?>

			<?php else : ?>

				<div id="catalogEmpty">
					<div class="empty-state">
						<span class="empty-state__ico">
							<?php echo Star_Electric_Shell::icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<h2><?php esc_html_e( 'No products match those filters', 'star-electric-child' ); ?></h2>
						<p><?php esc_html_e( 'Try removing a filter or widening the price range to see more of the catalogue.', 'star-electric-child' ); ?></p>
						<a class="btn btn--accent" href="<?php echo esc_url( Star_Electric_Filters::clear_url() ); ?>">
							<?php esc_html_e( 'Clear all filters', 'star-electric-child' ); ?>
						</a>
					</div>
				</div>

			<?php endif; ?>
		</div>

		<div class="drawer drawer--right" id="filterDrawer" hidden>
			<div class="drawer__scrim" data-side-close></div>
			<div class="drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Product filters', 'star-electric-child' ); ?>">
				<div class="drawer__head">
					<span class="drawer__title"><?php esc_html_e( 'Filters', 'star-electric-child' ); ?></span>
					<button class="icon-btn" type="button" data-side-close aria-label="<?php esc_attr_e( 'Close filters', 'star-electric-child' ); ?>">
						<?php echo Star_Electric_Shell::icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</div>
				<div class="drawer__body" data-filters>
					<?php
					if ( $star_filters ) {
						echo Star_Electric_Filters::render( 'f1' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

<?php
get_footer();
