<?php
/**
 * Catalogue import engine.
 *
 * 4,348 products, 181 ranges and 2,054 unique images cannot be entered by hand,
 * and a one-shot script that dies at product 3,000 is worse than useless. The
 * engine is therefore a series of resumable steps, each of which processes a
 * slice and reports exactly where the next slice starts:
 *
 *   idempotent  every record carries its source id in meta and is matched on it,
 *               so a second run updates rather than duplicates
 *   resumable   each step takes an offset and returns the next one; progress is
 *               persisted, so a closed browser tab loses nothing
 *   batched     slices are small enough to finish inside one PHP request
 *   deduped     each image is fetched once; later products reuse the attachment
 *               id, because 4,348 products share 2,054 masters
 *   honest      failures are counted and named, never swallowed
 *
 * This class holds no UI. Both the WP-CLI command and the admin AJAX runner are
 * thin wrappers over it, so the two routes cannot drift apart.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The importer.
 */
class Star_Electric_Importer {

	/** Maps a source image path to its attachment ID, so nothing imports twice. */
	public const MEDIA_MAP_OPTION = 'star_electric_media_map';

	/** Accumulated counters and resume points. */
	public const PROGRESS_OPTION = 'star_electric_import_progress';

	/** Where images are fetched from when they are not bundled with the plugin. */
	public const SOURCE_OPTION = 'star_electric_source_base';

	/** Cached line count of the product payload. */
	public const TOTAL_OPTION = 'star_electric_product_total';

	/** The approved storefront, which serves every master over HTTPS. */
	public const DEFAULT_SOURCE = 'https://aliza-younas.github.io/Star-Electric-Enterprises';

	/**
	 * Directory holding the exported payload, shipped inside the plugin.
	 */
	public static function data_dir(): string {
		return STAR_ELECTRIC_PATH . 'data/';
	}

	/**
	 * Base URL images are fetched from.
	 */
	public static function source_base(): string {
		$base = (string) get_option( self::SOURCE_OPTION, '' );
		if ( '' === $base ) {
			$base = self::DEFAULT_SOURCE;
		}
		return untrailingslashit( $base );
	}

	/**
	 * Describe the payload so the admin screen can refuse to start without it.
	 */
	public static function payload_status(): array {
		$out = array();
		foreach ( array( 'taxonomies.json', 'media.json', 'ranges.json', 'products.ndjson' ) as $name ) {
			$path         = self::data_dir() . $name;
			$out[ $name ] = array(
				'exists' => file_exists( $path ),
				'bytes'  => file_exists( $path ) ? (int) filesize( $path ) : 0,
			);
		}
		return $out;
	}

	/**
	 * Read one JSON payload file.
	 *
	 * @param string $name File name.
	 * @return array|null
	 */
	private static function read_json( string $name ) {
		$path = self::data_dir() . $name;
		if ( ! file_exists( $path ) ) {
			return null;
		}
		$data = json_decode( (string) file_get_contents( $path ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		return is_array( $data ) ? $data : null;
	}

	/**
	 * The persisted source-path to attachment-id map.
	 */
	public static function media_map(): array {
		$map = get_option( self::MEDIA_MAP_OPTION, array() );
		return is_array( $map ) ? $map : array();
	}

	/**
	 * Read the whole progress record.
	 */
	public static function progress(): array {
		$p = get_option( self::PROGRESS_OPTION, array() );
		return is_array( $p ) ? $p : array();
	}

	/**
	 * Merge counters for one step into the persisted progress record.
	 *
	 * @param string $step   Step key.
	 * @param array  $result Step result.
	 */
	private static function record( string $step, array $result ): void {
		$all  = self::progress();
		$prev = isset( $all[ $step ] ) && is_array( $all[ $step ] ) ? $all[ $step ] : array();

		foreach ( array( 'created', 'updated', 'reused', 'failed' ) as $k ) {
			$all[ $step ][ $k ] = (int) ( $prev[ $k ] ?? 0 ) + (int) ( $result[ $k ] ?? 0 );
		}
		$all[ $step ]['offset']     = (int) $result['offset'];
		$all[ $step ]['total']      = (int) $result['total'];
		$all[ $step ]['complete']   = (bool) $result['complete'];
		$all[ $step ]['updated_at'] = time();

		// Only the most recent errors are kept; the count is what matters.
		$errors                 = array_merge( (array) ( $prev['errors'] ?? array() ), (array) ( $result['errors'] ?? array() ) );
		$all[ $step ]['errors'] = array_slice( $errors, -50 );

		update_option( self::PROGRESS_OPTION, $all, false );
	}

	/**
	 * Forget the counters for a step so it can be re-run from the beginning.
	 *
	 * The media map is deliberately NOT cleared: re-running an import should
	 * reuse the images already uploaded, never duplicate 2,054 files.
	 *
	 * @param string $step Step key, or 'all'.
	 */
	public static function reset( string $step ): void {
		$all = self::progress();
		if ( 'all' === $step ) {
			$all = array();
		} else {
			unset( $all[ $step ] );
		}
		update_option( self::PROGRESS_OPTION, $all, false );
	}

	/**
	 * Shape a step result.
	 *
	 * @param int   $offset Next offset.
	 * @param int   $total  Total records.
	 * @param array $counts Counters.
	 * @param array $errors Error strings.
	 */
	private static function result( int $offset, int $total, array $counts, array $errors ): array {
		return array_merge(
			array(
				'created' => 0,
				'updated' => 0,
				'reused'  => 0,
				'failed'  => 0,
			),
			$counts,
			array(
				'offset'   => $offset,
				'total'    => $total,
				'complete' => $offset >= $total,
				'errors'   => $errors,
			)
		);
	}

	/**
	 * Give the request as much room as the host allows.
	 *
	 * Shared hosting frequently forbids this outright, which is fine - it is why
	 * the batch sizes are small. Suppressed rather than fatal.
	 */
	private static function relax_limits(): void {
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 0 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		}
		@ini_set( 'memory_limit', '512M' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors,WordPress.PHP.IniSet
		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );
	}

	/**
	 * Flush the counting that relax_limits() deferred.
	 *
	 * Deferring term counting keeps a batch fast, but the deferred list lives
	 * only for the request - so it has to be flushed before the request ends.
	 * Without this every term count stays at zero, and anything asking for
	 * non-empty terms (the shop's category and brand filters, for one) sees an
	 * empty catalogue.
	 */
	private static function settle(): void {
		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );
	}

