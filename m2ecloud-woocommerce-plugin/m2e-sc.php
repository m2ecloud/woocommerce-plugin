<?php
/**
 * Plugin Name:       M2E Cloud: Sales Channels
 * Plugin URI:        https://m2ecloud.com/walmart-ebay-amazon-woocommerce-integration-plugin-m2e
 * Description:       A complete integration for Amazon, eBay & Walmart marketplaces
 * Version:           1.0.0
 * Requires at least: 6.1
 * Requires PHP:      7.2
 * Author:            M2E Cloud
 * Author URI:        https://m2ecloud.com/about
 * License:           GPLv2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Woo:                  replacewithreal:hash
 * WC requires at least: 8.0.0
 * WC tested up to:      8.0.2
 */

defined( 'ABSPATH' ) || exit;

define( 'M2E_SC_NAME', 'm2e-sc' );
define( 'M2E_SC_VERSION', '1.0.0' );

if ( ! defined( 'M2E_SC_PLUGIN_FILE' ) ) {
	define( 'M2E_SC_PLUGIN_FILE', __FILE__ );
}

require plugin_dir_path( __FILE__ ) . 'includes/class-m2e-sc-bootstrap.php';
M2e_SC_Bootstrap::load_classes();
M2e_SC_Bootstrap::register_activate();
M2e_SC_Bootstrap::register_deactivate();

( new M2e_SC_Bootstrap() )->run();
