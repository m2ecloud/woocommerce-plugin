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
 * @subpackage M2e_CS/admin
 */

defined( 'ABSPATH' ) || exit;

class M2e_SC_Admin {

	/**
	 * The Facade object which interact with WordPress.
	 *
	 * @var M2e_SC_Facade
	 */
	private $facade;

	public function __construct( M2e_SC_Facade $facade ) {
		$this->facade = $facade;
	}

	public function check_auth() {
		if ( ! M2e_SC_Helper::has_auth_data() ) {
			wp_redirect( site_url() . '/wc-auth/v1/authorize?' . M2e_SC_Helper::build_authorize_params() );
		}
	}

	public function add_plugin_links( $links ) {
		$action_links = [
			'listings' => '<a href="' . admin_url( 'admin.php?page=m2e-sc' ) . '" title="' . esc_html__( 'Manage Amazon, eBay & Walmart Listings', 'm2e-sc' ) . '">' . esc_html__( 'Manage Amazon, eBay & Walmart Listings', 'm2e-sc' ) . '</a>',
			'settings' => '<a href="' . admin_url( 'admin.php?page=m2e-sc-settings' ) . '" title="' . esc_html__( 'Settings', 'm2e-sc' ) . '">' . esc_html__( 'Settings', 'm2e-sc' ) . '</a>',
		];

		return array_merge( $action_links, $links );
	}

	public function init_menu() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$main_page = M2E_SC_NAME;
		$on_load_callback = [ $this, 'check_auth' ];

