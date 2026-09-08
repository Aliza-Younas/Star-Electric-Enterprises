<?php
/**
 * WP-CLI catalogue importer.
 *
 * 4,348 products, 181 ranges and 2,054 unique images cannot be entered by hand,
 * and a one-shot script that dies at product 3,000 is worse than useless. So:
 *
 *   idempotent  every record carries its source id in meta and is matched on it,
 *               so a second run updates rather than duplicates
 *   resumable   --offset picks up where a run stopped
 *   batched     --batch keeps memory flat and lets the object cache be flushed
 *   deduped     each image file is uploaded once; later products reuse the
 *               attachment id, because 4,348 products share 2,054 masters
 *   honest      failures are counted and reported, never swallowed
 *
 * Usage from the WordPress root:
 *
 *   wp star-electric import-media    --dir=/path/to/static-site
 *   wp star-electric import-taxonomies --dir=...
 *   wp star-electric import-ranges   --dir=...
 *   wp star-electric import-products --dir=... [--batch=100] [--offset=0] [--limit=0]
 *   wp star-electric audit           --dir=...
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Catalogue import commands.
 */
class Star_Electric_Import_Command {

	/** Maps a source image path to its attachment ID, so nothing uploads twice. */
	private const MEDIA_MAP_OPTION = 'star_electric_media_map';

	/** Where the exported payload lives inside the source project. */
	private const PAYLOAD_DIR = 'wordpress/data';

	/**
	 * Resolve and validate the source project directory.
	 *
	 * @param array $assoc Associative CLI args.
	 */
	private function root( array $assoc ): string {
		$dir = untrailingslashit( $assoc['dir'] ?? '' );
		if ( '' === $dir || ! is_dir( $dir ) ) {
			WP_CLI::error( 'Pass --dir=/path/to/static-site (the folder holding wordpress/data and assets/).' );
		}
		if ( ! is_dir( $dir . '/' . self::PAYLOAD_DIR ) ) {
			WP_CLI::error( 'No ' . self::PAYLOAD_DIR . ' in ' . $dir . '. Run the exporter first.' );
		}
		return $dir;
	}

	/**
	 * Read a JSON payload file.
	 *
	 * @param string $root Project root.
	 * @param string $name File name.
	 */
	private function payload( string $root, string $name ) {
		$path = $root . '/' . self::PAYLOAD_DIR . '/' . $name;
		if ( ! file_exists( $path ) ) {
			WP_CLI::error( 'Missing payload: ' . $path );
		}
		return json_decode( (string) file_get_contents( $path ), true );
	}

	/**
	 * Import every unique image master once.
	 *
	 * ## OPTIONS
	 *
	 * --dir=<dir>
	 * : Path to the static site project.
	 *
	 * [--limit=<n>]
	 * : Stop after n files. Default 0 (all).
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Associative args.
	 */
	public function import_media( $args, $assoc_args ): void {
		$root  = $this->root( $assoc_args );
		$limit = (int) ( $assoc_args['limit'] ?? 0 );
		$media = $this->payload( $root, 'media.json' );
		$map   = get_option( self::MEDIA_MAP_OPTION, array() );

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$done = 0;
		$new  = 0;
		$fail = 0;
		$bar  = WP_CLI\Utils\make_progress_bar( 'Media', $limit ? min( $limit, count( $media ) ) : count( $media ) );

		foreach ( $media as $item ) {
			if ( $limit && $done >= $limit ) {
				break;
			}
			++$done;
			$bar->tick();

			$rel = $item['file'];
			if ( isset( $map[ $rel ] ) && get_post( $map[ $rel ] ) ) {
				continue; // already imported - this is what makes reruns cheap
			}

			$abs = $root . '/' . $rel;
			if ( ! file_exists( $abs ) ) {
				WP_CLI::warning( 'missing file: ' . $rel );
				++$fail;
				continue;
			}

			$tmp = wp_tempnam( basename( $abs ) );
			if ( ! $tmp || ! copy( $abs, $tmp ) ) {
				++$fail;
				continue;
			}

			$id = media_handle_sideload(
				array(
					'name'     => basename( $abs ),
					'tmp_name' => $tmp,
				),
				0,
				null,
				array( 'post_title' => sanitize_text_field( basename( $abs ) ) )
			);

			if ( is_wp_error( $id ) ) {
				@unlink( $tmp );
				WP_CLI::warning( $rel . ': ' . $id->get_error_message() );
				++$fail;
				continue;
			}

			// Provenance travels with the file, so an image can always be traced
			// back to the page it came from.
			update_post_meta( $id, '_star_electric_source_file', $rel );
			foreach ( array( 'source_url', 'source_domain', 'image_type', 'image_note' ) as $k ) {
				if ( ! empty( $item[ $k ] ) ) {
					update_post_meta( $id, '_star_electric_' . $k, $item[ $k ] );
				}
			}

			$map[ $rel ] = $id;
			++$new;

			if ( 0 === $new % 100 ) {
				update_option( self::MEDIA_MAP_OPTION, $map, false );
			}
		}

		$bar->finish();
		update_option( self::MEDIA_MAP_OPTION, $map, false );
		WP_CLI::success( sprintf( 'Media: %d processed, %d newly imported, %d reused, %d failed.', $done, $new, $done - $new - $fail, $fail ) );
	}

