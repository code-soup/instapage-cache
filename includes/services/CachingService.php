<?php
/**
 * Caching Service.
 *
 * @package CodeSoup\InstapageCache
 */

declare( strict_types=1 );

namespace CodeSoup\InstapageCache\Services;

use CodeSoup\InstapageCache\Admin\SettingsPage;
use function CodeSoup\InstapageCache\plugin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * CachingService class.
 */
class CachingService {

	/**
	 * Instapage host.
	 *
	 * @var string
	 */
	private const INSTAPAGE_HOST = 'plugin.pageserve.co';

	/**
	 * Cache base directory.
	 *
	 * @var string
	 */
	private const CACHE_BASE_DIR = WP_CONTENT_DIR . '/instapage-cache';

	/**
	 * Path to cache directory for specific page.
	 *
	 * @var string
	 */
	private string $path = '';

	/**
	 * Settings page instance.
	 *
	 * @var SettingsPage
	 */
	private SettingsPage $settings_page;

	/**
	 * Constructor.
	 *
	 * @param SettingsPage $settings_page Settings page instance.
	 */
	public function __construct( SettingsPage $settings_page ) {
		$this->settings_page = $settings_page;
	}

	/**
	 * Initialize the service.
	 */
	public function init(): void {
		// Caching not enabled
		if ( ! $this->settings_page->get_option( 'enable_caching' ) ) {
			return;
		}

		$hooker = plugin()->get( 'hooker' );

		$hooker->add_filters(
			array(
				array( 'pre_http_request', $this, 'maybe_block_request', 10, 3 ),
				array( 'http_response', $this, 'cache_instapage_response', 10, 3 ),
				array( 'pre_get_posts', $this, 'maybe_serve_cached_response' ),
			)
		);

		$hooker->add_actions(
			array(
				array( 'instapage_cache_autocleanup', $this, 'delete_instapage_cache' ),
			)
		);
	}

	/**
	 * Check if local cache exists. If cache exists block request.
	 *
	 * @param mixed  $block_request Whether to block the request.
	 * @param array  $parsed_args   Parsed arguments.
	 * @param string $url           Request URL.
	 * @return mixed
	 */
	public function maybe_block_request( $block_request, array $parsed_args, string $url ) {
		$this->set_path( $url );

		// Is request to instapage
		if ( $this->is_instapage( $url ) ) {
			// Don't block request: Page does not exist in Instapage WP table
			if ( ! $this->is_instapage_published() ) {
				return false;
			}

			// Don't block request: Local cached copy does not exist
			if ( ! $this->is_page_cached() ) {
				return false;
			}
		}

		return $block_request;
	}

	/**
	 * Cache response.
	 *
	 * @param array  $response    Response array.
	 * @param array  $parsed_args Parsed arguments.
	 * @param string $url         Request URL.
	 * @return array
	 */
	public function cache_instapage_response( array $response, array $parsed_args, string $url ): array {
		// Is request to instapage
		if ( ! $this->is_instapage( $url ) ) {
			return $response;
		}

		// Update path
		$this->set_path( $url );

		$body = do_shortcode( wp_remote_retrieve_body( $response ) );

		// Save HTML response for next time
		if ( wp_mkdir_p( $this->get_cache_dir() ) ) {
			$fs = new \WP_Filesystem_Direct( '' );
			$fs->put_contents( $this->get_cache_file_path(), $body );
		}

		// Parse Shortcodes in response
		$response['body'] = $body;

		return $response;
	}

	/**
	 * Maybe serve cached response.
	 *
	 * @param \WP_Query $query Query object.
	 */
	public function maybe_serve_cached_response( \WP_Query $query ): void {
		if ( ! $query->is_main_query() || is_admin() ) {
			return;
		}

		$this->set_path( $_SERVER['REQUEST_URI'] ?? '' );

		// Manually disabled for this page
		if ( $this->is_page_caching_disabled( intval( $this->get_page_id() ) ) ) {
			return;
		}

		if ( ! $this->is_instapage_published() || ! $this->is_page_cached() ) {
			return;
		}

		$fs = new \WP_Filesystem_Direct( '' );

		echo do_shortcode( $fs->get_contents( $this->get_cache_file_path() ) );
		printf(
			'<!-- instapage-cache-plugin-cached-response %s -->',
			date( 'Y-m-d h:i' )
		);

		exit;
	}

	/**
	 * Delete instapage cache.
	 */
	public function delete_instapage_cache(): void {
		$fs = new \WP_Filesystem_Direct( '' );
		$fs->rmdir( self::CACHE_BASE_DIR, true );
	}

	/**
	 * Set instapage path.
	 *
	 * @param string $url URL to parse.
	 */
	private function set_path( string $url ): void {
		$this->path = trim( $this->get_url_part( $url, 'path' ), '/' );
	}

	/**
	 * Get instapage page ID in WP.
	 *
	 * @return int|null
	 */
	private function get_page_id(): ?int {
		global $wpdb;

		$result = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}instapage_pages WHERE slug = %s",
				trim( $this->path, '/' )
			)
		);

		return $result ? (int) $result : null;
	}

	/**
	 * Get cache directory for specific page.
	 *
	 * @return string
	 */
	private function get_cache_dir(): string {
		return sprintf( '%s/%s', self::CACHE_BASE_DIR, $this->path );
	}

	/**
	 * Get cache file path.
	 *
	 * @return string
	 */
	private function get_cache_file_path(): string {
		return sprintf( '%s/%s/index.html', self::CACHE_BASE_DIR, $this->path );
	}

	/**
	 * Check if request is made to instapage host.
	 *
	 * @param string $url URL to check.
	 * @return bool
	 */
	private function is_instapage( string $url ): bool {
		return self::INSTAPAGE_HOST === $this->get_url_part( $url, 'host' );
	}

	/**
	 * Verify instapage is published in WP.
	 *
	 * @return bool
	 */
	private function is_instapage_published(): bool {
		return ! empty( $this->get_page_id() );
	}

	/**
	 * Verify if page is locally cached.
	 *
	 * @return bool
	 */
	private function is_page_cached(): bool {
		$fs = new \WP_Filesystem_Direct( '' );
		return $fs->exists( $this->get_cache_file_path() );
	}

	/**
	 * Get URL part.
	 *
	 * @param string $url  URL to parse.
	 * @param string $part Part to extract.
	 * @return string
	 */
	private function get_url_part( string $url, string $part = 'path' ): string {
		$parsed = wp_parse_url( $url );

		return isset( $parsed[ $part ] ) ? (string) $parsed[ $part ] : '';
	}

	/**
	 * Get list of disabled pages.
	 *
	 * @return array
	 */
	private function get_disabled_pages(): array {
		$pages = get_option( 'codesoup_ilc_cache_disabled', wp_json_encode( array() ) );

		return is_string( $pages ) ? json_decode( $pages, true ) : array();
	}

	/**
	 * Check if page caching is disabled.
	 *
	 * @param int $page_id Page ID.
	 * @return bool
	 */
	private function is_page_caching_disabled( int $page_id ): bool {
		return array_search( $page_id, $this->get_disabled_pages(), true ) !== false;
	}
}

