<?php
/**
 * The product page.
 *
 * A port of product.html. Three things the approved page is careful about are
 * carried over deliberately:
 *
 *   - a product whose source published no price is never given a cart. It gets
 *     a quotation route instead, and no price appears anywhere on the page;
 *   - there is no delivery, returns or warranty row. The store has not supplied
 *     those terms and a public product page must not imply a policy that is not
 *     on record;
 *   - the reviews tab says there are none rather than inventing a rating, and
 *     the Additional Information tab is hidden when the record carries no notes.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	global $product;
	if ( ! $product instanceof WC_Product ) {
		$product = wc_get_product( get_the_ID() );
	}

	$star_id    = $product->get_id();
	$star_quote = class_exists( 'Star_Electric_Quote_Only' )
		? Star_Electric_Quote_Only::is_quote( $product )
		: ! $product->is_purchasable();

	$star_brands = wp_get_object_terms( $star_id, 'star_brand', array( 'fields' => 'names' ) );
	$star_brand  = ( ! is_wp_error( $star_brands ) && $star_brands ) ? $star_brands[0] : '';

	$star_cat_terms = wp_get_object_terms( $star_id, 'product_cat' );
	$star_cat       = ( ! is_wp_error( $star_cat_terms ) && $star_cat_terms ) ? $star_cat_terms[0] : null;

	$star_pill = Star_Electric_Shell::availability_pill( $product );

	$star_gallery = array_values( array_filter( array_merge(
		array( (int) $product->get_image_id() ),
		$product->get_gallery_image_ids()
	) ) );

	$star_note       = (string) $product->get_meta( '_star_electric_image_note' );
	$star_image_type = (string) $product->get_meta( '_star_electric_image_type' );
	$star_specs      = json_decode( (string) $product->get_meta( '_star_electric_specifications' ), true );
	$star_notes      = json_decode( (string) $product->get_meta( '_star_electric_notes' ), true );
	$star_specs      = is_array( $star_specs ) ? $star_specs : array();
	$star_notes      = is_array( $star_notes ) ? $star_notes : array();

	$star_off = 0;
	if ( ! $star_quote ) {
		$regular = (float) $product->get_regular_price();
		$sale    = (float) $product->get_price();
		if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
			$star_off = (int) round( ( 1 - ( $sale / $regular ) ) * 100 );
		}
	}
	?>

	<div class="page-head">
		<div class="container" id="breadcrumb">
			<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'star-electric-child' ); ?>">
				<ol class="breadcrumb">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'star-electric-child' ); ?></a></li>
					<li class="sep" aria-hidden="true">/</li>
					<li><a href="<?php echo esc_url( Star_Electric_Shell::url( 'shop' ) ); ?>"><?php esc_html_e( 'Shop', 'star-electric-child' ); ?></a></li>
					<?php if ( $star_cat instanceof WP_Term ) : ?>
						<li class="sep" aria-hidden="true">/</li>
						<li><a href="<?php echo esc_url( (string) get_term_link( $star_cat ) ); ?>"><?php echo esc_html( $star_cat->name ); ?></a></li>
					<?php endif; ?>
					<li class="sep" aria-hidden="true">/</li>
					<li aria-current="page"><?php the_title(); ?></li>
				</ol>
			</nav>
		</div>
	</div>

	<section class="section section--sm">
		<div class="container pdp">

			<div class="gallery">
				<?php if ( count( $star_gallery ) > 1 ) : ?>
					<div class="gallery__thumbs" id="galleryThumbs" role="tablist" aria-label="<?php esc_attr_e( 'Product images', 'star-electric-child' ); ?>">
						<?php foreach ( $star_gallery as $star_i => $star_att ) : ?>
							<button type="button" role="tab"
								aria-selected="<?php echo esc_attr( 0 === $star_i ? 'true' : 'false' ); ?>"
								class="<?php echo esc_attr( 0 === $star_i ? 'is-active' : '' ); ?>"
								data-full="<?php echo esc_url( (string) wp_get_attachment_image_url( $star_att, 'large' ) ); ?>">
								<?php echo wp_kses_post( wp_get_attachment_image( $star_att, 'woocommerce_gallery_thumbnail', false, array( 'alt' => '' ) ) ); ?>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<div class="gallery__main">
					<div class="gallery__badges" id="galleryBadges">
						<?php if ( $star_off > 0 ) : ?>
							<span class="tag tag--sale">-<?php echo esc_html( (string) $star_off ); ?>%</span>
						<?php endif; ?>
						<?php if ( $star_quote ) : ?>
							<span class="tag tag--quote"><?php esc_html_e( 'Request Quote', 'star-electric-child' ); ?></span>
						<?php endif; ?>
					</div>

					<?php if ( $star_gallery ) : ?>
						<img id="galleryMain"
							src="<?php echo esc_url( (string) wp_get_attachment_image_url( $star_gallery[0], 'large' ) ); ?>"
							alt="<?php echo esc_attr( $product->get_name() ); ?>"
							width="1200" height="900" fetchpriority="high" decoding="async">
					<?php else : ?>
						<p class="gallery__noimage">
							<?php esc_html_e( 'Product image unavailable — the approved source for this product does not publish a photograph.', 'star-electric-child' ); ?>
						</p>
					<?php endif; ?>
				</div>

				<?php if ( '' !== $star_note && 'exact-image' !== $star_image_type ) : ?>
					<p class="gallery__imgnote"><?php echo esc_html( $star_note ); ?></p>
				<?php endif; ?>
			</div>

			<div>
				<p class="pdp__brandrow">
					<span class="pdp__brand"><?php echo esc_html( $star_brand ); ?></span>
					<?php if ( $star_cat instanceof WP_Term ) : ?>
						<span class="t-xs t-faint"><?php esc_html_e( 'in', 'star-electric-child' ); ?> <?php echo esc_html( $star_cat->name ); ?></span>
					<?php endif; ?>
				</p>

				<h1 class="pdp__title"><?php the_title(); ?></h1>

				<?php if ( '' !== $product->get_sku() ) : ?>
					<div class="pdp__ratingrow">
						<span><?php esc_html_e( 'SKU:', 'star-electric-child' ); ?> <strong><?php echo esc_html( $product->get_sku() ); ?></strong></span>
					</div>
				<?php endif; ?>

				<div class="pdp__pricebox">
					<?php if ( $star_quote ) : ?>
						<p class="price price--lg price--quote">
							<span class="price__quote"><?php esc_html_e( 'Request a Quote', 'star-electric-child' ); ?></span>
						</p>
					<?php else : ?>
						<p class="price price--lg"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
					<?php endif; ?>
					<p style="margin-top:12px">
						<span class="pill <?php echo esc_attr( $star_pill[0] ); ?>"><?php echo esc_html( $star_pill[1] ); ?></span>
					</p>
				</div>

				<?php if ( '' !== $product->get_short_description() ) : ?>
					<div class="pdp__short"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div>
				<?php endif; ?>

				<?php if ( $star_quote ) : ?>

					<div class="pdp__buy">
						<a class="btn btn--accent btn--lg" href="<?php echo esc_url( Star_Electric_Shell::quote_url( $star_id ) ); ?>">
							<?php echo Star_Electric_Shell::icon( 'doc' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php esc_html_e( 'Request a Quote', 'star-electric-child' ); ?>
						</a>
					</div>
					<p class="field__hint" style="margin-bottom:24px">
						<?php esc_html_e( 'This product’s source does not publish a price, so it is quoted on enquiry.', 'star-electric-child' ); ?>
					</p>

				<?php elseif ( $product->is_type( 'variable' ) ) : ?>

					<div class="variations" id="pdpVariations">
						<?php woocommerce_variable_add_to_cart(); ?>
					</div>

				<?php elseif ( $product->is_in_stock() ) : ?>

					<form class="cart" method="post" enctype="multipart/form-data"
						action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>">
						<div class="field" style="max-width:170px;margin-bottom:20px">
							<label class="field__label" for="pdpQty"><?php esc_html_e( 'Quantity', 'star-electric-child' ); ?></label>
							<div class="qty">
								<button type="button" data-qty="down" aria-label="<?php esc_attr_e( 'Decrease quantity', 'star-electric-child' ); ?>">
									<?php echo Star_Electric_Shell::icon( 'minus' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</button>
								<input id="pdpQty" name="quantity" type="number" min="1" value="1" aria-label="<?php esc_attr_e( 'Quantity', 'star-electric-child' ); ?>">
								<button type="button" data-qty="up" aria-label="<?php esc_attr_e( 'Increase quantity', 'star-electric-child' ); ?>">
									<?php echo Star_Electric_Shell::icon( 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</button>
							</div>
						</div>

						<div class="pdp__buy">
							<button class="btn btn--accent btn--lg" type="submit" name="add-to-cart" value="<?php echo esc_attr( (string) $star_id ); ?>">
								<?php echo Star_Electric_Shell::icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php esc_html_e( 'Add to Cart', 'star-electric-child' ); ?>
							</button>
							<a class="btn btn--primary btn--lg" href="<?php echo esc_url( add_query_arg( 'add-to-cart', $star_id, Star_Electric_Shell::url( 'checkout' ) ) ); ?>">
								<?php esc_html_e( 'Buy Now', 'star-electric-child' ); ?>
							</a>
						</div>
					</form>

				<?php else : ?>

					<div class="pdp__buy">
						<button class="btn btn--ghost btn--lg" type="button" disabled>
							<?php esc_html_e( 'Out of Stock', 'star-electric-child' ); ?>
						</button>
						<a class="btn btn--accent btn--lg" href="<?php echo esc_url( Star_Electric_Shell::quote_url( $star_id ) ); ?>">
							<?php esc_html_e( 'Request a Quote', 'star-electric-child' ); ?>
						</a>
					</div>

				<?php endif; ?>

				<div class="btn-row" style="margin-bottom:24px">
					<a class="btn btn--ghost" href="<?php echo esc_url( Star_Electric_Shell::url( 'contact' ) ); ?>">
						<?php echo Star_Electric_Shell::icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php esc_html_e( 'Ask about this product', 'star-electric-child' ); ?>
					</a>
					<a class="btn btn--ghost" href="<?php echo esc_url( Star_Electric_Shell::url( 'quote' ) ); ?>">
						<?php esc_html_e( 'Request Bulk Quote', 'star-electric-child' ); ?>
					</a>
				</div>

				<?php
				$star_domain = (string) $product->get_meta( '_star_electric_source_domain' );
				$star_url    = (string) $product->get_meta( '_star_electric_source_url' );
				$star_model  = (string) $product->get_meta( '_star_electric_model' );
				?>
				<dl class="pdp__meta">
					<?php if ( '' !== $star_model ) : ?>
						<div><dt><?php esc_html_e( 'Model', 'star-electric-child' ); ?></dt><dd><?php echo esc_html( $star_model ); ?></dd></div>
					<?php endif; ?>
					<?php if ( $star_cat instanceof WP_Term ) : ?>
						<div><dt><?php esc_html_e( 'Category', 'star-electric-child' ); ?></dt><dd><?php echo esc_html( $star_cat->name ); ?></dd></div>
					<?php endif; ?>
					<?php if ( '' !== $star_domain ) : ?>
						<div>
							<dt><?php esc_html_e( 'Source', 'star-electric-child' ); ?></dt>
							<dd>
								<?php if ( '' !== $star_url ) : ?>
									<a class="link-inline" href="<?php echo esc_url( $star_url ); ?>" target="_blank" rel="noopener nofollow"><?php echo esc_html( $star_domain ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $star_domain ); ?>
								<?php endif; ?>
							</dd>
						</div>
					<?php endif; ?>
				</dl>
			</div>
		</div>
	</section>

	<section class="section section--sm">
		<div class="container">
			<div data-tabs>
				<div class="tabs" role="tablist" aria-label="<?php esc_attr_e( 'Product information', 'star-electric-child' ); ?>">
					<button role="tab" id="tabDesc" aria-controls="panelDesc" aria-selected="true"><?php esc_html_e( 'Description', 'star-electric-child' ); ?></button>
					<?php if ( $star_specs ) : ?>
						<button role="tab" id="tabSpecs" aria-controls="panelSpecs" aria-selected="false"><?php esc_html_e( 'Specifications', 'star-electric-child' ); ?></button>
					<?php endif; ?>
					<?php if ( $star_notes ) : ?>
						<button role="tab" id="tabAdd" aria-controls="panelAdd" aria-selected="false"><?php esc_html_e( 'Additional Information', 'star-electric-child' ); ?></button>
					<?php endif; ?>
					<button role="tab" id="tabRev" aria-controls="panelRev" aria-selected="false"><?php esc_html_e( 'Reviews', 'star-electric-child' ); ?></button>
				</div>

				<div class="tab-panel" id="panelDesc" role="tabpanel" aria-labelledby="tabDesc">
					<div class="prose">
						<?php
						$star_desc = (string) $product->get_description();
						if ( '' !== trim( wp_strip_all_tags( $star_desc ) ) ) {
							echo wp_kses_post( $star_desc );
						} else {
							echo '<p>' . esc_html__( 'This product’s source does not publish a description.', 'star-electric-child' ) . '</p>';
						}
						?>
					</div>
				</div>

				<?php if ( $star_specs ) : ?>
					<div class="tab-panel" id="panelSpecs" role="tabpanel" aria-labelledby="tabSpecs" hidden>
						<table class="table table--specs">
							<caption class="sr-only"><?php esc_html_e( 'Product specifications', 'star-electric-child' ); ?></caption>
							<tbody>
								<?php foreach ( $star_specs as $star_key => $star_value ) : ?>
									<tr>
										<th scope="row"><?php echo esc_html( (string) $star_key ); ?></th>
										<td><?php echo esc_html( is_scalar( $star_value ) ? (string) $star_value : wp_json_encode( $star_value ) ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>

				<?php if ( $star_notes ) : ?>
					<div class="tab-panel" id="panelAdd" role="tabpanel" aria-labelledby="tabAdd" hidden>
						<ul class="prose">
							<?php foreach ( $star_notes as $star_line ) : ?>
								<li><?php echo esc_html( (string) $star_line ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<div class="tab-panel" id="panelRev" role="tabpanel" aria-labelledby="tabRev" hidden>
					<div class="empty-state">
						<span class="empty-state__ico">
							<?php echo Star_Electric_Shell::icon( 'star' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<h3><?php esc_html_e( 'No reviews yet', 'star-electric-child' ); ?></h3>
						<p><?php esc_html_e( 'No customer reviews have been published for this product.', 'star-electric-child' ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<?php
	$star_related = wc_get_related_products( $star_id, 5 );
	if ( $star_related ) :
		?>
		<section class="section section--tint" aria-labelledby="relTitle">
			<div class="container">
				<div class="section__head">
					<div>
						<h2 class="section__title" id="relTitle"><?php esc_html_e( 'Related Products', 'star-electric-child' ); ?></h2>
						<p class="section__sub"><?php esc_html_e( 'Other items from the same department.', 'star-electric-child' ); ?></p>
					</div>
					<a class="link-more" href="<?php echo esc_url( Star_Electric_Shell::url( 'shop' ) ); ?>">
						<?php esc_html_e( 'Shop all', 'star-electric-child' ); ?>
						<?php echo Star_Electric_Shell::icon( 'arrowright' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				</div>
				<ul class="product-grid">
					<?php
					$star_keep = $product;
					foreach ( $star_related as $star_rel_id ) {
						$star_rel = wc_get_product( $star_rel_id );
						if ( ! $star_rel instanceof WC_Product ) {
							continue;
						}
						$GLOBALS['post']    = get_post( $star_rel_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
						$GLOBALS['product'] = $star_rel; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
						setup_postdata( $GLOBALS['post'] );
						wc_get_template_part( 'content', 'product' );
					}
					wp_reset_postdata();
					$GLOBALS['product'] = $star_keep; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
					?>
				</ul>
			</div>
		</section>
		<?php
	endif;

endwhile;

get_footer();
