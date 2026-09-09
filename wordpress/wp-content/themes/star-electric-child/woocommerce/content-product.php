<?php
/**
 * One product card in a WooCommerce loop.
 *
 * The direct port of SEE_UI.renderProductCard() from the approved storefront.
 * Class names and element order are identical, because this theme ships that
 * storefront's stylesheets unchanged.
 *
 * Two rules from the approved design are enforced here rather than assumed:
 * a product with no published price shows "Request a Quote" and gets a
 * quotation link instead of an add-to-cart button, and badges state facts only
 * - a discount badge requires a real source discount, and there is no
 * "best seller", because no approved source ranks sales.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
	return;
}

$star_id    = $product->get_id();
$star_link  = get_permalink( $star_id );
$star_quote = class_exists( 'Star_Electric_Quote_Only' )
	? Star_Electric_Quote_Only::is_quote( $product )
	: ( '' === (string) $product->get_price( 'edit' ) );

$star_brand = wp_get_object_terms( $star_id, 'star_brand', array( 'fields' => 'names' ) );
$star_brand = ( ! is_wp_error( $star_brand ) && $star_brand ) ? $star_brand[0] : '';

/*
 * The card names the department, not the leaf subcategory a product happens to
 * be filed under: the approved card reads "ELECTRICAL ACCESSORIES", and
 * "ACCESSORIES" on its own tells a shopper nothing about where they are.
 */
$star_cats = wp_get_object_terms( $star_id, 'product_cat' );
$star_cat  = '';
if ( ! is_wp_error( $star_cats ) && $star_cats ) {
	foreach ( $star_cats as $star_term ) {
		if ( 0 === (int) $star_term->parent ) {
			$star_cat = $star_term->name;
			break;
		}
	}
	if ( '' === $star_cat ) {
		$star_top = get_term( (int) $star_cats[0]->parent, 'product_cat' );
		$star_cat = $star_top instanceof WP_Term ? $star_top->name : $star_cats[0]->name;
	}
}

$star_model = (string) $product->get_meta( '_star_electric_model' );

// A discount badge is only honest when both figures are real.
$star_off = 0;
if ( ! $star_quote ) {
	$regular = (float) $product->get_regular_price();
	$sale    = (float) $product->get_price();
	if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
		$star_off = (int) round( ( 1 - ( $sale / $regular ) ) * 100 );
	}
}

$star_pill = Star_Electric_Shell::availability_pill( $product );
?>
<li class="pcard" data-id="<?php echo esc_attr( (string) $star_id ); ?>">
	<div class="pcard__media">
		<div class="pcard__badges">
			<?php if ( $star_off > 0 ) : ?>
				<span class="tag tag--sale">-<?php echo esc_html( (string) $star_off ); ?>%</span>
			<?php endif; ?>
			<?php if ( $star_quote ) : ?>
				<span class="tag tag--quote"><?php esc_html_e( 'Request Quote', 'star-electric-child' ); ?></span>
			<?php endif; ?>
		</div>
		<div class="pcard__tools">
			<button class="tool-btn js-wish" type="button" data-id="<?php echo esc_attr( (string) $star_id ); ?>"
				aria-pressed="false" aria-label="<?php esc_attr_e( 'Add to wishlist', 'star-electric-child' ); ?>">
				<?php echo Star_Electric_Shell::icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
			<button class="tool-btn js-quick" type="button" data-id="<?php echo esc_attr( (string) $star_id ); ?>"
				aria-label="<?php esc_attr_e( 'Quick view', 'star-electric-child' ); ?>">
				<?php echo Star_Electric_Shell::icon( 'eye' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>
		<a href="<?php echo esc_url( (string) $star_link ); ?>" tabindex="-1">
			<?php if ( has_post_thumbnail( $star_id ) ) : ?>
				<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy', 'decoding' => 'async' ) ) ); ?>
			<?php else : ?>
				<span class="pcard__noimg"><?php esc_html_e( 'Product image unavailable', 'star-electric-child' ); ?></span>
			<?php endif; ?>
		</a>
	</div>

	<div class="pcard__body">
		<p class="pcard__meta">
			<span class="pcard__brand"><?php echo esc_html( $star_brand ); ?></span>
			<span class="pcard__cat"><?php echo esc_html( $star_cat ); ?></span>
		</p>
		<h3 class="pcard__name">
			<a href="<?php echo esc_url( (string) $star_link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
		</h3>
		<?php if ( '' !== $star_model ) : ?>
			<p class="pcard__model"><?php esc_html_e( 'Model:', 'star-electric-child' ); ?> <?php echo esc_html( $star_model ); ?></p>
		<?php endif; ?>

		<p class="price<?php echo esc_attr( $star_quote ? ' price--quote' : '' ); ?>">
			<?php echo wp_kses_post( Star_Electric_Quote_Only::price_block( $product ) ); ?>
		</p>

		<span class="pill <?php echo esc_attr( $star_pill[0] ); ?>"><?php echo esc_html( $star_pill[1] ); ?></span>

		<?php if ( $star_quote ) : ?>
			<a class="btn btn--accent btn--block pcard__add" href="<?php echo esc_url( Star_Electric_Shell::quote_url( $star_id ) ); ?>">
				<?php esc_html_e( 'Request a Quote', 'star-electric-child' ); ?>
			</a>
		<?php elseif ( ! $product->is_in_stock() ) : ?>
			<button class="btn btn--ghost btn--block pcard__add" type="button" disabled>
				<?php esc_html_e( 'Out of Stock', 'star-electric-child' ); ?>
			</button>
		<?php elseif ( $product->is_type( 'variable' ) ) : ?>
			<a class="btn btn--ghost btn--block pcard__add" href="<?php echo esc_url( (string) $star_link ); ?>">
				<?php esc_html_e( 'Select Options', 'star-electric-child' ); ?>
			</a>
		<?php else : ?>
			<a class="btn btn--ghost btn--block pcard__add add_to_cart_button ajax_add_to_cart"
				href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
				data-product_id="<?php echo esc_attr( (string) $star_id ); ?>"
				data-quantity="1" rel="nofollow">
				<?php echo Star_Electric_Shell::icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php esc_html_e( 'Add to Cart', 'star-electric-child' ); ?>
			</a>
		<?php endif; ?>
	</div>
</li>
