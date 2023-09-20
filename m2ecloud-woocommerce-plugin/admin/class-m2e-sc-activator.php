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

class M2e_SC_Activator {

	public static function activate() {
		$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';

		if (
			in_array( $plugin_path, wp_get_active_and_valid_plugins() )
			|| ( is_multisite() && in_array( $plugin_path, wp_get_active_network_plugins() ) )
		) {

			if ( ! M2e_SC_Helper::has_auth_data() ) {
				add_option( 'm2e_cs_do_activation_redirect', true );
			}

			return;
		}

		set_transient( 'm2e-sc-admin-error', true, 20 );
	}

	public static function install() {
		wp_redirect( site_url() . '/wc-auth/v1/authorize?' . M2e_SC_Helper::build_authorize_params() );
	}

	public static function uninstall() {
		printf( '<div class="error is-dismissible"><p>'
		. esc_html__( 'WooCommerce Plugin is not founded.', 'm2e-sc' )
		. '</p></div>' );

		deactivate_plugins( plugin_basename( M2E_SC_PLUGIN_FILE ) );
	}
}
