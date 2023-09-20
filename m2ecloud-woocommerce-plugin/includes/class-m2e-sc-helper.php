<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 * @package    M2e_CS
 * @subpackage M2e_CS/includes
 */

defined( 'ABSPATH' ) || exit;

class M2e_SC_Helper {
	private static $cache = [];

	public static function get_server_endpoint() {
		return 'https://m2e.cloud';
	}

	public static function get_auth_data() {
		global $wpdb;

		if ( ! empty( self::$cache['auth_data'] ) ) {
			return self::$cache['auth_data'];
		}

		$data = get_plugin_data( M2E_SC_PLUGIN_FILE );
		self::$cache['auth_data'] = $wpdb->get_row( $wpdb->prepare( "
			SELECT key_id, user_id, permissions, consumer_key, consumer_secret, nonces
			FROM {$wpdb->prefix}woocommerce_api_keys
			WHERE description LIKE %s
			ORDER BY key_id DESC
			LIMIT 1
		", $data['Name'] . '%' ), ARRAY_A );

		return self::$cache['auth_data'];
	}

	public static function has_auth_data() {
		return ! empty( self::get_auth_data() );
	}

	public static function clear_auth_data() {
		global $wpdb;

		$data = get_plugin_data( M2E_SC_PLUGIN_FILE );
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}woocommerce_api_keys WHERE description LIKE %s",
				$data['Name'] . '%'
			)
		);
	}

	public static function build_authorize_params() {
		$state = [
			'email' => wp_get_current_user()->user_email,
			'url' => get_site_url(),
		];
		$params = [
			'app_name' => 'M2E Cloud: Sales Channels',
			'scope' => 'read_write',
			'user_id' => base64_encode( wp_json_encode( $state, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ),
			'return_url' => get_admin_url( null, 'admin.php?page=m2e-sc' ),
			'callback_url' => self::get_server_endpoint() . '/api/v1/woocommerce/account/login/',
		];

		return http_build_query( $params );
	}

	public static function build_jwt_token() {
		$auth_data = self::get_auth_data();
		$headers = wp_json_encode( [
			'alg' => 'HS256',
			'typ' => 'JWT',
		], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		$payload = wp_json_encode( [
			'iss' => get_admin_url(),
			'dest' => get_site_url(),
			'aud' => $auth_data['consumer_key'],
			'sub' => get_current_user_id(),
			'exp' => current_time( 'timestamp' ) + 120,
			'iat' => current_time( 'timestamp' ),
		], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		$encoded_headers = self::bin2base64( $headers );
		$encoded_payload = self::bin2base64( $payload );
		$token_parts = $encoded_headers . '.' . $encoded_payload;
		$signature = hash_hmac( 'sha256', $token_parts, $auth_data['consumer_secret'], true );

		return urlencode( $token_parts . '.' . self::bin2base64( $signature ) );
	}

	public static function bin2base64( $data ) {
		$encoded = base64_encode( $data );
		$encoded = strtr( $encoded, '+/', '-_' );

		return rtrim( $encoded, '=' );
	}
}
