<?php
/**
 * Brand Directory.
 *
 * A port of brands.html. Every brand tile is a monogram, not a logo, and the
 * note above the directory is kept word for word: the store has stated no
 * dealership, distribution or authorisation relationship with any
 * manufacturer, so none is claimed or implied here.
 *
 * The whole directory is rendered server-side and filtered in the browser,
 * which is what the approved page does. It also means the list is complete for
 * a visitor with no JavaScript, and complete for a search engine.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

Star_Electric_Shell::page_head(
	array(
		__( 'Home', 'star-electric-child' )   => home_url( '/' ),
		__( 'Brands', 'star-electric-child' ) => '',
	),
	__( 'Brand Directory', 'star-electric-child' ),
	__( 'Search or browse alphabetically to find products by brand.', 'star-electric-child' )
);

$star_brands = get_terms(
	array(
		'taxonomy'   => 'star_brand',
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);
$star_brands = is_wp_error( $star_brands ) ? array() : $star_brands;

/*
 * The approved directory lists brands in the catalogue's own order - Pakistan
 * Cables first, then Aqua - and not alphabetically. The importer stores that
 * order on the term; a brand that somehow has none sorts last rather than
 * disappearing, which is why this is a sort rather than an ordered query.
 */
usort(
	$star_brands,
	static function ( WP_Term $a, WP_Term $b ): int {
		$oa = get_term_meta( $a->term_id, '_star_electric_order', true );
		$ob = get_term_meta( $b->term_id, '_star_electric_order', true );
		$oa = '' === $oa ? PHP_INT_MAX : (int) $oa;
		$ob = '' === $ob ? PHP_INT_MAX : (int) $ob;
		return $oa === $ob ? strcmp( $a->name, $b->name ) : ( $oa <=> $ob );
	}
);

/*
 * A brand whose source publishes ranges rather than individual products has no
 * products to link to. Those are separated out below the directory instead of
 * being listed as if a click would reach a catalogue.
 */
$star_listed = array();
$star_family = array();
foreach ( $star_brands as $star_term ) {
	if ( (int) $star_term->count > 0 ) {
		$star_listed[] = $star_term;
	} else {
		$star_family[] = $star_term;
	}
}

/*
 * The approved A-Z bar shows the whole alphabet and disables the letters no
 * brand starts with, so the bar is the same shape on every visit. Listing only
 * the letters in use made it four buttons wide and a different size at every
 * breakpoint from the approved page.
 */
$star_first = static function ( string $name ): string {
	$letter = strtoupper( substr( remove_accents( $name ), 0, 1 ) );
	return preg_match( '/[A-Z]/', $letter ) ? $letter : '#';
};

$star_available = array();
foreach ( $star_brands as $star_term ) {
	$star_available[ $star_first( $star_term->name ) ] = true;
}
?>

