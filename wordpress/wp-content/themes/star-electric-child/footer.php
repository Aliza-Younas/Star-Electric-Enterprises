<?php
/**
 * The global footer and the end of the document.
 *
 * The footer markup lives in Star_Electric_Chrome in the Core plugin, so that
 * Elementor's Theme Builder and this template render exactly the same thing. If
 * a Footer template exists in Theme Builder, Elementor renders it and this
 * template steps aside; if none does, the renderer is called directly and the
 * site still has its footer.
 *
 * @package StarElectricChild
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>

<?php
$star_from_elementor = function_exists( 'elementor_theme_do_location' )
	&& elementor_theme_do_location( 'footer' );

if ( ! $star_from_elementor && class_exists( 'Star_Electric_Chrome' ) ) {
	Star_Electric_Chrome::site_footer();
}
?>

<?php wp_footer(); ?>
</body>
</html>
