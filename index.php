<?php
/**
 * Plugin main file.
 *
 * @package CodeSoup\InstapageCache
 */

namespace CodeSoup\InstapageCache;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || die;

/**
 * Plugin Name:       Instapage Cache
 * Plugin URI:        https://github.com/code-soup/instapage-cache
 * Description:       Boost performance and reduce server load with Instapage Cache. This add-on for Instapage lets you easily manage and clear cache to speed up page load time.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.1
 * Author:            Code Soup
 * Author URI:        https://www.codesoup.co
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.en.html
 * Update URI:        https://github.com/code-soup/wordpress-plugin-boilerplate
 * Text Domain:       instapage-cache
 * Domain Path:       /languages
 */

// NOTE: Activation hooks need to be inside index.php file or it might not work properly.
// It can fail without error, WordPress is silently failing in case of error.

// The code that runs during plugin activation.
// - includes/core/Activator.php.
register_activation_hook(
	__FILE__,
	function () {

		// On activate do this.
		\CodeSoup\InstapageCache\Core\Activator::activate();
	}
);

// The code that runs during plugin deactivation.
// - includes/core/Deactivator.php.
register_deactivation_hook(
	__FILE__,
	function () {

		// On deactivate do that.
		\CodeSoup\InstapageCache\Core\Deactivator::deactivate();
	}
);

// Run plugin, run.
require_once 'run.php';
