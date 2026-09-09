<?php
/**
 * Elementor integration.
 *
 * Elementor owns presentation and content; this plugin owns the business logic
 * and the markup the approved design system depends on. The widgets registered
 * here are the join between the two: each one exposes a section's *content* as
 * Elementor controls, and renders it through Star_Electric_Sections, which is
 * the same code the PHP templates call.
 *
 * That split is deliberate. The approved stylesheets target exact class names -
 * .hs, .rail, .mcard, .ptile, .why-card - so the structure of a section is not
 * something an editor should be able to rearrange without the design breaking.
 * What the section *says*, and which catalogue selection it shows, is.
 *
 * Nothing here loads unless Elementor is active, and nothing here decides
 * anything about a product: quote-only, pricing, stock and the catalogue
 * queries all stay in the plugin's own classes.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Star Electric widget category and widgets.
 */
class Star_Electric_Elementor {

	/** The widget category slug shown in the Elementor panel. */
	public const CATEGORY = 'star-electric';

	/**
	 * Every widget class, in the order they appear in the panel.
	 *
	 * @return string[] File slug => class name.
	 */
	private static function widgets(): array {
		return array(
			'campaign-banner'  => 'Star_Electric_Widget_Campaign_Banner',
			'trust-strip'      => 'Star_Electric_Widget_Trust_Strip',
			'department-strip' => 'Star_Electric_Widget_Department_Strip',
			'product-rails'    => 'Star_Electric_Widget_Product_Rails',
			'product-rail'     => 'Star_Electric_Widget_Product_Rail',
			'deals'            => 'Star_Electric_Widget_Deals',
			'brand-grid'       => 'Star_Electric_Widget_Brand_Grid',
			'bulk-cta'         => 'Star_Electric_Widget_Bulk_Cta',
			'why-choose-us'    => 'Star_Electric_Widget_Why_Choose_Us',
			'store-band'       => 'Star_Electric_Widget_Store_Band',
		);
	}

	/**
	 * Hook up, but only when Elementor is actually there.
	 */
	public static function init(): void {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'elementor/loaded', array( __CLASS__, 'boot' ) );
			return;
		}
		self::boot();
	}

	/**
	 * Register the category and the widgets.
	 */
	public static function boot(): void {
		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'category' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'register' ) );
	}

	/**
	 * A category of our own, so the widgets are not lost among Elementor's.
	 *
	 * @param \Elementor\Elements_Manager $manager Elements manager.
	 */
	public static function category( $manager ): void {
		$manager->add_category(
			self::CATEGORY,
			array(
				'title' => __( 'Star Electric', 'star-electric' ),
				'icon'  => 'eicon-bolt',
			)
		);
	}

	/**
	 * Load and register every widget.
	 *
	 * @param \Elementor\Widgets_Manager $manager Widgets manager.
	 */
	public static function register( $manager ): void {
		$dir = plugin_dir_path( __FILE__ ) . 'widgets/';

		require_once $dir . 'class-widget-base.php';

		foreach ( self::widgets() as $slug => $class ) {
			$file = $dir . 'class-' . $slug . '.php';
			if ( ! file_exists( $file ) ) {
				continue;
			}
			require_once $file;
			if ( class_exists( $class ) ) {
				$manager->register( new $class() );
			}
		}
	}
}
