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

class M2e_SC_Bootstrap {
	public static function register_activate() {
		register_activation_hook( M2E_SC_PLUGIN_FILE, function () {
			require_once plugin_dir_path( M2E_SC_PLUGIN_FILE ) . 'admin/class-m2e-sc-activator.php';
			M2e_SC_Activator::activate();
		} );
	}

	public static function register_deactivate() {
		register_deactivation_hook( M2E_SC_PLUGIN_FILE, function () {
			require_once plugin_dir_path( M2E_SC_PLUGIN_FILE ) . 'admin/class-m2e-sc-deactivator.php';
			M2e_SC_Deactivator::deactivate();
		} );
	}

	public static function load_classes() {
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-m2e-sc-helper.php';
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-m2e-sc-facade.php';
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-m2e-sc-admin.php';
	}

	public function run() {
		$facade       = new M2e_SC_Facade();
		$plugin_admin = new M2e_SC_Admin( $facade );

		$facade->add_action( 'admin_init', function () {
			if ( get_option( 'm2e_cs_do_activation_redirect', false ) ) {
				require_once plugin_dir_path( M2E_SC_PLUGIN_FILE ) . 'admin/class-m2e-sc-activator.php';
				delete_option( 'm2e_cs_do_activation_redirect' );
				M2e_SC_Activator::install();
			}
		} );
		$facade->add_action( 'admin_notices', function () {
			if ( get_transient( 'm2e-sc-admin-error' ) ) {
				require_once plugin_dir_path( M2E_SC_PLUGIN_FILE ) . 'admin/class-m2e-sc-activator.php';
				M2e_SC_Activator::uninstall();
			}
		} );

		$facade->add_action( 'admin_enqueue_scripts', function () use ( $facade ) {
			$facade->enqueue_styles( 'admin/css/m2e-sc-admin.css' );
		} );
		$facade->add_action( 'admin_enqueue_scripts', function () use ( $facade ) {
			$facade->enqueue_scripts( 'admin/js/m2e-sc-admin.js', [ 'jquery' ] );
		} );
		$facade->add_action( 'admin_menu', [ $plugin_admin, 'init_menu' ] );

		$facade->add_filter( 'plugin_action_links_' . plugin_basename( M2E_SC_PLUGIN_FILE ), [$plugin_admin, 'add_plugin_links'] );

		$facade->run();
	}
}
