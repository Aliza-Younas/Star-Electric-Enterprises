<?php
/**
 * WP-CLI front end for the catalogue importer.
 *
 * All of the work lives in Star_Electric_Importer. This class only loops the
 * engine's resumable steps and prints what they report, so the CLI route and
 * the dashboard route cannot drift apart: fix a bug in one and both get it.
 *
 * Usage from the WordPress root:
 *
 *   wp star-electric import-media
 *   wp star-electric import-taxonomies
 *   wp star-electric import-ranges
 *   wp star-electric import-products [--batch=<n>] [--offset=<n>]
 *   wp star-electric audit
 *   wp star-electric reset [--step=<step>]
 *
 * The payload ships inside the plugin, so there is no --dir to point anywhere.
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

	/**
	 * Run one step to completion, batch by batch.
	 *
	 * @param string $label  Human label.
	 * @param string $step   Step key.
	 * @param int    $size   Batch size.
	 * @param int    $offset Starting offset.
	 */
	private function drive( string $label, string $step, int $size, int $offset ): void {
		$created = 0;
		$updated = 0;
		$failed  = 0;

		do {
			switch ( $step ) {
				case 'media':
					$result = Star_Electric_Importer::step_media( $offset, $size );
					break;
				case 'ranges':
					$result = Star_Electric_Importer::step_ranges( $offset, $size );
					break;
				case 'products':
					$result = Star_Electric_Importer::step_products( $offset, $size );
					break;
				default:
					WP_CLI::error( 'Unknown step: ' . $step );
					return;
			}

			foreach ( $result['errors'] as $error ) {
				WP_CLI::warning( $error );
			}

			$created += (int) $result['created'];
			$updated += (int) $result['updated'] + (int) $result['reused'];
			$failed  += (int) $result['failed'];

			// A step that cannot advance would otherwise spin forever.
			if ( $result['offset'] <= $offset && ! $result['complete'] ) {
				WP_CLI::error(
					sprintf( '%s stalled at %d of %d.', $label, $offset, (int) $result['total'] )
				);
				return;
			}

			$offset = (int) $result['offset'];
			WP_CLI::log( sprintf( '  %s: %d of %d', $label, $offset, (int) $result['total'] ) );
		} while ( ! $result['complete'] );

		WP_CLI::success(
			sprintf(
				'%s: %d created, %d updated, %d failed.',
				$label,
				$created,
				$updated,
				$failed
			)
		);
	}

	/**
	 * Import every unique image master once.
	 *
	 * ## OPTIONS
	 *
	 * [--batch=<n>]
	 * : Images per batch. Default 25.
	 *
	 * [--offset=<n>]
	 * : Skip the first n images. Use to resume. Default 0.
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Associative args.
	 */
	public function import_media( $args, $assoc_args ): void {
		unset( $args );
		$this->drive(
			'Media',
			'media',
			max( 1, (int) ( $assoc_args['batch'] ?? 25 ) ),
			(int) ( $assoc_args['offset'] ?? 0 )
		);
	}

	/**
	 * Create product categories, brands and departments.
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Associative args.
	 */
	public function import_taxonomies( $args, $assoc_args ): void {
		unset( $args, $assoc_args );

		$result = Star_Electric_Importer::step_taxonomies();
		foreach ( $result['errors'] as $error ) {
			WP_CLI::warning( $error );
		}

		WP_CLI::success(
			sprintf(
				'Taxonomies: %d created, %d already present, %d failed.',
				(int) $result['created'],
				(int) $result['updated'],
				(int) $result['failed']
			)
		);
	}

	/**
	 * Import the range/family records as star_range posts.
	 *
	 * ## OPTIONS
	 *
	 * [--batch=<n>]
	 * : Records per batch. Default 50.
	 *
	 * [--offset=<n>]
	 * : Skip the first n records. Use to resume. Default 0.
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Associative args.
	 */
	public function import_ranges( $args, $assoc_args ): void {
		unset( $args );
		$this->drive(
			'Ranges',
			'ranges',
			max( 1, (int) ( $assoc_args['batch'] ?? 50 ) ),
			(int) ( $assoc_args['offset'] ?? 0 )
		);
	}

	/**
	 * Import the verified products as WooCommerce products.
	 *
	 * ## OPTIONS
	 *
	 * [--batch=<n>]
	 * : Records per batch. Default 50.
	 *
	 * [--offset=<n>]
	 * : Skip the first n records. Use to resume. Default 0.
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Associative args.
	 */
	public function import_products( $args, $assoc_args ): void {
		unset( $args );

		if ( ! class_exists( 'WC_Product_Simple' ) ) {
			WP_CLI::error( 'WooCommerce is not active.' );
		}

		$this->drive(
			'Products',
			'products',
			max( 1, (int) ( $assoc_args['batch'] ?? 50 ) ),
			(int) ( $assoc_args['offset'] ?? 0 )
		);
	}

	/**
	 * Reconcile what is in WordPress against the source payload.
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Associative args.
	 */
	public function audit( $args, $assoc_args ): void {
		unset( $args, $assoc_args );

		$rows = Star_Electric_Importer::audit();

		WP_CLI\Utils\format_items(
			'table',
			array_map(
				static function ( $r ) {
					return array(
						'item'      => $r[0],
						'source'    => $r[1],
						'wordpress' => $r[2],
						'status'    => $r[3],
					);
				},
				$rows
			),
			array( 'item', 'source', 'wordpress', 'status' )
		);

		foreach ( $rows as $row ) {
			if ( 'OK' !== $row[3] ) {
				WP_CLI::error( 'Reconciliation failed on: ' . $row[0], false );
			}
		}
	}

	/**
	 * Clear the recorded progress for a step so it starts again from the top.
	 *
	 * The media map is deliberately preserved, so a re-run reuses the images
	 * already uploaded rather than importing 2,054 files a second time.
	 *
	 * ## OPTIONS
	 *
	 * [--step=<step>]
	 * : media, taxonomies, ranges, products, or all. Default all.
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Associative args.
	 */
	public function reset( $args, $assoc_args ): void {
		unset( $args );

		$step = (string) ( $assoc_args['step'] ?? 'all' );
		Star_Electric_Importer::reset( $step );
		WP_CLI::success( 'Progress cleared for: ' . $step );
	}
}
