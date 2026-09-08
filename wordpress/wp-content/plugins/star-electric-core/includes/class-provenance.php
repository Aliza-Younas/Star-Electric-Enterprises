<?php
/**
 * Source provenance.
 *
 * Every product and image in this catalogue came from a named approved source
 * and that record has to survive the migration. It is stored as post meta and
 * surfaced in the admin, so an editor can always answer "where did this come
 * from?" without going back to the static project.
 *
 * The customer-facing part is deliberately small: the product page shows the
 * source and, where the image is not an exact photograph of that item, the note
 * explaining what it is instead. Nothing else is published.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores and displays where catalogue data came from.
 */
class Star_Electric_Provenance {

	/**
	 * Hook up the admin panel and the front-end note.
	 */
	public static function init(): void {
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_box' ) );
		add_action( 'woocommerce_product_thumbnails', array( __CLASS__, 'image_note' ), 20 );
	}

	/**
	 * A read-only provenance panel on the product editor.
	 */
	public static function meta_box(): void {
		foreach ( array( 'product', Star_Electric_Ranges::POST_TYPE ) as $screen ) {
			add_meta_box(
				'star-electric-provenance',
				__( 'Source provenance', 'star-electric' ),
				array( __CLASS__, 'render_meta_box' ),
				$screen,
				'side',
				'default'
			);
		}
	}

	/**
	 * Render the panel.
	 *
	 * @param WP_Post $post Post being edited.
	 */
	public static function render_meta_box( WP_Post $post ): void {
		$fields = array(
			'_star_electric_source_id'     => __( 'Source ID', 'star-electric' ),
			'_star_electric_source_domain' => __( 'Source', 'star-electric' ),
			'_star_electric_source_url'    => __( 'Source URL', 'star-electric' ),
			'_star_electric_source_checked' => __( 'Checked', 'star-electric' ),
			'_star_electric_image_type'    => __( 'Image type', 'star-electric' ),
			'_star_electric_image_note'    => __( 'Image note', 'star-electric' ),
			'_star_electric_model'         => __( 'Model', 'star-electric' ),
			'_star_electric_series'        => __( 'Series', 'star-electric' ),
		);

		echo '<p class="description">'
			. esc_html__( 'Imported from the approved source catalogue. Read only.', 'star-electric' )
			. '</p><table class="widefat striped"><tbody>';

		foreach ( $fields as $key => $label ) {
			$value = get_post_meta( $post->ID, $key, true );
			if ( '' === $value ) {
				continue;
			}
			echo '<tr><th scope="row" style="width:38%">' . esc_html( $label ) . '</th><td>';
			if ( '_star_electric_source_url' === $key ) {
				printf(
					'<a href="%s" target="_blank" rel="noopener nofollow">%s</a>',
					esc_url( (string) $value ),
					esc_html__( 'open', 'star-electric' )
				);
			} else {
				echo esc_html( (string) $value );
			}
			echo '</td></tr>';
		}

		echo '</tbody></table>';
	}

	/**
	 * Show the image note under the product gallery.
	 *
	 * Only printed when the image is not an exact photograph of this item, which
	 * is a fact the customer is entitled to know.
	 */
	public static function image_note(): void {
		global $product;

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$note = (string) $product->get_meta( '_star_electric_image_note' );
		$type = (string) $product->get_meta( '_star_electric_image_type' );

		if ( '' === $note || 'exact-image' === $type ) {
			return;
		}

		$label = 'representative-image' === $type
			? __( 'Representative image.', 'star-electric' )
			: __( 'Manufacturer range image.', 'star-electric' );

		printf(
			'<p class="pdp__imgnote"><strong>%s</strong> %s</p>',
			esc_html( $label ),
			esc_html( $note )
		);
	}
}