		$this->facade->add_menu_item(
			__( 'M2e Cloud: Sales Channels', 'm2e-sc' ),
			__( 'Sales Channels', 'm2e-sc' ),
			'edit_posts',
			$main_page,
			null,
			'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCA0OCA0OCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCiAgICA8cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCINCiAgICAgICAgICBkPSJNOC4zODUxMyAyNy4xNjE0QzcuMDU2ODYgMjcuMTYxNCA1LjU5OTMzIDI2LjY1ODIgNS4yMDg3NyAyNS4zODg2QzQuOTMwNDEgMjQuNDgzOCA0LjkzMDQxIDIzLjUxNjIgNS4yMDg3NyAyMi42MTE0QzUuNTUzNyAyMS40OTAyIDYuNTIwMzIgMjAuNTIzNiA4LjQ1MzU2IDE4LjU5MDNMOC40NTM1NyAxOC41OTAzTDE4LjU5MDMgOC40NTM1N0wxOC41OTAzIDguNDUzNTZDMjAuNTIzNiA2LjUyMDMyIDIxLjQ5MDIgNS41NTM3IDIyLjYxMTQgNS4yMDg3N0MyMy41MTYyIDQuOTMwNDEgMjQuNDgzOCA0LjkzMDQxIDI1LjM4ODYgNS4yMDg3N0MyNi41MDk4IDUuNTUzNyAyNy40NzY1IDYuNTIwMzMgMjkuNDA5NyA4LjQ1MzU3TDM0LjEwMTYgMTMuMTQ1NUMzNC45ODc0IDE0LjAzMTMgMzUuNDMwNCAxNC40NzQyIDM1LjQ1MTQgMTQuODU2QzM1LjQ2ODEgMTUuMTU5MSAzNS4zNDYyIDE1LjQ1MzQgMzUuMTIwMSAxNS42NTU5QzM0LjgzNTMgMTUuOTExIDM0LjIwODkgMTUuOTExIDMyLjk1NjEgMTUuOTExSDI1LjUyNDhDMjQuOTQ2MSAxNS45MTEgMjQuNjU2NyAxNS45MTEgMjQuNDM3MyAxNS43OTQ4QzI0LjI2MDEgMTUuNzAxIDI0LjExNTMgMTUuNTU2MiAyNC4wMjE0IDE1LjM3OTJDMjMuOTA1MiAxNS4xNTk3IDIzLjkwNTEgMTQuODcwNCAyMy45MDQ4IDE0LjI5MTZMMjMuOTA0NSAxMy40MDU2QzIzLjkwNDUgMTMuMjI2NiAyMy42OTQ5IDEzLjEzMTggMjMuNTYyNSAxMy4yNTA4TDE0LjYwMzQgMjEuMzA0MUMxNC40NjI0IDIxLjQzMDkgMTQuNTUxMiAyMS42NjYzIDE0Ljc0IDIxLjY2NjNMNDEuMjg2NCAyMS42NjYyQzQxLjk0MiAyMS42NjYyIDQyLjU5ODQgMjEuOTg0NyA0Mi43OTEyIDIyLjYxMTRDNDMuMDY5NiAyMy41MTYyIDQzLjA2OTYgMjQuNDgzOCA0Mi43OTEyIDI1LjM4ODZDNDIuNDQ2MyAyNi41MDk4IDQxLjQ3OTcgMjcuNDc2NCAzOS41NDY0IDI5LjQwOTdMMzguNDQzMiAzMC41MTI5QzM4LjI5NjYgMzAuNTA0MyAzOC4xNDg4IDMwLjUgMzggMzAuNUMzMy44NTc5IDMwLjUgMzAuNSAzMy44NTc5IDMwLjUgMzhDMzAuNSAzOC4xNDg4IDMwLjUwNDMgMzguMjk2NiAzMC41MTI5IDM4LjQ0MzJMMjkuNDA5NyAzOS41NDY0TDI5LjQwOTcgMzkuNTQ2NUMyNy40NzY0IDQxLjQ3OTcgMjYuNTA5OCA0Mi40NDYzIDI1LjM4ODYgNDIuNzkxMkMyNC40ODM4IDQzLjA2OTYgMjMuNTE2MiA0My4wNjk2IDIyLjYxMTQgNDIuNzkxMkMyMS40OTAyIDQyLjQ0NjMgMjAuNTIzNiA0MS40Nzk3IDE4LjU5MDMgMzkuNTQ2NEwxNC43MjYxIDM1LjY4MjJDMTMuODQwMiAzNC43OTY0IDEzLjM5NzMgMzQuMzUzNCAxMy4zNzYzIDMzLjk3MTdDMTMuMzU5NiAzMy42Njg2IDEzLjQ4MTUgMzMuMzc0MyAxMy43MDc2IDMzLjE3MThDMTMuOTkyNCAzMi45MTY3IDE0LjYxODggMzIuOTE2NyAxNS44NzE2IDMyLjkxNjdIMjMuMDY1NUMyMy42NDQyIDMyLjkxNjcgMjMuOTMzNiAzMi45MTY3IDI0LjE1MyAzMy4wMzI5QzI0LjMzMDIgMzMuMTI2NiAyNC40NzUgMzMuMjcxNCAyNC41Njg5IDMzLjQ0ODVDMjQuNjg1MSAzMy42NjggMjQuNjg1MiAzMy45NTczIDI0LjY4NTUgMzQuNTM2TDI0LjY4NTggMzUuNDIyMUMyNC42ODU4IDM1LjYwMSAyNC44OTU0IDM1LjY5NTkgMjUuMDI3OCAzNS41NzY5TDMzLjk4NjkgMjcuNTIzNUMzNC4xMjc5IDI3LjM5NjggMzQuMDM5MSAyNy4xNjE0IDMzLjg1MDMgMjcuMTYxNEw4LjM4NTEzIDI3LjE2MTRaTTM0LjE2NzQgMzQuNzg4OEwzNC43ODg4IDM0LjE2NzRDMzQuNTYzOSAzNC4zNTU5IDM0LjM1NTkgMzQuNTYzOSAzNC4xNjc0IDM0Ljc4ODhaIg0KICAgICAgICAgIGZpbGw9InVybCgjcGFpbnQwX2xpbmVhcl8xMzkxXzI1MzEpIi8+DQogICAgPG1hc2sgaWQ9Im1hc2swXzEzOTFfMjUzMSIgc3R5bGU9Im1hc2stdHlwZTphbHBoYSIgbWFza1VuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeD0iMzMiIHk9IjMzIiB3aWR0aD0iMTAiIGhlaWdodD0iMTAiPg0KICAgICAgICA8Y2lyY2xlIGN4PSIzOCIgY3k9IjM4IiByPSI1IiBmaWxsPSIjQzRDNEM0Ii8+DQogICAgPC9tYXNrPg0KICAgIDxnIG1hc2s9InVybCgjbWFzazBfMTM5MV8yNTMxKSI+DQogICAgICAgIDxyZWN0IHg9IjMyLjcwNTkiIHk9IjMyLjk1MzkiIHdpZHRoPSIxMC4yOTQxIiBoZWlnaHQ9IjUuMDQ2MTQiIGZpbGw9IiMyRTVGREMiLz4NCiAgICAgICAgPHJlY3QgeD0iMzIuNzA1OSIgeT0iMzgiIHdpZHRoPSIxMC4yOTQxIiBoZWlnaHQ9IjUuMDQ2MTQiIGZpbGw9IiNGRkRBMTUiLz4NCiAgICA8L2c+DQogICAgPGRlZnM+DQogICAgICAgIDxsaW5lYXJHcmFkaWVudCBpZD0icGFpbnQwX2xpbmVhcl8xMzkxXzI1MzEiIHgxPSIxMi45MTY3IiB5MT0iMi44ODg4OSIgeDI9IjM0LjAyNzgiIHkyPSI1MC45MTY3Ig0KICAgICAgICAgICAgICAgICAgICAgICAgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiPg0KICAgICAgICAgICAgPHN0b3Agc3RvcC1jb2xvcj0iIzY3OTBGRiIvPg0KICAgICAgICAgICAgPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjMjM1Q0Y5Ii8+DQogICAgICAgIDwvbGluZWFyR3JhZGllbnQ+DQogICAgPC9kZWZzPg0KPC9zdmc+DQo=',
			'56.501',
			$on_load_callback
		);
		$this->facade->add_sub_menu_item(
			$main_page,
			__( 'M2e Cloud: Sales Channels > Dashboard', 'm2e-sc' ),
			__( 'Dashboard', 'm2e-sc' ),
			'edit_posts',
			$main_page,
			function () {
				$this->render_page( '/app/dashboard' );
			},
			null,
			$on_load_callback
		);
		$this->facade->add_sub_menu_item(
			$main_page,
			__( 'M2e Cloud: Sales Channels > Listings', 'm2e-sc' ),
			__( 'Listings', 'm2e-sc' ),
			'edit_posts',
			'm2e-sc-channels',
			function () {
				$this->render_page( '/app/channel' );
			},
			null,
			$on_load_callback
		);
		$this->facade->add_sub_menu_item(
			$main_page,
			__( 'M2e Cloud: Sales Channels > Orders', 'm2e-sc' ),
			__( 'Orders', 'm2e-sc' ),
			'edit_posts',
			'm2e-sc-orders',
			function () {
				$this->render_page( '/app/orders' );
			},
			null,
			$on_load_callback
		);
		$this->facade->add_sub_menu_item(
			$main_page,
			__( 'M2e Cloud: Sales Channels > Logs', 'm2e-sc' ),
			__( 'Logs', 'm2e-sc' ),
			'edit_posts',
			'm2e-sc-logs',
			function () {
				$this->render_page( '/app/logs' );
			},
			null,
			$on_load_callback
		);
		$this->facade->add_sub_menu_item(
			$main_page,
			__( 'M2e Cloud: Sales Channels > Settings', 'm2e-sc' ),
			__( 'Settings', 'm2e-sc' ),
			'edit_posts',
			'm2e-sc-settings',
			function () {
				$this->render_page( '/app/settings' );
			},
			null,
			$on_load_callback
		);
	}

	private function render_page( $path ) {
		$params = [
			'woocommerce_embedded' => '1',
			'session_token' => M2e_SC_Helper::build_jwt_token(),
		];
		$url = M2e_SC_Helper::get_server_endpoint() . $path . '?' . http_build_query( $params );
		?>
		<iframe class="m2e-cs-frame" src="<?php echo esc_url( $url ); ?>" frameborder="0"></iframe>
		<?php
	}
}
