<?php
/**
 * Categories.
 *
 * The department index. On the approved storefront "Categories" is the mega
 * menu in the header, and the link behind it lands on a category view; here it
 * is a page of its own so the whole department tree has a real URL that can be
 * linked, shared and indexed. The cards and subcategory tiles are the approved
 * components, unchanged.
 *
 * Counts come from the taxonomy, so this page cannot drift from the catalogue.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$star_tree = Star_Electric_Shell::category_tree();

$star_total = 0;
foreach ( $star_tree as $star_node ) {
	$star_total += (int) $star_node['term']->count;
}

Star_Electric_Shell::page_head(
	array(
		__( 'Home', 'star-electric-child' )       => home_url( '/' ),
		__( 'Categories', 'star-electric-child' ) => '',
	),
	__( 'Shop by Department', 'star-electric-child' ),
	sprintf(
		/* translators: 1: number of departments, 2: number of products */
		__( 'Every product in the catalogue sits in one of these %1$s departments — %2$s items in all. Open a department to filter it by brand, price, availability or product type.', 'star-electric-child' ),
		number_format_i18n( count( $star_tree ) ),
		number_format_i18n( $star_total )
	)
);
?>

<section class="section section--sm">
	<div class="container">
		<?php get_template_part( 'template-parts/category-grid' ); ?>
	</div>
</section>

<?php foreach ( $star_tree as $star_i => $star_node ) : ?>
	<?php
	$star_term = $star_node['term'];
	$star_subs = $star_node['children'];
	if ( empty( $star_subs ) ) {
		continue;
	}
	?>
	<section class="section section--sm<?php echo esc_attr( 0 === $star_i % 2 ? ' section--tint' : '' ); ?>"
		aria-labelledby="dept-<?php echo esc_attr( $star_term->slug ); ?>">
		<div class="container">
			<div class="section__head">
				<div>
					<h2 class="section__title" style="font-size:20px" id="dept-<?php echo esc_attr( $star_term->slug ); ?>">
						<?php echo esc_html( $star_term->name ); ?>
					</h2>
					<p class="section__sub">
						<?php
						printf(
							/* translators: %s: product count */
							esc_html( _n( '%s product', '%s products', (int) $star_term->count, 'star-electric-child' ) ),
							esc_html( number_format_i18n( (int) $star_term->count ) )
						);
						?>
					</p>
				</div>
				<a class="link-more" href="<?php echo esc_url( (string) get_term_link( $star_term ) ); ?>">
					<?php esc_html_e( 'Browse department', 'star-electric-child' ); ?>
					<?php echo Star_Electric_Shell::icon( 'arrowright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</div>

			<ul class="subcat-grid">
				<?php foreach ( $star_subs as $star_sub ) : ?>
					<?php $star_art = Star_Electric_Shell::category_icon( $star_sub ); ?>
					<li>
						<a class="subcat-card" href="<?php echo esc_url( (string) get_term_link( $star_sub ) ); ?>">
							<?php echo wp_kses_post( $star_art ); ?>
							<span><?php echo esc_html( $star_sub->name ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
<?php endforeach; ?>

<section class="section bulk" aria-labelledby="catCta">
	<div class="container bulk__inner">
		<div>
			<h2 class="bulk__title" id="catCta"><?php esc_html_e( 'Cannot find the department you need?', 'star-electric-child' ); ?></h2>
			<p class="bulk__text"><?php esc_html_e( 'Search the whole catalogue, or send us the ratings and sizes you are after and we will price them.', 'star-electric-child' ); ?></p>
			<div class="btn-row">
				<a class="btn btn--accent btn--lg" href="<?php echo esc_url( Star_Electric_Shell::url( 'shop' ) ); ?>"><?php esc_html_e( 'Shop All Products', 'star-electric-child' ); ?></a>
				<a class="btn btn--light btn--lg" href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>"><?php esc_html_e( 'Request a Quote', 'star-electric-child' ); ?></a>
			</div>
		</div>
		<ol class="bulk__steps">
			<li><b>01</b> <?php esc_html_e( 'Pick the department', 'star-electric-child' ); ?></li>
			<li><b>02</b> <?php esc_html_e( 'Filter by brand, price or availability', 'star-electric-child' ); ?></li>
			<li><b>03</b> <?php esc_html_e( 'Add to cart or request a quotation', 'star-electric-child' ); ?></li>
		</ol>
	</div>
</section>

<?php
get_footer();