	/**
	 * Recount every catalogue taxonomy from scratch.
	 *
	 * A repair for counts left stale by an interrupted run, and cheap enough to
	 * run whenever the numbers look wrong.
	 */
	public static function recount(): array {
		self::relax_limits();

		$taxonomies = array( 'product_cat', 'product_tag', 'product_type' );
		if ( class_exists( 'Star_Electric_Taxonomies' ) ) {
			$taxonomies[] = Star_Electric_Taxonomies::BRAND;
			$taxonomies[] = Star_Electric_Taxonomies::DEPARTMENT;
		}

		$total = 0;
		foreach ( $taxonomies as $taxonomy ) {
			if ( ! taxonomy_exists( $taxonomy ) ) {
				continue;
			}
			$ids = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
					'fields'     => 'ids',
				)
			);
			if ( is_wp_error( $ids ) || ! $ids ) {
				continue;
			}
			wp_update_term_count_now( $ids, $taxonomy );
			$total += count( $ids );
		}

		self::settle();
		delete_transient( 'star_electric_filter_counts' );

		return array( 'terms' => $total );
	}

	/* --------------------------------------------------------------------- *
	 * Step 1 - media
	 * --------------------------------------------------------------------- */

	/**
	 * Import a slice of the unique image masters.
	 *
	 * Files bundled with the plugin are used when present; otherwise each master
	 * is fetched over HTTPS from the approved storefront. Either way the image
	 * ends up in this site's own media library - nothing is ever hotlinked.
	 *
	 * @param int $offset Records already processed.
	 * @param int $size   Records to process now.
	 */
	public static function step_media( int $offset, int $size ): array {
		$media = self::read_json( 'media.json' );
		if ( null === $media ) {
			return self::result( 0, 0, array( 'failed' => 1 ), array( 'media.json is missing from the plugin.' ) );
		}

		self::relax_limits();
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$total  = count( $media );
		$map    = self::media_map();
		$errors = array();
		$new    = 0;
		$reused = 0;
		$failed = 0;
		$i      = $offset;
		$end    = min( $total, $offset + $size );

		for ( ; $i < $end; $i++ ) {
			$item = $media[ $i ];
			$rel  = (string) $item['file'];

			if ( isset( $map[ $rel ] ) && get_post( (int) $map[ $rel ] ) ) {
				++$reused; // this is what makes a re-run cheap
				continue;
			}

			$id = self::sideload( $rel );
			if ( is_wp_error( $id ) ) {
				$errors[] = $rel . ': ' . $id->get_error_message();
				++$failed;
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
		}

		update_option( self::MEDIA_MAP_OPTION, $map, false );

		$result = self::result(
			$i,
			$total,
			array(
				'created' => $new,
				'reused'  => $reused,
				'failed'  => $failed,
			),
			$errors
		);
		self::settle();
		self::record( 'media', $result );
		return $result;
	}

	/**
	 * Put one image into the media library.
	 *
	 * @param string $rel Source-relative path.
	 * @return int|WP_Error Attachment ID.
	 */
	private static function sideload( string $rel ) {
		$name  = basename( $rel );
		$local = self::data_dir() . 'media/' . $rel;

		if ( file_exists( $local ) ) {
			$tmp = wp_tempnam( $name );
			if ( ! $tmp || ! copy( $local, $tmp ) ) {
				if ( $tmp ) {
					@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
				}
				return new WP_Error( 'star_electric_copy', 'could not stage the bundled file' );
			}
		} else {
			$url = self::source_base() . '/' . implode( '/', array_map( 'rawurlencode', explode( '/', $rel ) ) );
			$tmp = download_url( $url, 30 );
			if ( is_wp_error( $tmp ) ) {
				return $tmp;
			}
		}

		$id = media_handle_sideload(
			array(
				'name'     => $name,
				'tmp_name' => $tmp,
			),
			0,
			null,
			array( 'post_title' => sanitize_text_field( $name ) )
		);

		if ( is_wp_error( $id ) && file_exists( $tmp ) ) {
			@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
			return $id;
		}

		return (int) $id;
	}

	/* --------------------------------------------------------------------- *
	 * Step 2 - taxonomies
	 * --------------------------------------------------------------------- */

	/**
	 * Create product categories, brands and departments.
	 *
	 * Small enough to run in a single request.
	 */
	public static function step_taxonomies(): array {
		$tax = self::read_json( 'taxonomies.json' );
		if ( null === $tax ) {
			return self::result( 0, 0, array( 'failed' => 1 ), array( 'taxonomies.json is missing from the plugin.' ) );
		}

		self::relax_limits();

		$map    = self::media_map();
		$errors = array();
		$made   = 0;
		$kept   = 0;

		foreach ( $tax['categories'] as $cat_index => $cat ) {
			$parent = self::ensure_term( $cat['name'], 'product_cat', $cat['slug'], 0, $made, $kept, $errors );
			if ( ! $parent ) {
				continue;
			}

			/*
			 * A term has no order of its own, and the approved site lists both
			 * departments and their subcategories in the catalogue's order, not
			 * alphabetically and not by count. The payload's order is stored so
			 * the filter panel, the subcategory strip and the department index
			 * all read the same way round.
			 */
			update_term_meta( $parent, '_star_electric_order', (int) $cat_index );

			/*
			 * The approved category page prints this under the department
			 * title. Stored as the term description, which is where WordPress
			 * expects a category's own words to live.
			 */
			if ( ! empty( $cat['blurb'] ) ) {
				wp_update_term( $parent, 'product_cat', array( 'description' => (string) $cat['blurb'] ) );
			}
			$thumb = $map[ 'assets/images/categories/' . $cat['slug'] . '.webp' ] ?? 0;
			if ( $thumb ) {
				update_term_meta( $parent, 'thumbnail_id', $thumb );
			}
			foreach ( (array) ( $cat['subcategories'] ?? array() ) as $sub_index => $sub ) {
				$child = self::ensure_term( $sub['name'], 'product_cat', $sub['slug'], $parent, $made, $kept, $errors );
				if ( $child ) {
					update_term_meta( $child, '_star_electric_order', (int) $sub_index );
				}
			}
		}

		foreach ( $tax['brands'] as $brand_index => $brand ) {
			$term_id = self::ensure_term( $brand['name'], Star_Electric_Taxonomies::BRAND, $brand['slug'], 0, $made, $kept, $errors );
			if ( ! $term_id ) {
				continue;
			}

			/*
			 * The approved brand directory lists brands in the catalogue's own
			 * order, largest first, not alphabetically. A term has no order of
			 * its own, so the payload's is carried across rather than guessed
			 * at from the product count - which happens to agree today and
			 * would not have to tomorrow.
			 */
			update_term_meta( $term_id, '_star_electric_order', (int) $brand_index );

			/*
			 * The monogram is the brand's mark on the approved storefront.
			 * Real manufacturer logos are deliberately not used: showing one
			 * would imply a dealership or distribution relationship that is not
			 * on record.
			 */
			update_term_meta( $term_id, '_star_electric_mark', (string) ( $brand['mark'] ?? '' ) );
			update_term_meta( $term_id, '_star_electric_note', (string) ( $brand['note'] ?? '' ) );
			update_term_meta( $term_id, '_star_electric_family_count', (int) ( $brand['familyCount'] ?? 0 ) );

			// A brand whose source publishes ranges but no individual products
			// is navigable but not a catalogue listing.
			update_term_meta(
				$term_id,
				'_star_electric_catalogue_exposed',
				false === ( $brand['catalogueExposed'] ?? true ) ? 'no' : 'yes'
			);
		}

		foreach ( $tax['departments'] as $dept ) {
			self::ensure_term(
				Star_Electric_Taxonomies::department_label( $dept['slug'] ),
				Star_Electric_Taxonomies::DEPARTMENT,
				$dept['slug'],
				0,
				$made,
				$kept,
				$errors
			);
		}

		$total  = $made + $kept;
		$result = self::result(
			$total,
			$total,
			array(
				'created' => $made,
				'updated' => $kept,
				'failed'  => count( $errors ),
			),
			$errors
		);
		self::settle();
		self::record( 'taxonomies', $result );
		return $result;
	}

	/**
	 * Create a term if it is missing, matched on slug so re-runs are safe.
	 *
	 * @param string $name     Term name.
	 * @param string $taxonomy Taxonomy.
	 * @param string $slug     Slug.
	 * @param int    $parent   Parent term ID.
	 * @param int    $made     Created counter, by reference.
	 * @param int    $kept     Existing counter, by reference.
	 * @param array  $errors   Errors, by reference.
	 */
	private static function ensure_term( string $name, string $taxonomy, string $slug, int $parent, int &$made, int &$kept, array &$errors ): int {
		$existing = get_term_by( 'slug', $slug, $taxonomy );
		if ( $existing instanceof WP_Term ) {
			++$kept;
			return (int) $existing->term_id;
		}

		$term = wp_insert_term(
			$name,
			$taxonomy,
			array(
				'slug'   => $slug,
				'parent' => $parent,
			)
		);

		if ( is_wp_error( $term ) ) {
			$errors[] = $taxonomy . '/' . $slug . ': ' . $term->get_error_message();
			return 0;
		}

		++$made;
		return (int) $term['term_id'];
	}

	/* --------------------------------------------------------------------- *
	 * Step 3 - ranges
	 * --------------------------------------------------------------------- */

	/**
	 * Import a slice of the range/family records as star_range posts.
	 *
	 * @param int $offset Records already processed.
	 * @param int $size   Records to process now.
	 */
	public static function step_ranges( int $offset, int $size ): array {
		$ranges = self::read_json( 'ranges.json' );
		if ( null === $ranges ) {
			return self::result( 0, 0, array( 'failed' => 1 ), array( 'ranges.json is missing from the plugin.' ) );
		}

		self::relax_limits();

		$total  = count( $ranges );
		$map    = self::media_map();
		$errors = array();
		$new    = 0;
		$upd    = 0;
		$failed = 0;
		$i      = $offset;
		$end    = min( $total, $offset + $size );

		for ( ; $i < $end; $i++ ) {
			$r        = $ranges[ $i ];
			$existing = self::find_by_source_id( (string) $r['source_id'], Star_Electric_Ranges::POST_TYPE );

			$id = wp_insert_post(
				array(
					'ID'           => $existing ? $existing : 0,
					'post_type'    => Star_Electric_Ranges::POST_TYPE,
					'post_status'  => 'publish',
					'post_title'   => $r['name'],
					'post_name'    => $r['slug'],
					'post_excerpt' => $r['summary'],
					'post_content' => $r['summary'],
				),
				true
			);

			if ( is_wp_error( $id ) ) {
				$errors[] = $r['source_id'] . ': ' . $id->get_error_message();
				++$failed;
				continue;
			}
			$existing ? ++$upd : ++$new;

			update_post_meta( $id, STAR_ELECTRIC_SOURCE_ID_META, $r['source_id'] );
			foreach ( array( 'brand', 'category', 'subcategory', 'series', 'kind', 'reason', 'source_url', 'source_domain', 'image_type' ) as $k ) {
				update_post_meta( $id, '_star_electric_' . $k, (string) ( $r[ $k ] ?? '' ) );
			}

			if ( ! empty( $r['image'] ) && isset( $map[ $r['image'] ] ) ) {
				set_post_thumbnail( $id, (int) $map[ $r['image'] ] );
			}
			$r_depts = self::known_departments( (array) ( $r['departments'] ?? array() ) );
			wp_set_object_terms( $id, $r_depts, Star_Electric_Taxonomies::DEPARTMENT );
		}

		$result = self::result(
			$i,
			$total,
			array(
				'created' => $new,
				'updated' => $upd,
				'failed'  => $failed,
			),
			$errors
		);
		self::settle();
		self::record( 'ranges', $result );
		return $result;
	}

	/* --------------------------------------------------------------------- *
	 * Step 4 - products
	 * --------------------------------------------------------------------- */

	/**
	 * Count the products in the payload without holding them all in memory.
	 */
	public static function product_total(): int {
		$path = self::data_dir() . 'products.ndjson';
		if ( ! file_exists( $path ) ) {
			return 0;
		}

		$cached = (int) get_option( self::TOTAL_OPTION, 0 );
		if ( $cached > 0 ) {
			return $cached;
		}

		$n  = 0;
		$fh = fopen( $path, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		if ( ! $fh ) {
			return 0;
		}
		while ( false !== ( $line = fgets( $fh ) ) ) {
			if ( '' !== trim( $line ) ) {
				++$n;
			}
		}
		fclose( $fh ); // phpcs:ignore WordPress.WP.AlternativeFunctions

		update_option( self::TOTAL_OPTION, $n, false );
		return $n;
	}

	/**
	 * Import a slice of the products.
	 *
	 * The payload is newline-delimited JSON, so a slice can be read without
	 * parsing the other 4,300 records. A byte offset is remembered alongside the
	 * record offset, so resuming does not mean re-reading the whole file.
	 *
	 * @param int $offset Records already processed.
	 * @param int $size   Records to process now.
	 */
	public static function step_products( int $offset, int $size ): array {
		if ( ! class_exists( 'WC_Product_Simple' ) ) {
			return self::result( 0, 0, array( 'failed' => 1 ), array( 'WooCommerce is not active.' ) );
		}

		$path = self::data_dir() . 'products.ndjson';
		if ( ! file_exists( $path ) ) {
			return self::result( 0, 0, array( 'failed' => 1 ), array( 'products.ndjson is missing from the plugin.' ) );
		}

		self::relax_limits();

		$total = self::product_total();
		$fh    = fopen( $path, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		if ( ! $fh ) {
			return self::result( $offset, $total, array( 'failed' => 1 ), array( 'Cannot read products.ndjson.' ) );
		}

		/*
		 * Resume from the remembered byte position when it matches this offset,
		 * otherwise wind forward from the top. Winding forward is slower but
		 * always correct, which is the behaviour a resume needs.
		 */
		$progress = self::progress();
		$seek     = $progress['products']['byte'] ?? null;
		$at       = 0;

		if ( $offset > 0 ) {
			if ( null !== $seek && (int) ( $progress['products']['offset'] ?? -1 ) === $offset ) {
				fseek( $fh, (int) $seek );
				$at = $offset;
			} else {
				while ( $at < $offset && false !== ( $line = fgets( $fh ) ) ) {
					if ( '' !== trim( $line ) ) {
						++$at;
					}
				}
			}
		}

		$map    = self::media_map();
		$errors = array();
		$new    = 0;
		$upd    = 0;
		$failed = 0;
		$done   = 0;

		while ( $done < $size && false !== ( $line = fgets( $fh ) ) ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}

			$rec = json_decode( $line, true );
			if ( ! is_array( $rec ) ) {
				$errors[] = 'record ' . ( $at + 1 ) . ': malformed JSON';
				++$failed;
				++$at;
				++$done;
				continue;
			}

			try {
				$was = self::find_by_source_id( (string) $rec['source_id'], 'product' );
				$id  = self::save_product( $rec, $map, $was );
				if ( $id ) {
					$was ? ++$upd : ++$new;
				} else {
					$errors[] = $rec['source_id'] . ': save returned no id';
					++$failed;
				}
			} catch ( Throwable $e ) {
				$errors[] = $rec['source_id'] . ': ' . $e->getMessage();
				++$failed;
			}

			++$at;
			++$done;
		}

		$byte = ftell( $fh );
		fclose( $fh ); // phpcs:ignore WordPress.WP.AlternativeFunctions

		$result = self::result(
			$at,
			$total,
			array(
				'created' => $new,
				'updated' => $upd,
				'failed'  => $failed,
			),
			$errors
		);
		self::settle();
		self::record( 'products', $result );

		$all                     = self::progress();
		$all['products']['byte'] = (int) $byte;
		update_option( self::PROGRESS_OPTION, $all, false );

		return $result;
	}

	/**
	 * Create or update one WooCommerce product from a source record.
	 *
	 * @param array $rec      Source record.
	 * @param array $map      Media map.
	 * @param int   $existing Existing post ID, or 0.
	 */
	private static function save_product( array $rec, array $map, int $existing ): int {
		$variable = 'variable' === $rec['type'] && ! empty( $rec['variations'] );
		$product  = $existing ? wc_get_product( $existing ) : null;

		// A record whose type changed between runs must not keep the wrong class.
		if ( $product instanceof WC_Product && $product->is_type( 'variable' ) !== $variable ) {
			$product = null;
		}
		if ( ! $product instanceof WC_Product ) {
			$product = $variable ? new WC_Product_Variable() : new WC_Product_Simple();
			if ( $existing ) {
				$product->set_id( $existing );
			}
		}

		$product->set_name( $rec['name'] );
		$product->set_slug( $rec['slug'] );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'visible' );
		$product->set_short_description( (string) $rec['short_description'] );
		$product->set_description( self::description_html( $rec ) );

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
		 * writing 0 here is exactly what would produce "Rs. 0" on the storefront.
		 * A variable parent carries no price of its own either; WooCommerce
		 * derives its range from the children.
		 */
		if ( ! empty( $rec['quote_only'] ) ) {
			$product->set_regular_price( '' );
			$product->set_sale_price( '' );
			$product->set_price( '' );
		} elseif ( ! $variable ) {
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

		/*
		 * WooCommerce has two stock states; the source has three. 2,382 products
		 * publish no availability at all, and calling those "In Stock" would be
		 * inventing a claim about the shop's shelves. So the WooCommerce status
		 * only decides whether the product can be bought, while the availability
		 * actually shown to a shopper comes from the meta below - which says
		 * "not specified" when that is the truth.
		 */
		$product->set_stock_status( 'Out of Stock' === $rec['availability'] ? 'outofstock' : 'instock' );

		if ( ! empty( $rec['image'] ) && isset( $map[ $rec['image'] ] ) ) {
			$product->set_image_id( (int) $map[ $rec['image'] ] );
		}
		$gallery = array();
		foreach ( (array) ( $rec['gallery'] ?? array() ) as $g ) {
			if ( isset( $map[ $g ] ) ) {
				$gallery[] = (int) $map[ $g ];
			}
		}
		$product->set_gallery_image_ids( $gallery );

		if ( $variable ) {
			$product->set_attributes( self::build_attributes( $rec['variations'] ) );
		}

		$id = $product->save();
		if ( ! $id ) {
			return 0;
		}

		update_post_meta( $id, STAR_ELECTRIC_SOURCE_ID_META, $rec['source_id'] );
		update_post_meta( $id, STAR_ELECTRIC_QUOTE_META, ! empty( $rec['quote_only'] ) ? 'yes' : 'no' );
		update_post_meta( $id, '_star_electric_availability', (string) $rec['availability'] );
		foreach ( array( 'model', 'series', 'source_url', 'source_domain', 'source_checked', 'image_type', 'image_note' ) as $k ) {
			if ( ! empty( $rec[ $k ] ) ) {
				update_post_meta( $id, '_star_electric_' . $k, $rec[ $k ] );
			}
		}
		if ( ! empty( $rec['specifications'] ) ) {
			update_post_meta( $id, '_star_electric_specifications', wp_json_encode( $rec['specifications'] ) );
		}

		/*
		 * The approved product page lists these as "Key points" under the short
		 * description. Only four records carry any, but dropping them was still
		 * content the source publishes and the migration did not.
		 */
		if ( ! empty( $rec['features'] ) ) {
			update_post_meta( $id, '_star_electric_features', wp_json_encode( array_values( (array) $rec['features'] ) ) );
		} else {
			delete_post_meta( $id, '_star_electric_features' );
		}

		/*
		 * The catalogue's own notes about a record - most often that its source
		 * publishes no price. The approved product page shows these under
		 * Additional Information, and hides the tab when there are none, so
		 * they are part of the content rather than a debugging artefact.
		 */
		if ( ! empty( $rec['import_notes'] ) ) {
			update_post_meta( $id, '_star_electric_notes', wp_json_encode( (array) $rec['import_notes'] ) );
		} else {
			delete_post_meta( $id, '_star_electric_notes' );
		}

		/*
		 * The approved shop offers a "Biggest Discount" sort. Ordering by a
		 * discount computed at query time is not possible, so it is stored -
		 * and stored on every product, including 0, so the sort does not
		 * silently drop the products that are not discounted.
		 */
		$discount = 0;
		if ( empty( $rec['quote_only'] ) && ! empty( $rec['sale_price'] ) && ! empty( $rec['regular_price'] ) ) {
			$regular = (float) $rec['regular_price'];
			$sale    = (float) $rec['sale_price'];
			if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
				$discount = (int) round( ( 1 - ( $sale / $regular ) ) * 100 );
			}
		}
		update_post_meta( $id, '_star_electric_discount', $discount );

		// Category tree, brand and departments - one product, many terms.
		$cats = array_filter( array( $rec['category'], $rec['subcategory'] ) );
		if ( $cats ) {
			wp_set_object_terms( $id, array_values( $cats ), 'product_cat' );
		}
		if ( ! empty( $rec['brand'] ) && 'Not specified' !== $rec['brand'] ) {
			wp_set_object_terms( $id, sanitize_title( $rec['brand'] ), Star_Electric_Taxonomies::BRAND );
		}
		wp_set_object_terms(
			$id,
			self::known_departments( (array) ( $rec['departments'] ?? array() ) ),
			Star_Electric_Taxonomies::DEPARTMENT
		);

		if ( $variable ) {
			self::save_variations( (int) $id, $rec, $map );
		}

		return (int) $id;
	}

	/**
	 * Build custom product attributes from the variation options.
	 *
	 * These are local attributes rather than global taxonomies: option sets like
	 * "Sheath Colour" or "Face Decor" belong to individual ranges, so a global
	 * taxonomy would collect hundreds of unrelated terms for no benefit.
	 *
	 * @param array $variations Variation records.
	 */
	private static function build_attributes( array $variations ): array {
		$values = array();
		foreach ( $variations as $v ) {
			foreach ( (array) ( $v['options'] ?? array() ) as $name => $value ) {
				$name  = (string) $name;
				$value = (string) $value;
				if ( '' === $value ) {
					continue;
				}
				if ( ! isset( $values[ $name ] ) ) {
					$values[ $name ] = array();
				}
				if ( ! in_array( $value, $values[ $name ], true ) ) {
					$values[ $name ][] = $value;
				}
			}
		}

		$attributes = array();
		$position   = 0;
		foreach ( $values as $name => $options ) {
			$attribute = new WC_Product_Attribute();
			$attribute->set_id( 0 );
			$attribute->set_name( $name );
			$attribute->set_options( $options );
			$attribute->set_position( $position );
			$attribute->set_visible( true );
			$attribute->set_variation( true );
			$attributes[] = $attribute;
			++$position;
		}

		return $attributes;
	}

	/**
	 * Create, update and prune the variation children of a variable product.
	 *
	 * Matched on a source id derived from the parent, so a re-run updates the
	 * same child rather than adding another one. Children the source no longer
	 * lists are deleted, which is what keeps a re-import honest.
	 *
	 * @param int   $parent_id Parent product ID.
	 * @param array $rec       Source record.
	 * @param array $map       Media map.
	 */
	private static function save_variations( int $parent_id, array $rec, array $map ): void {
		$parent_quote = ! empty( $rec['quote_only'] );
		$keep         = array();

		foreach ( (array) $rec['variations'] as $index => $v ) {
			$source_id = $rec['source_id'] . '#' . $index;
			$existing  = self::find_by_source_id( $source_id, 'product_variation' );

			$variation = $existing ? wc_get_product( $existing ) : null;
			if ( ! $variation instanceof WC_Product_Variation ) {
				$variation = new WC_Product_Variation();
			}

			$variation->set_parent_id( $parent_id );
			$variation->set_name( (string) ( $v['name'] ?? '' ) );
			$variation->set_status( 'publish' );

			$attributes = array();
			foreach ( (array) ( $v['options'] ?? array() ) as $name => $value ) {
				$attributes[ sanitize_title( (string) $name ) ] = (string) $value;
			}
			$variation->set_attributes( $attributes );

			$price = $v['price'] ?? ( $v['regularPrice'] ?? null );
			if ( $parent_quote || null === $price || '' === $price ) {
				// Same rule as the parent: no price at all, never zero.
				$variation->set_regular_price( '' );
				$variation->set_sale_price( '' );
				$variation->set_price( '' );
			} else {
				$regular = (string) ( $v['regularPrice'] ?? $price );
				$variation->set_regular_price( $regular );
				if ( (string) $price !== $regular ) {
					$variation->set_sale_price( (string) $price );
					$variation->set_price( (string) $price );
				} else {
					$variation->set_sale_price( '' );
					$variation->set_price( $regular );
				}
			}

			if ( ! empty( $v['sku'] ) ) {
				try {
					$variation->set_sku( (string) $v['sku'] );
				} catch ( Throwable $e ) {
					unset( $e );
				}
			}

			$variation->set_stock_status(
				'Out of Stock' === (string) ( $v['availability'] ?? '' ) ? 'outofstock' : 'instock'
			);

			if ( ! empty( $v['image'] ) && isset( $map[ $v['image'] ] ) ) {
				$variation->set_image_id( (int) $map[ $v['image'] ] );
			}

			$vid = $variation->save();
			if ( $vid ) {
				update_post_meta( $vid, STAR_ELECTRIC_SOURCE_ID_META, $source_id );
				update_post_meta( $vid, STAR_ELECTRIC_QUOTE_META, $parent_quote ? 'yes' : 'no' );
				$keep[] = (int) $vid;
			}
		}

		/*
		 * Remove children the source no longer lists. Queried directly rather
		 * than through wc_get_products(), which does not reliably return
		 * variations across WooCommerce versions.
		 */
		$children = get_posts(
			array(
				'post_type'      => 'product_variation',
				'post_parent'    => $parent_id,
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);
		foreach ( (array) $children as $child ) {
			if ( ! in_array( (int) $child, $keep, true ) ) {
				wp_delete_post( (int) $child, true );
			}
		}

		if ( class_exists( 'WC_Product_Variable' ) ) {
			WC_Product_Variable::sync( $parent_id );
		}
	}

	/**
	 * Build the product description from source-published facts only.
	 *
	 * @param array $rec Source record.
	 */
	private static function description_html( array $rec ): string {
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
	 * Find an existing post by its source id - the key that makes re-runs safe.
	 *
	 * @param string $source_id Source identifier.
	 * @param string $post_type Post type.
	 */
	private static function find_by_source_id( string $source_id, string $post_type ): int {
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
	 * Keep only the departments the catalogue actually declares.
	 *
	 * wp_set_object_terms() creates a term it cannot find, and three earthing
	 * ranges carry their category slug in the departments field. Left alone
	 * that invented a fourth department called "earthing-material", with a
	 * public archive whose title was a slug. Departments are the three in
	 * taxonomies.json; anything else is data noise, not a department.
	 *
	 * @param array $slugs Department slugs from a record.
	 * @return string[] The ones that exist.
	 */
	private static function known_departments( array $slugs ): array {
		$out = array();
		foreach ( $slugs as $slug ) {
			$slug = sanitize_title( (string) $slug );
			if ( '' !== $slug && get_term_by( 'slug', $slug, Star_Electric_Taxonomies::DEPARTMENT ) ) {
				$out[] = $slug;
			}
		}
		return $out;
	}

	/* --------------------------------------------------------------------- *
	 * Audit
	 * --------------------------------------------------------------------- */

	/**
	 * Reconcile what is in WordPress against the source payload.
	 *
	 * Every row is counted on both sides from the same definition, so a
	 * mismatch names a real difference rather than two ways of counting. The
	 * three rows at the end are defects rather than quantities: a record that
	 * never arrived, a source id that arrived twice, and a product wearing a
	 * price of zero - which for this catalogue would mean a quote-only item had
	 * been given an invented price.
	 *
	 * @return array Rows of item / source / wordpress / status.
	 */
	public static function audit(): array {
		global $wpdb;

		/* ---------------------------------------------------- the source side */
		$src = array(
			'products'   => 0,
			'quote'      => 0,
			'priced'     => 0,
			'sale'       => 0,
			'variable'   => 0,
			'variations' => 0,
			'oos'        => 0,
		);
		$src_ids  = array();
		$path     = self::data_dir() . 'products.ndjson';

		if ( file_exists( $path ) ) {
			$fh = fopen( $path, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			while ( false !== ( $line = fgets( $fh ) ) ) {
				$line = trim( $line );
				if ( '' === $line ) {
					continue;
				}
				$rec = json_decode( $line, true );
				if ( ! is_array( $rec ) ) {
					continue;
				}
				++$src['products'];
				$src_ids[] = (string) ( $rec['source_id'] ?? '' );

				if ( ! empty( $rec['quote_only'] ) ) {
					++$src['quote'];
				} else {
					++$src['priced'];
					if ( ! empty( $rec['sale_price'] ) && ! empty( $rec['regular_price'] )
						&& (float) $rec['sale_price'] < (float) $rec['regular_price'] ) {
						++$src['sale'];
					}
				}
				if ( ! empty( $rec['variations'] ) ) {
					++$src['variable'];
					$src['variations'] += count( (array) $rec['variations'] );
				}
				if ( 'Out of Stock' === ( $rec['availability'] ?? '' ) ) {
					++$src['oos'];
				}
			}
			fclose( $fh ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		}

		$ranges     = self::read_json( 'ranges.json' );
		$media      = self::read_json( 'media.json' );
		$taxonomies = self::read_json( 'taxonomies.json' );
		$src_ranges = is_array( $ranges ) ? count( $ranges ) : 0;
		$src_media  = is_array( $media ) ? count( $media ) : 0;
		$src_cats   = isset( $taxonomies['categories'] ) ? count( (array) $taxonomies['categories'] ) : 0;
		$src_brands = isset( $taxonomies['brands'] ) ? count( (array) $taxonomies['brands'] ) : 0;
		$src_depts  = isset( $taxonomies['departments'] ) ? count( (array) $taxonomies['departments'] ) : 0;
		$src_subs   = 0;
		foreach ( (array) ( $taxonomies['categories'] ?? array() ) as $cat ) {
			$src_subs += count( (array) ( $cat['subcategories'] ?? array() ) );
		}

		/* ----------------------------------------------- the WordPress side */
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

		$products = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type='product' AND post_status='publish'";

		$meta_count = static function ( string $key, string $value ) use ( $wpdb ): int {
			return (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$wpdb->postmeta} pm
					 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
					 WHERE pm.meta_key = %s AND pm.meta_value = %s
					   AND p.post_type = 'product' AND p.post_status = 'publish'",
					$key,
					$value
				)
			);
		};

		$term_count = static function ( string $taxonomy ) use ( $wpdb ): int {
			return (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$wpdb->term_taxonomy} WHERE taxonomy = %s",
					$taxonomy
				)
			);
		};

		$wp_products = (int) $wpdb->get_var( $products );
		$wp_quote    = $meta_count( STAR_ELECTRIC_QUOTE_META, 'yes' );
		$wp_priced   = $meta_count( STAR_ELECTRIC_QUOTE_META, 'no' );
		$wp_oos      = $meta_count( '_star_electric_availability', 'Out of Stock' );

		$wp_sale = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->postmeta} pm
			 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			 WHERE pm.meta_key = '_star_electric_discount' AND pm.meta_value + 0 > 0
			   AND p.post_type = 'product' AND p.post_status = 'publish'"
		);

		$wp_variable = (int) $wpdb->get_var(
			"SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p
			 INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
			 INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
			 INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
			 WHERE tt.taxonomy = 'product_type' AND t.slug = 'variable'
			   AND p.post_type = 'product' AND p.post_status = 'publish'"
		);

		$wp_variations = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type='product_variation' AND post_status IN ('publish','private')"
		);

		$wp_ranges = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type=%s AND post_status='publish'",
				Star_Electric_Ranges::POST_TYPE
			)
		);

		// The catalogue's own categories, so a stray "Uncategorized" is not counted.
		$wp_cats = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->term_taxonomy} WHERE taxonomy='product_cat' AND parent = 0
			   AND term_id NOT IN ( SELECT term_id FROM {$wpdb->terms} WHERE slug = 'uncategorized' )"
		);
		$wp_subs = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->term_taxonomy} WHERE taxonomy='product_cat' AND parent <> 0"
		);

		$map = self::media_map();

		/* ------------------------------------------------------------ defects */
		// One query for every source id WordPress holds, rather than 4,348 of them.
		$have = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT pm.meta_value FROM {$wpdb->postmeta} pm
				 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				 WHERE pm.meta_key = %s AND p.post_type = 'product' AND p.post_status = 'publish'",
				STAR_ELECTRIC_SOURCE_ID_META
			)
		);
		$have    = array_flip( array_map( 'strval', (array) $have ) );
		$missing = 0;
		foreach ( $src_ids as $source_id ) {
			if ( '' !== $source_id && ! isset( $have[ $source_id ] ) ) {
				++$missing;
			}
		}

		$duplicates = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM (
					SELECT pm.meta_value FROM {$wpdb->postmeta} pm
					INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
					WHERE pm.meta_key = %s AND p.post_type = 'product' AND p.post_status = 'publish'
					GROUP BY pm.meta_value HAVING COUNT(*) > 1
				) d",
				STAR_ELECTRIC_SOURCE_ID_META
			)
		);

		/*
		 * A price of zero on this catalogue can only mean a quote-only product
		 * was given an invented one, so it is a defect and not a quantity.
		 */
		$zero = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->postmeta} pm
			 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			 WHERE pm.meta_key = '_price' AND pm.meta_value + 0 = 0 AND pm.meta_value <> ''
			   AND p.post_type IN ('product','product_variation')"
		);
		// phpcs:enable

		$row = static function ( string $label, int $source, int $wordpress ): array {
			return array( $label, $source, $wordpress, $source === $wordpress ? 'OK' : 'MISMATCH' );
		};

		return array(
			$row( 'products', $src['products'], $wp_products ),
			$row( 'quote-only products', $src['quote'], $wp_quote ),
			$row( 'priced products', $src['priced'], $wp_priced ),
			$row( 'products on genuine sale', $src['sale'], $wp_sale ),
			$row( 'variable products', $src['variable'], $wp_variable ),
			$row( 'variations', $src['variations'], $wp_variations ),
			$row( 'out of stock', $src['oos'], $wp_oos ),
			$row( 'ranges', $src_ranges, $wp_ranges ),
			$row( 'categories', $src_cats, $wp_cats ),
			$row( 'subcategories', $src_subs, $wp_subs ),
			$row( 'brands', $src_brands, $term_count( Star_Electric_Taxonomies::BRAND ) ),
			$row( 'departments', $src_depts, $term_count( Star_Electric_Taxonomies::DEPARTMENT ) ),
			$row( 'media masters', $src_media, count( $map ) ),
			array( 'failed imports', 0, $missing, 0 === $missing ? 'OK' : 'FAIL - records missing' ),
			array( 'duplicate source ids', 0, $duplicates, 0 === $duplicates ? 'OK' : 'FAIL - imported twice' ),
			array( 'products priced 0', 0, $zero, 0 === $zero ? 'OK' : 'FAIL - fake price present' ),
		);
	}
}
