<?php
/**
 * Frontend Init Class.
 *
 * @package CodeSoup\InstapageCache
 */

namespace CodeSoup\InstapageCache\Frontend;

use function CodeSoup\InstapageCache\plugin;

/** If this file is called directly, abort. */
defined( 'ABSPATH' ) || die;

/**
 * The public-facing functionality of the plugin.
 */
class Init {

	/**
	 * Init constructor.
	 */
	public function __construct() {
		$this->add_hooks();
	}

	/**
	 * Add the frontend hooks.
	 */
	private function add_hooks(): void {
		$hooker = plugin()->get( 'hooker' );
		$hooker->add_actions(
			array(
				// array( 'wp_enqueue_scripts', $this ),
			)
		);
	}

	/**
	 * Enqueue the frontend styles.
	 */
	public function wp_enqueue_scripts(): void {

		$assets_handler = plugin()->get( 'assets' );
		$plugin_version = plugin()->config['PLUGIN_VERSION'];

		// Enqueue the main admin stylesheet.
		wp_enqueue_style(
			'codesoup_ilc-common',
			$assets_handler->get_asset_url( 'frontend-common.css' ),
			array(),
			$plugin_version
		);

		// Enqueue the webpack runtime script.
		wp_enqueue_script(
			'codesoup_ilc-runtime',
			$assets_handler->get_asset_url( 'runtime.js' ),
			array(),
			$plugin_version,
			true
		);

		// Enqueue the vendor libs script, dependent on the runtime.
		wp_enqueue_script(
			'codesoup_ilc-vendor',
			$assets_handler->get_asset_url( 'vendor-libs.js' ),
			array( 'codesoup_ilc-runtime' ),
			$plugin_version,
			true
		);

		// Enqueue the main admin script, dependent on runtime and vendors.
		wp_enqueue_script(
			'codesoup_ilc-frontend-common',
			$assets_handler->get_asset_url( 'frontend-common.js' ),
			array( 'codesoup_ilc-runtime', 'codesoup_ilc-vendor' ),
			$plugin_version,
			true
		);
	}
}
