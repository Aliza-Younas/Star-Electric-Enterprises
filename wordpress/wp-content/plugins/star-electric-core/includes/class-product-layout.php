<?php
/**
 * Every product's own Elementor layout.
 *
 * A product page used to be drawn by one shared Theme Builder template, which
 * meant every product's page was the same page: moving a section on one moved
 * it on all 4,348. Now each product owns an Elementor document of its own, and
 * the Theme Builder keeps a single shell whose whole job is to print it.
 *
 * This class is what makes that practical. It holds the approved default
 * layout, seeds it onto a product that has none, and remembers - in one meta
 * key - whether a product is still carrying that default or has been edited by
 * a person. A product that has been edited is never written to again.
 *
 * What it does NOT do is copy anything. A seeded document contains no product
 * title, price, SKU, stock state, brand or photograph: it is a list of widgets,
 * each of which asks WooCommerce and Star Electric Core for the product being
 * viewed. Every widget's wording comes from its own defaults, so a seeded
 * document is a couple of kilobytes and a change of wording in the plugin
 * reaches every product that has not been customised.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Per-product Elementor documents.
 */
class Star_Electric_Product_Layout {

	/**
	 * The approved default layout's version.
	 *
	 * Raise it when the default structure changes and every *untouched* product
	 * should pick the change up. Customised products are never affected.
	 */
	public const VERSION = 1;

	/**
	 * What a product is carrying: "seed:<version>:<fingerprint of the layout>".
	 *
	 * The fingerprint is what makes the difference between a product still
	 * showing the default and one somebody has made their own. Asking Elementor
	 * to tell us instead does not work: opening the editor saves the document it
	 * is about to show, so "was it saved" would call every product anyone had
	 * ever looked at customised.
	 */
	public const MARKER = '_star_electric_layout';

	/**
	 * Hook up.
	 */
	public static function init(): void {
		/*
		 * "Edit with Elementor" on a product. Elementor decides which post types
		 * it will open by asking WordPress whether they support 'elementor', and
		 * turns that support on for whatever is ticked in its own settings. It is
		 * declared here instead so that it travels with this plugin: a product
		 * page that is a per-product Elementor document has to be openable in
		 * Elementor, and that is not a preference to be toggled off by accident.
		 */
		add_action( 'init', array( __CLASS__, 'support' ), 20 );

		// A newly imported or newly published product gets the starter layout,
		// so nothing ever has to be seeded by hand again.
		add_action( 'save_post_product', array( __CLASS__, 'on_save' ), 20, 3 );

		/*
		 * "Edit with Elementor" beside every product in the list. Elementor adds
		 * that row action itself for pages and posts, but WooCommerce curates the
		 * product list's row actions and Elementor's does not survive it, so it is
		 * put back here - late, and only when it is not already there.
		 */
		add_filter( 'post_row_actions', array( __CLASS__, 'row_action' ), 100, 2 );

		// The backfill, one batch per request, on the catalogue import screen.
		add_action( 'wp_ajax_star_electric_layouts', array( __CLASS__, 'ajax' ) );
	}

	/**
	 * One batch of seeding, and where the next one starts.
	 *
	 * Same shape as the import screen's own endpoints: a small request that
	 * finishes well inside any execution limit and says where to resume, so the
	 * backfill survives a shared host and a closed browser tab.
	 */
	public static function ajax(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Not allowed.' ), 403 );
		}
		check_ajax_referer( 'star_electric_import' );

		$offset = isset( $_POST['offset'] ) ? max( 0, (int) $_POST['offset'] ) : 0;
		$size   = isset( $_POST['size'] ) ? (int) $_POST['size'] : 50;