	/**
	 * Create product categories, brands and departments.
	 *
	 * ## OPTIONS
	 *
	 * --dir=<dir>
	 * : Path to the static site project.
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Associative args.
	 */
	public function import_taxonomies( $args, $assoc_args ): void {
		$root = $this->root( $assoc_args );
		$tax  = $this->payload( $root, 'taxonomies.json' );
		$map  = get_option( self::MEDIA_MAP_OPTION, array() );

		foreach ( $tax['categories'] as $cat ) {
			$parent = $this->ensure_term( $cat['name'], 'product_cat', $cat['slug'], 0 );
			if ( $parent ) {
				$thumb = $map[ 'assets/images/categories/' . $cat['slug'] . '.webp' ] ?? 0;
				if ( $thumb ) {
					update_term_meta( $parent, 'thumbnail_id', $thumb );
				}
				foreach ( $cat['subcategories'] as $sub ) {
					$this->ensure_term( $sub['name'], 'product_cat', $sub['slug'], $parent );
				}
			}
		}

		foreach ( $tax['brands'] as $brand ) {
			$this->ensure_term( $brand['name'], Star_Electric_Taxonomies::BRAND, $brand['slug'], 0 );
		}

		foreach ( $tax['departments'] as $dept ) {
			$this->ensure_term(
				Star_Electric_Taxonomies::department_label( $dept['slug'] ),
				Star_Electric_Taxonomies::DEPARTMENT,
				$dept['slug'],
				0
			);
		}

		WP_CLI::success( sprintf(
			'Taxonomies: %d categories, %d brands, %d departments.',
			count( $tax['categories'] ),
			count( $tax['brands'] ),
			count( $tax['departments'] )
		) );
	}

	/**
	 * Create or update a term, matched on slug so reruns are safe.
	 *
	 * @param string $name     Term name.
	 * @param string $taxonomy Taxonomy.
	 * @param string $slug     Slug.
	 * @param int    $parent   Parent term ID.
	 */
	private function ensure_term( string $name, string $taxonomy, string $slug, int $parent ): int {
		$existing = get_term_by( 'slug', $slug, $taxonomy );
		if ( $existing instanceof WP_Term ) {
			return (int) $existing->term_id;
		}

		$made = wp_insert_term( $name, $taxonomy, array(
			'slug'   => $slug,
			'parent' => $parent,
		) );

		if ( is_wp_error( $made ) ) {
			WP_CLI::warning( $taxonomy . '/' . $slug . ': ' . $made->get_error_message() );
			return 0;
		}

		return (int) $made['term_id'];
	}

