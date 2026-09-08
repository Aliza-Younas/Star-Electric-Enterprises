<?php
/**
 * Active filter chips.
 *
 * Each chip's remove control is a link carrying the URL with that one value
 * dropped, so filters can be removed one at a time without JavaScript.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$star_chips = Star_Electric_Filters::chips();
?>
<div class="chips" id="activeChips" style="margin-bottom:16px">
	<?php foreach ( $star_chips as $star_chip ) : ?>
		<span class="chip">
			<?php echo esc_html( $star_chip['label'] ); ?>
			<button type="button" data-chip-href="<?php echo esc_url( $star_chip['url'] ); ?>"
				aria-label="<?php esc_attr_e( 'Remove filter', 'star-electric-child' ); ?>">
				<?php echo Star_Electric_Shell::icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</span>
	<?php endforeach; ?>
	<?php if ( $star_chips ) : ?>
		<a class="btn btn--quiet btn--sm" href="<?php echo esc_url( Star_Electric_Filters::clear_url() ); ?>">
			<?php esc_html_e( 'Clear all', 'star-electric-child' ); ?>
		</a>
	<?php endif; ?>
</div>
