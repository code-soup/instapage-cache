<?php
/**
 * I18n class.
 *
 * @package CodeSoup\InstapageCache
 */

declare(strict_types=1);

namespace CodeSoup\InstapageCache\Core;

use function CodeSoup\InstapageCache\plugin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 */
class I18n {

	use \CodeSoup\InstapageCache\Traits\HelpersTrait;

	/**
	 * Main plugin instance.
	 *
	 * @var self|null
	 * @since 1.0.0
	 */
	protected static ?self $instance = null;

	/**
	 * I18n constructor.
	 */
	public function __construct() {
		$this->register_hooks();
	}

	/**
	 * Register hooks for internationalization
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function register_hooks(): void {
		$hooker = plugin()->get( 'hooker' );

		// Register the textdomain loading hook.
		$hooker->add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
	}

	/**
	 * Load the plugin text domain.
	 */
	public function load_plugin_textdomain(): void {
		load_plugin_textdomain(
			'instapage-cache',
			false,
			plugin()->get_basename() . '/languages/'
		);
	}
}