		// A size of zero asks only where things stand, without seeding anything.
		wp_send_json_success(
			array(
				'batch' => $size > 0 ? self::step( $offset, $size ) : null,
				'state' => self::state(),
			)
		);
	}

	/**
	 * The row action that opens one product in Elementor.
	 *
	 * @param array   $actions Row actions.
	 * @param WP_Post $post    The row's post.
	 * @return array
	 */
	public static function row_action( $actions, $post ): array {
		$actions = (array) $actions;

		if ( ! $post instanceof WP_Post || 'product' !== $post->post_type ) {
			return $actions;
		}
		if ( isset( $actions['edit_with_elementor'] ) || ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}

		$actions['edit_with_elementor'] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'post.php?post=' . $post->ID . '&action=elementor' ) ),
			esc_html__( 'Edit with Elementor', 'star-electric' )
		);

		return $actions;
	}

	/**
	 * Let Elementor open a product.
	 */
	public static function support(): void {
		add_post_type_support( 'product', 'elementor' );
	}

	/* --------------------------------------------------------------------- *
	 * The approved default layout
	 * --------------------------------------------------------------------- */

	/**
	 * One element of an Elementor document.
	 *
	 * Settings are deliberately left empty. Every widget's wording lives in its
	 * own defaults, so an unedited product carries none of it in the database -
	 * which is both a great deal smaller and the reason a wording change in the
	 * plugin reaches every product that has not been customised.
	 *
	 * @param string $type     container or widget.
	 * @param array  $settings Element settings.
	 * @param array  $children Child elements.
	 * @param string $widget   Widget type, for a widget.
	 */
	private static function element( string $type, array $settings = array(), array $children = array(), string $widget = '' ): array {
		$element = array(
			'id'       => substr( md5( uniqid( '', true ) ), 0, 7 ),
			'elType'   => $type,
			'settings' => $settings,
			'elements' => $children,
		);
		if ( 'widget' === $type ) {
			$element['widgetType'] = $widget;
		}
		return $element;
	}

	/**
	 * A widget, named as the Navigator should show it.
	 *
	 * @param string $widget Widget type.
	 * @param string $title  Navigator name.
	 */
	private static function widget( string $widget, string $title ): array {
		return self::element( 'widget', array( '_title' => $title ), array(), $widget );
	}

	/**
	 * A container that adds no layout of its own.
	 *
	 * The approved sections are full-width elements carrying their own width
	 * and padding, so the container around them has to be invisible.
	 *
	 * @param array  $children Child elements.
	 * @param string $title    Navigator name.
	 */
	private static function plain( array $children, string $title ): array {
		return self::element(
			'container',
			array(
				'_title'        => $title,
				'content_width' => 'full',
				'padding'       => array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ),
				'margin'        => array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ),
				'flex_gap'      => array( 'unit' => 'px', 'size' => 0, 'column' => '0', 'row' => '0' ),
			),
			$children
		);
	}

	/**
	 * A container that carries one of the approved layout classes.
	 *
	 * The detail block is a two-column grid the approved stylesheet already
	 * knows how to draw. Giving the container that class, and letting the
	 * stylesheet step Elementor's own wrappers out of the way, is what lets the
	 * gallery and the three columns beside it be four separate widgets rather
	 * than one.
	 *
	 * @param array  $children Child elements.
	 * @param string $title    Navigator name.
	 * @param string $classes  Approved CSS classes.
	 * @param string $tag      HTML tag.
	 */
	private static function shell( array $children, string $title, string $classes, string $tag = 'div' ): array {
		return self::element(
			'container',
			array(
				'_title'         => $title,
				'content_width'  => 'full',
				'css_classes'    => $classes,
				'html_tag'       => $tag,
				'padding'        => array( 'unit' => 'px', 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'isLinked' => true ),
				'flex_gap'       => array( 'unit' => 'px', 'size' => 0, 'column' => '0', 'row' => '0' ),
			),
			$children
		);
	}

	/**
	 * The approved default product page, as an Elementor document.
	 *
	 * @return array
	 */
	public static function elements(): array {
		return array(
			self::plain(
				array( self::widget( 'star-product-head', 'PRODUCT — Breadcrumbs' ) ),
				'PRODUCT — Breadcrumbs'
			),

			self::shell(
				array(
					self::shell(
						array(
							self::widget( 'star-product-gallery', 'PRODUCT — Gallery' ),
							self::plain(
								array(
									self::widget( 'star-product-info', 'PRODUCT — Main Information' ),
									self::widget( 'star-product-purchase', 'PRODUCT — Purchase / Quote' ),
									self::widget( 'star-product-record', 'PRODUCT — Product Record' ),
								),
								'PRODUCT — Summary Column'
							),
						),
						'PRODUCT — Detail Grid',
						'container pdp'
					),
				),
				'PRODUCT — Detail',
				'section section--sm',
				'section'
			),

			self::plain(
				array( self::widget( 'star-product-tabs', 'PRODUCT — Description & Specifications' ) ),
				'PRODUCT — Description & Specifications'
			),
			self::plain(
				array( self::widget( 'star-product-related', 'PRODUCT — Related Products' ) ),
				'PRODUCT — Related Products'
			),
			self::plain(
				array( self::widget( 'star-product-recent', 'PRODUCT — Recently Viewed' ) ),
				'PRODUCT — Recently Viewed'
			),
		);
	}

	/* --------------------------------------------------------------------- *
	 * Seeding
	 * --------------------------------------------------------------------- */

	/**
	 * What a product is carrying: "custom", "seed:<n>", or "" for nothing.
	 *
	 * @param int $product_id Product id.
	 */
	public static function marker( int $product_id ): string {
		return (string) get_post_meta( $product_id, self::MARKER, true );
	}

	/**
	 * Whether a product's page has been made its own.
	 *
	 * A product is customised when it has an Elementor document that is not the
	 * one seeding left behind. A product with a document but no marker of ours
	 * was built before this system existed, or by some other route; it counts as
	 * customised too, because the safe assumption about a page somebody made is
	 * that they meant it.
	 *
	 * @param int $product_id Product id.
	 */
	public static function is_custom( int $product_id ): bool {
		$data = (string) get_post_meta( $product_id, '_elementor_data', true );
		if ( '' === $data || '[]' === $data ) {
			return false;
		}

		$marker = self::marker( $product_id );
		if ( '' === $marker ) {
			return true;
		}

		$stored = json_decode( $data, true );
		return ! is_array( $stored ) || $marker !== self::stamp( self::shape( $stored ) );
	}

	/**
	 * A document's shape, with the element ids left out.
	 *
	 * Elementor gives every element a fresh id, so two documents that are the
	 * same page differ byte for byte. This is what they have in common.
	 *
	 * @param array $elements Elements.
	 */
	private static function shape( array $elements ): string {
		$strip = static function ( array $list, callable $self ) {
			$out = array();
			foreach ( $list as $element ) {
				$out[] = array(
					'elType'     => $element['elType'] ?? '',
					'widgetType' => $element['widgetType'] ?? '',
					'settings'   => $element['settings'] ?? array(),
					'elements'   => $self( (array) ( $element['elements'] ?? array() ), $self ),
				);
			}
			return $out;
		};

		return (string) wp_json_encode( $strip( $elements, $strip ) );
	}

	/**
	 * The marker a document of exactly this shape would carry.
	 *
	 * @param string $shape The document's shape.
	 */
	private static function stamp( string $shape ): string {
		return 'seed:' . self::VERSION . ':' . md5( $shape );
	}

	/**
	 * Give one product the approved default layout.
	 *
	 * Idempotent, and safe to run again: a product that already carries the
	 * current default is left alone, and a product somebody has edited is never
	 * written to unless it is asked for explicitly.
	 *
	 * @param int  $product_id Product id.
	 * @param bool $force      Overwrite a customised page. Only ever on request.
	 * @return string seeded, current, custom or skipped.
	 */
	public static function seed( int $product_id, bool $force = false ): string {
		if ( 'product' !== get_post_type( $product_id ) ) {
			return 'skipped';
		}

		if ( ! $force && self::is_custom( $product_id ) ) {
			return 'custom';
		}

		$data = wp_json_encode( self::elements() );
		if ( ! is_string( $data ) ) {
			return 'skipped';
		}

		/*
		 * Element ids are generated fresh every time, so the JSON never matches
		 * byte for byte. The fingerprint is taken over the shape - the widgets,
		 * their order and their settings - which is what actually decides
		 * whether a product is still showing the default.
		 */
		$stamp = self::stamp( self::shape( self::elements() ) );

		if ( ! $force && self::marker( $product_id ) === $stamp
			&& '' !== (string) get_post_meta( $product_id, '_elementor_data', true ) ) {
			return 'current';
		}

		// wp_slash, because update_post_meta unslashes what it is given and the
		// JSON is full of backslashes Elementor needs to get back intact.
		update_post_meta( $product_id, '_elementor_data', wp_slash( $data ) );
		update_post_meta( $product_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $product_id, '_elementor_template_type', 'product-post' );
		update_post_meta( $product_id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.0.0' );
		update_post_meta( $product_id, self::MARKER, $stamp );

		// The per-page CSS file is rebuilt from the new data on the next view.
		delete_post_meta( $product_id, '_elementor_css' );

		return 'seeded';
	}

	/**
	 * Seed a batch, oldest id first, and say where the next one starts.
	 *
	 * @param int $offset Where to start.
	 * @param int $size   How many to look at.
	 * @return array
	 */
	public static function step( int $offset = 0, int $size = 50 ): array {
		global $wpdb;

		$size = max( 1, min( 200, $size ) );

		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts}
				  WHERE post_type = 'product' AND post_status IN ( 'publish', 'private', 'draft' )
				  ORDER BY ID ASC LIMIT %d OFFSET %d",
				$size,
				$offset
			)
		);

		$counts = array( 'seeded' => 0, 'current' => 0, 'custom' => 0, 'skipped' => 0 );
		foreach ( $ids as $id ) {
			++$counts[ self::seed( (int) $id ) ];
		}

		$counts['looked_at'] = count( $ids );
		$counts['next']      = $offset + count( $ids );
		$counts['done']      = count( $ids ) < $size;

		return $counts;
	}

	/**
	 * How far the seeding has got.
	 *
	 * @return array
	 */
	public static function state(): array {
		global $wpdb;

		$total = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->posts}
			  WHERE post_type = 'product' AND post_status IN ( 'publish', 'private', 'draft' )"
		);

		$stamp = self::stamp( self::shape( self::elements() ) );

		$seeded = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->postmeta}
				  WHERE meta_key = %s AND meta_value = %s",
				self::MARKER,
				$stamp
			)
		);

		/*
		 * Customised means: has a document, and it is not the one seeding would
		 * leave behind. Counting it in SQL means joining the marker against the
		 * data, which is what this asks: a product with Elementor data whose
		 * marker is missing or stale.
		 */
		$custom = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->posts} p
				   INNER JOIN {$wpdb->postmeta} d ON d.post_id = p.ID AND d.meta_key = '_elementor_data'
				   LEFT JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = %s
				  WHERE p.post_type = 'product'
				    AND p.post_status IN ( 'publish', 'private', 'draft' )
				    AND d.meta_value NOT IN ( '', '[]' )
				    AND ( m.meta_value IS NULL OR m.meta_value <> %s )",
				self::MARKER,
				$stamp
			)
		);

		return array(
			'products'   => $total,
			'seeded'     => $seeded,
			'customised' => $custom,
			'remaining'  => max( 0, $total - $seeded - $custom ),
			'version'    => self::VERSION,
		);
	}

	/* --------------------------------------------------------------------- *
	 * Keeping up with the catalogue
	 * --------------------------------------------------------------------- */

	/**
	 * A new product gets the starter layout the moment it exists.
	 *
	 * @param int     $post_id Post id.
	 * @param WP_Post $post    Post.
	 * @param bool    $update  Whether this is an update rather than a create.
	 */
	public static function on_save( $post_id, $post = null, $update = false ): void {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}
		if ( $post instanceof WP_Post && 'auto-draft' === $post->post_status ) {
			return;
		}
		self::seed( (int) $post_id );
	}

}
