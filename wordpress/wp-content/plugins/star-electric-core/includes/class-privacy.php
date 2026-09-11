<?php
/**
 * Keep the shop's own account details out of public view.
 *
 * WordPress publishes more about the people behind a site than a shop needs to,
 * and on this install it was publishing the owner's email address. The account
 * slug is derived from the address it was registered with, so three separate
 * routes handed it out to anyone who asked:
 *
 *   /wp-json/wp/v2/users            lists every account with a published post
 *   /?author=1                      redirects to /author/<slug>/
 *   /wp-json/oembed/1.0/embed       carries author_name and author_url
 *
 * That is the store's own inbox, which is the address a phishing attempt would
 * aim at and half of what a login attempt needs. A storefront has no author
 * pages, no bylines and no reason to expose any of it.
 *
 * Everything here applies to visitors only. A signed-in user still gets the
 * user endpoints, because Elementor, Gutenberg and the media library all read
 * them and breaking the editor to hide a name would be a poor trade.
 *
 * @package StarElectric
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Closes the account-disclosure routes for visitors.
 */
class Star_Electric_Privacy {

	/**
	 * Hook up.
	 */
	public static function init(): void {
		add_filter( 'rest_endpoints', array( __CLASS__, 'rest_endpoints' ) );
		add_action( 'template_redirect', array( __CLASS__, 'block_author_archive' ) );
		add_filter( 'oembed_response_data', array( __CLASS__, 'oembed' ) );
		add_filter( 'rest_prepare_user', array( __CLASS__, 'rest_user' ), 10, 3 );
	}

	/**
	 * Whether the request is a visitor rather than somebody working on the site.
	 */
	private static function is_visitor(): bool {
		return ! is_user_logged_in();
	}

	/**
	 * Drop the user routes for visitors.
	 *
	 * @param array $endpoints REST endpoints.
	 * @return array
	 */
	public static function rest_endpoints( $endpoints ) {
		if ( ! self::is_visitor() || ! is_array( $endpoints ) ) {
			return $endpoints;
		}

		unset( $endpoints['/wp/v2/users'] );
		unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );

		return $endpoints;
	}

	/**
	 * Belt and braces: if a user response is built anyway, say nothing useful.
	 *
	 * @param WP_REST_Response $response Response.
	 * @param WP_User          $user     User.
	 * @param WP_REST_Request  $request  Request.
	 * @return WP_REST_Response
	 */
	public static function rest_user( $response, $user, $request ) {
		unset( $user, $request );

		if ( self::is_visitor() && $response instanceof WP_REST_Response ) {
			$response->set_data( array() );
		}

		return $response;
	}

	/**
	 * Refuse author archives and the ?author=N redirect that reveals the slug.
	 *
	 * A 404 rather than a redirect home: there is no author archive on this
	 * site, and saying so is both honest and silent about who exists.
	 */
	public static function block_author_archive(): void {
		if ( ! self::is_visitor() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$probing = isset( $_GET['author'] ) && '' !== $_GET['author'];

		if ( $probing || is_author() ) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			nocache_headers();
		}
	}

	/**
	 * Strip the byline from oEmbed responses.
	 *
	 * @param array $data Response data.
	 * @return array
	 */
	public static function oembed( $data ) {
		if ( self::is_visitor() && is_array( $data ) ) {
			unset( $data['author_name'], $data['author_url'] );
		}

		return $data;
	}
}