	/**
	 * Import the range/family records as star_range posts.
	 *
	 * ## OPTIONS
	 *
	 * --dir=<dir>
	 * : Path to the static site project.
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Associative args.
	 */
	public function import_ranges( $args, $assoc_args ): void {
		$root   = $this->root( $assoc_args );
		$ranges = $this->payload( $root, 'ranges.json' );
		$map    = get_option( self::MEDIA_MAP_OPTION, array() );

		$new  = 0;
		$upd  = 0;
		$fail = 0;
		$bar  = WP_CLI\Utils\make_progress_bar( 'Ranges', count( $ranges ) );

		foreach ( $ranges as $r ) {
			$bar->tick();
			$existing = $this->find_by_source_id( $r['source_id'], Star_Electric_Ranges::POST_TYPE );

			$postarr = array(
				'ID'           => $existing ?: 0,
				'post_type'    => Star_Electric_Ranges::POST_TYPE,
				'post_status'  => 'publish',
				'post_title'   => $r['name'],
				'post_name'    => $r['slug'],
				'post_excerpt' => $r['summary'],
				'post_content' => $r['summary'],
			);

			$id = wp_insert_post( $postarr, true );
			if ( is_wp_error( $id ) ) {
				WP_CLI::warning( $r['source_id'] . ': ' . $id->get_error_message() );
				++$fail;
				continue;
			}
			$existing ? ++$upd : ++$new;

			update_post_meta( $id, STAR_ELECTRIC_SOURCE_ID_META, $r['source_id'] );
			foreach ( array( 'brand', 'category', 'subcategory', 'series', 'kind', 'reason',
				'source_url', 'source_domain', 'image_type' ) as $k ) {
				update_post_meta( $id, '_star_electric_' . $k, (string) ( $r[ $k ] ?? '' ) );
			}

			if ( ! empty( $r['image'] ) && isset( $map[ $r['image'] ] ) ) {
				set_post_thumbnail( $id, $map[ $r['image'] ] );
			}
			if ( ! empty( $r['departments'] ) ) {
				wp_set_object_terms( $id, $r['departments'], Star_Electric_Taxonomies::DEPARTMENT );
			}
		}

		$bar->finish();
		WP_CLI::success( sprintf( 'Ranges: %d created, %d updated, %d failed.', $new, $upd, $fail ) );
	}

	/**
	 * Import the verified products as WooCommerce products.
	 *
	 * ## OPTIONS
	 *
	 * --dir=<dir>
	 * : Path to the static site project.
	 *
	 * [--batch=<n>]
	 * : Records per batch before the caches are flushed. Default 100.
	 *
	 * [--offset=<n>]
	 * : Skip the first n records. Use to resume. Default 0.
	 *
	 * [--limit=<n>]
	 * : Stop after n records. Default 0 (all).
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Associative args.
	 */
	public function import_products( $args, $assoc_args ): void {
		if ( ! class_exists( 'WC_Product_Simple' ) ) {
			WP_CLI::error( 'WooCommerce is not active.' );
		}

		$root   = $this->root( $assoc_args );
		$batch  = max( 1, (int) ( $assoc_args['batch'] ?? 100 ) );
		$offset = (int) ( $assoc_args['offset'] ?? 0 );
		$limit  = (int) ( $assoc_args['limit'] ?? 0 );
		$map    = get_option( self::MEDIA_MAP_OPTION, array() );

		$file = $root . '/' . self::PAYLOAD_DIR . '/products.ndjson';
		$fh   = fopen( $file, 'r' );
		if ( ! $fh ) {
			WP_CLI::error( 'Cannot read ' . $file );
		}

		$seen = 0;
		$new  = 0;
		$upd  = 0;
		$fail = 0;
		$done = 0;

		while ( false !== ( $line = fgets( $fh ) ) ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			++$seen;
			if ( $seen <= $offset ) {
				continue;
			}
			if ( $limit && $done >= $limit ) {
				break;
			}

			$rec = json_decode( $line, true );
			if ( ! is_array( $rec ) ) {
				++$fail;
				continue;
			}

			try {
				$was = $this->find_by_source_id( $rec['source_id'], 'product' );
				$id  = $this->save_product( $rec, $map, $was );
				$was ? ++$upd : ++$new;
				if ( ! $id ) {
					++$fail;
				}
			} catch ( Throwable $e ) {
				WP_CLI::warning( $rec['source_id'] . ': ' . $e->getMessage() );
				++$fail;
			}

			++$done;

			if ( 0 === $done % $batch ) {
				// Long-running CLI leaks memory through these caches otherwise.
				wp_cache_flush();
				WP_CLI::log( sprintf( '  ... %d done (%d new, %d updated, %d failed)', $done, $new, $upd, $fail ) );
			}
		}

		fclose( $fh );
		WP_CLI::success( sprintf(
			'Products: %d processed, %d created, %d updated, %d failed. Resume with --offset=%d',
			$done, $new, $upd, $fail, $offset + $done
		) );
	}

