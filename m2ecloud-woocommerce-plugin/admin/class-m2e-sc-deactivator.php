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

class M2e_SC_Deactivator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		M2e_SC_Helper::clear_auth_data();
	}

}
