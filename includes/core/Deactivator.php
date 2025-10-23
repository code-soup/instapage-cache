<?php
/**
 * Fired during plugin deactivation.
 *
 * @package CodeSoup\InstapageCache
 */

namespace CodeSoup\InstapageCache\Core;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * Deactivator class.
 */
class Deactivator {

	/**
	 * Deactivate the plugin.
	 */
	public static function deactivate(): void {
		self::remove_instapage_cache_role();
	}

	/**
	 * Remove instapage cache manager role.
	 */
	private static function remove_instapage_cache_role(): void {
		remove_role( 'instapage_cache_manager' );
	}
}