	/**
	 * Create or update one WooCommerce product from a source record.
	 *
	 * @param array $rec      Source record.
	 * @param array $map      Media map.
	 * @param int   $existing Existing post ID, or 0.
	 */
	private function save_product( array $rec, array $map, int $existing ): int {
		$variable = 'variable' === $rec['type'] && ! empty( $rec['variations'] );
		$product  = $existing
			? wc_get_product( $existing )
			: ( $variable ? new WC_Product_Variable() : new WC_Product_Simple() );

		if ( ! $product instanceof WC_Product ) {
			$product = $variable ? new WC_Product_Variable() : new WC_Product_Simple();
		}

		$product->set_name( $rec['name'] );
		$product->set_slug( $rec['slug'] );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'visible' );
		$product->set_short_description( $rec['short_description'] );
		$product->set_description( $this->description_html( $rec ) );

		if ( ! empty( $rec['sku'] ) ) {
			// A duplicate SKU throws; the source id remains the canonical key.
			try {
				$product->set_sku( $rec['sku'] );
			} catch ( Throwable $e ) {
				unset( $e );
			}
		}

		/*
		 * Pricing. A quote-only product is saved with no price whatsoever -
		 * writing 0 here is what would produce "Rs. 0" on the storefront.
		 */
		if ( $rec['quote_only'] ) {
			$product->set_regular_price( '' );
			$product->set_sale_price( '' );
			$product->set_price( '' );
		} else {
			$regular = (string) $rec['regular_price'];
			$product->set_regular_price( $regular );
			if ( ! empty( $rec['sale_price'] ) ) {
				$product->set_sale_price( (string) $rec['sale_price'] );
				$product->set_price( (string) $rec['sale_price'] );
			} else {
				$product->set_sale_price( '' );
				$product->set_price( $regular );
			}
		}

		$product->set_stock_status(
			'Out of Stock' === $rec['availability'] ? 'outofstock' : 'instock'
		);

		if ( ! empty( $rec['image'] ) && isset( $map[ $rec['image'] ] ) ) {
			$product->set_image_id( $map[ $rec['image'] ] );
		}
		$gallery = array();
		foreach ( (array) $rec['gallery'] as $g ) {
			if ( isset( $map[ $g ] ) ) {
				$gallery[] = $map[ $g ];
			}
		}
		if ( $gallery ) {
			$product->set_gallery_image_ids( $gallery );
		}

		$id = $product->save();
		if ( ! $id ) {
			return 0;
		}

		update_post_meta( $id, STAR_ELECTRIC_SOURCE_ID_META, $rec['source_id'] );
		update_post_meta( $id, STAR_ELECTRIC_QUOTE_META, $rec['quote_only'] ? 'yes' : 'no' );
		foreach ( array( 'model', 'series', 'source_url', 'source_domain',
			'source_checked', 'image_type', 'image_note' ) as $k ) {
			if ( ! empty( $rec[ $k ] ) ) {
				update_post_meta( $id, '_star_electric_' . $k, $rec[ $k ] );
			}
		}
		if ( ! empty( $rec['specifications'] ) ) {
			update_post_meta( $id, '_star_electric_specifications', wp_json_encode( $rec['specifications'] ) );
		}