<section class="section section--sm">
	<div class="container">

		<div class="placeholder-note" style="margin-bottom:32px">
			<?php echo Star_Electric_Shell::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span>
				<strong><?php esc_html_e( 'Brand names come from the approved product sources; the logo tiles are placeholders.', 'star-electric-child' ); ?></strong>
				<?php esc_html_e( 'Star Electric Enterprises has not stated any dealership, distribution or authorisation relationship, so none is claimed here. Supplied logo files will replace these tiles.', 'star-electric-child' ); ?>
			</span>
		</div>

		<div class="panel panel--tint" style="margin-bottom:32px">
			<div class="form-grid" style="grid-template-columns:minmax(0,1fr) auto;align-items:end">
				<div class="field">
					<label class="field__label" for="brandSearch"><?php esc_html_e( 'Search brands', 'star-electric-child' ); ?></label>
					<input class="input" id="brandSearch" type="search" autocomplete="off"
						placeholder="<?php esc_attr_e( 'Start typing a brand name…', 'star-electric-child' ); ?>">
				</div>
				<p class="t-sm t-muted" id="brandCount" style="padding-bottom:12px">
					<?php
					printf(
						/* translators: %s: number of brands with individual products */
						esc_html__( '%s brands with individual products', 'star-electric-child' ),
						esc_html( (string) count( $star_listed ) )
					);
					if ( ! empty( $star_family ) ) {
						printf(
							/* translators: %s: number of brands listed at family level */
							esc_html__( ' · %s listed at family level', 'star-electric-child' ),
							esc_html( (string) count( $star_family ) )
						);
					}
					?>
				</p>
			</div>

			<div class="az-bar" id="azBar" role="group" aria-label="<?php esc_attr_e( 'Filter brands by first letter', 'star-electric-child' ); ?>" style="margin-top:20px">
				<button type="button" data-letter="" aria-pressed="true"><?php esc_html_e( 'All', 'star-electric-child' ); ?></button>
				<?php foreach ( str_split( 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' ) as $star_letter ) : ?>
					<button type="button" data-letter="<?php echo esc_attr( $star_letter ); ?>" aria-pressed="false"
						<?php disabled( isset( $star_available[ $star_letter ] ), false ); ?>><?php echo esc_html( $star_letter ); ?></button>
				<?php endforeach; ?>
			</div>
		</div>

		<ul class="brand-grid" id="brandDirectory">
			<?php foreach ( $star_listed as $star_term ) : ?>
				<?php
				$star_mark = (string) get_term_meta( $star_term->term_id, '_star_electric_mark', true );
				if ( '' === $star_mark ) {
					$star_mark = strtoupper( substr( $star_term->name, 0, 1 ) );
				}
				?>
				<li data-brand="<?php echo esc_attr( strtolower( $star_term->name ) ); ?>" data-letter="<?php echo esc_attr( $star_first( $star_term->name ) ); ?>">
					<a class="brand-card" href="<?php echo esc_url( Star_Electric_Navigation::brand_url( $star_term ) ); ?>">
						<span class="brand-card__mark" aria-hidden="true"><?php echo esc_html( $star_mark ); ?></span>
						<span class="brand-card__name"><?php echo esc_html( $star_term->name ); ?></span>
						<span class="brand-card__note">
							<?php
							printf(
								/* translators: %s: product count */
								esc_html( _n( '%s product', '%s products', (int) $star_term->count, 'star-electric-child' ) ),
								esc_html( (string) (int) $star_term->count )
							);
							?>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

		<div id="brandEmpty" hidden>
			<div class="empty-state">
				<span class="empty-state__ico">
					<?php echo Star_Electric_Shell::icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
				<h2><?php esc_html_e( 'No brands match that search', 'star-electric-child' ); ?></h2>
				<p><?php esc_html_e( 'Try a different letter or clear the search box to see the whole directory.', 'star-electric-child' ); ?></p>
			</div>
		</div>
	</div>
</section>

<?php if ( ! empty( $star_family ) ) : ?>
	<section class="section section--sm" id="familySection" aria-labelledby="familyTitle">
		<div class="container">
			<div class="section__head">
				<div>
					<h2 class="section__title" id="familyTitle"><?php esc_html_e( 'Listed at product-family level', 'star-electric-child' ); ?></h2>
					<p class="section__sub">
						<?php esc_html_e( 'For these brands the approved source does not publish individual product pages, so no individual products are listed. The ranges below are shown for navigation and enquiry only — they are not sellable products and carry no price.', 'star-electric-child' ); ?>
					</p>
				</div>
			</div>
			<div id="familyDirectory">
				<?php foreach ( $star_family as $star_term ) : ?>
					<?php
					$star_ranges = do_shortcode( '[star_ranges brand="' . esc_attr( $star_term->slug ) . '" limit="60"]' );
					if ( '' === trim( $star_ranges ) ) {
						continue;
					}
					?>
					<div data-brand="<?php echo esc_attr( strtolower( $star_term->name ) ); ?>" data-letter="<?php echo esc_attr( $star_first( $star_term->name ) ); ?>">
						<?php echo $star_ranges; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<section class="section section--tint" aria-labelledby="brandCatTitle">
	<div class="container">
		<div class="section__head">
			<div>
				<h2 class="section__title" id="brandCatTitle"><?php esc_html_e( 'Prefer to browse by department?', 'star-electric-child' ); ?></h2>
				<p class="section__sub"><?php esc_html_e( 'Every brand’s products also sit inside these departments.', 'star-electric-child' ); ?></p>
			</div>
			<a class="link-more" href="<?php echo esc_url( Star_Electric_Shell::url( 'shop' ) ); ?>">
				<?php esc_html_e( 'Go to shop', 'star-electric-child' ); ?>
				<?php echo Star_Electric_Shell::icon( 'arrowright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>
		<?php get_template_part( 'template-parts/category-grid' ); ?>
	</div>
</section>

<?php
get_footer();
