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

class M2e_SC_Facade {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @var      array $actions The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @var      array $filters The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->actions = [];
		$this->filters = [];
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @param string   $hook          The name of the WordPress action that is being registered.
	 * @param object   $component     A reference to the instance of the object on which the action is defined.
	 * @param callable $callback      The name of the function definition on the $component.
	 * @param int      $priority      Optional. The priority at which the function should be fired. Default is 10.
	 * @param int      $accepted_args Optional. The number of arguments that should be passed to the $callback. Default
	 *                                is 1.
	 *
	 * @since    1.0.0
	 */
	public function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @param string   $hook          The name of the WordPress filter that is being registered.
	 * @param object   $component     A reference to the instance of the object on which the filter is defined.
	 * @param callable $callback      The name of the function definition on the $component.
	 * @param int      $priority      Optional. The priority at which the function should be fired. Default is 10.
	 * @param int      $accepted_args Optional. The number of arguments that should be passed to the $callback. Default
	 *                                is 1
	 *
	 * @since    1.0.0
	 */
	public function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @param array    $hooks         The collection of hooks that is being registered (that is, actions or filters).
	 * @param string   $hook          The name of the WordPress filter that is being registered.
	 * @param object   $component     A reference to the instance of the object on which the filter is defined.
	 * @param callable $callback      The name of the function definition on the $component.
	 * @param int      $priority      The priority at which the function should be fired.
	 * @param int      $accepted_args The number of arguments that should be passed to the $callback.
	 *
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 * @since    1.0.0
	 */
	private function add( $hooks, $hook, $callback, $priority, $accepted_args ) {
		$hooks[] = [
			'hook'          => $hook,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		];

		return $hooks;
	}

	public function add_menu_item( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $icon_url = '', $position = null, $on_load_callback = '' ) {
		$hookname = add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url, $position );

		if ( ! empty( $on_load_callback ) ) {
			add_action( 'load-' . $hookname, $on_load_callback );
		}
	}

	public function add_sub_menu_item( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null, $on_load_callback = '' ) {
		$hookname = add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback, $position, $on_load_callback );

		if ( ! empty( $on_load_callback ) ) {
			add_action( 'load-' . $hookname, $on_load_callback );
		}
	}

	public function enqueue_styles( $path ) {
		wp_enqueue_style( M2E_SC_NAME, plugin_dir_url( M2E_SC_PLUGIN_FILE ) . $path, [], M2E_SC_VERSION );
	}

	public function enqueue_scripts( $path, $deps = [] ) {
		wp_enqueue_script( M2E_SC_NAME, plugin_dir_url( M2E_SC_PLUGIN_FILE ) . $path, $deps, M2E_SC_VERSION, false );
	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], $hook['callback'], $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], $hook['callback'], $hook['priority'], $hook['accepted_args'] );
		}
	}
}
