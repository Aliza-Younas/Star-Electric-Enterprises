<?php
/**
 * The department grid.
 *
 * SEE_UI.renderCategoryCard() in PHP. The approved storefront drops this list
 * into five different pages, so it lives in one file here for the same reason
 * it lived in one function there.
 *
 * A category with no image renders the text half of the card rather than a
 * broken frame - there is no stand-in photograph to substitute.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$star_tree = Star_Electric_Shell::category_tree();

if ( empty( $star_tree ) ) {
	return;
}
?>
<ul class="cat-grid cat-grid--4">
	<?php foreach ( $star_tree as $star_node ) : ?>
		<?php
		$star_term  = $star_node['term'];
		$star_image = Star_Electric_Shell::category_image( $star_term->slug, 900, 560 );
		$star_subs  = count( $star_node['children'] );
		?>
		<li>
			<a class="cat-card" href="<?php echo esc_url( (string) get_term_link( $star_term ) ); ?>">
				<?php if ( '' !== $star_image ) : ?>
					<span class="cat-card__photo"><?php echo wp_kses_post( $star_image ); ?></span>
				<?php endif; ?>
				<span class="cat-card__txt">
					<span class="cat-card__name"><?php echo esc_html( $star_term->name ); ?></span>
					<span class="cat-card__meta">
						<?php
						printf(
							/* translators: 1: product count, 2: subcategory count */
							esc_html( _n( '%1$s product', '%1$s products', (int) $star_term->count, 'star-electric-child' ) ) . ' &middot; ' .
							esc_html( _n( '%2$s subcategory', '%2$s subcategories', $star_subs, 'star-electric-child' ) ),
							esc_html( number_format_i18n( (int) $star_term->count ) ),
							esc_html( number_format_i18n( $star_subs ) )
						);
						?>
					</span>
				</span>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