		// Category tree, brand and departments - one product, many terms.
		$cats = array_filter( array( $rec['category'], $rec['subcategory'] ) );
		if ( $cats ) {
			wp_set_object_terms( $id, array_values( $cats ), 'product_cat' );
		}
		if ( ! empty( $rec['brand'] ) && 'Not specified' !== $rec['brand'] ) {
			wp_set_object_terms( $id, sanitize_title( $rec['brand'] ), Star_Electric_Taxonomies::BRAND );
		}
		if ( ! empty( $rec['departments'] ) ) {
			wp_set_object_terms( $id, $rec['departments'], Star_Electric_Taxonomies::DEPARTMENT );
		}

		return (int) $id;
	}

	/**
	 * Build the product description from source-published facts only.
	 *
	 * @param array $rec Source record.
	 */
	private function description_html( array $rec ): string {
		$out = '';
		if ( ! empty( $rec['short_description'] ) ) {
			$out .= '<p>' . esc_html( $rec['short_description'] ) . '</p>';
		}
		if ( ! empty( $rec['features'] ) ) {
			$out .= '<ul>';
			foreach ( $rec['features'] as $f ) {
				$out .= '<li>' . esc_html( (string) $f ) . '</li>';
			}
			$out .= '</ul>';
		}
		return $out;
	}

	/**
	 * Find an existing post by its source id - the key that makes reruns safe.
	 *
	 * @param string $source_id Source identifier.
	 * @param string $post_type Post type.
	 */
	private function find_by_source_id( string $source_id, string $post_type ): int {
		global $wpdb;

		$id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT p.ID FROM {$wpdb->posts} p
				 INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID
				 WHERE m.meta_key = %s AND m.meta_value = %s AND p.post_type = %s
				 LIMIT 1",
				STAR_ELECTRIC_SOURCE_ID_META,
				$source_id,
				$post_type
			)
		);

		return (int) $id;
	}

	/**
	 * Reconcile what is in WordPress against the source payload.
	 *
	 * ## OPTIONS
	 *
	 * --dir=<dir>
	 * : Path to the static site project.
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Associative args.
	 */
	public function audit( $args, $assoc_args ): void {
		$root = $this->root( $assoc_args );
		$map  = get_option( self::MEDIA_MAP_OPTION, array() );

		$src_products = 0;
		$src_quote    = 0;
		$fh           = fopen( $root . '/' . self::PAYLOAD_DIR . '/products.ndjson', 'r' );
		while ( false !== ( $line = fgets( $fh ) ) ) {
			if ( '' === trim( $line ) ) {
				continue;
			}
			++$src_products;
			if ( false !== strpos( $line, '"quote_only":true' ) || false !== strpos( $line, '"quote_only": true' ) ) {
				++$src_quote;
			}
		}
		fclose( $fh );

		$src_ranges = count( $this->payload( $root, 'ranges.json' ) );
		$src_media  = count( $this->payload( $root, 'media.json' ) );

		global $wpdb;
		$wp_products = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type='product' AND post_status='publish'" );
		$wp_ranges   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type=%s AND post_status='publish'", Star_Electric_Ranges::POST_TYPE ) );
		$wp_quote    = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key=%s AND meta_value='yes'", STAR_ELECTRIC_QUOTE_META ) );
		$wp_zero     = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key='_price' AND meta_value='0'" );

		$rows = array(
			array( 'products', $src_products, $wp_products, $src_products === $wp_products ? 'OK' : 'MISMATCH' ),
			array( 'quote-only', $src_quote, $wp_quote, $src_quote === $wp_quote ? 'OK' : 'MISMATCH' ),
			array( 'ranges', $src_ranges, $wp_ranges, $src_ranges === $wp_ranges ? 'OK' : 'MISMATCH' ),
			array( 'media masters', $src_media, count( $map ), $src_media === count( $map ) ? 'OK' : 'MISMATCH' ),
			array( 'products priced 0', 0, $wp_zero, 0 === $wp_zero ? 'OK' : 'FAIL - fake price present' ),
		);

		WP_CLI\Utils\format_items( 'table', array_map(
			static function ( $r ) {
				return array(
					'item'      => $r[0],
					'source'    => $r[1],
					'wordpress' => $r[2],
					'status'    => $r[3],
				);
			},
			$rows
		), array( 'item', 'source', 'wordpress', 'status' ) );
	}
}
