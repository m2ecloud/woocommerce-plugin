<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-m2e-sc-helper.php';

M2e_SC_Helper::clear_auth_data();
