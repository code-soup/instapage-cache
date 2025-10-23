<?php
/**
 * Sync Service for syncing instapage_pages table with custom posts.
 *
 * @package CodeSoup\InstapageCache
 */

namespace CodeSoup\InstapageCache\Services;

use CodeSoup\InstapageCache\Admin\PostType;
use CodeSoup\InstapageCache\Traits\HelpersTrait;
use CodeSoup\InstapageCache\Traits\LoggingTrait;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * SyncService class.
 */
class SyncService {

	use HelpersTrait;
	use LoggingTrait;

	/**
	 * Cache base directory.
	 *
	 * @var string
	 */
	private string $cache_base_dir;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->cache_base_dir = WP_CONTENT_DIR . '/instapage-cache';
	}

	/**
	 * Perform full sync from instapage_pages table to custom posts.
	 *
	 * @return array Sync results.
	 */
	public function full_sync(): array {
		$this->log( 'Starting full sync from instapage_pages table to custom posts' );

		global $wpdb;

		// Get all instapage pages
		$instapage_pages = $wpdb->get_results(
			"SELECT * FROM {$wpdb->prefix}instapage_pages",
			ARRAY_A
		);

		if ( empty( $instapage_pages ) ) {
			$this->log( 'No instapage pages found in database' );
			return array(
				'success' => true,
				'created' => 0,
				'updated' => 0,
				'deleted' => 0,
				'message' => 'No instapage pages found in database',
			);
		}

		$created = 0;
		$updated = 0;
		$errors  = array();

		foreach ( $instapage_pages as $page ) {
			try {
				$existing_post = $this->find_cache_post_by_instapage_id( $page['id'] );

				if ( $existing_post ) {
					$this->update_cache_post( $existing_post->ID, $page );
					$updated++;
					$this->log( "Updated cache post for instapage ID: {$page['id']}" );
				} else {
					$post_id = $this->create_cache_post( $page );
					if ( $post_id ) {
						$created++;
						$this->log( "Created cache post for instapage ID: {$page['id']}" );
					}
				}
			} catch ( \Exception $e ) {
				$errors[] = "Error syncing page ID {$page['id']}: " . $e->getMessage();
				$this->log( "Error syncing page ID {$page['id']}: " . $e->getMessage(), 'error' );
			}
		}

		// Clean up orphaned posts
		$deleted = $this->cleanup_orphaned_posts( $instapage_pages );

		$this->log( "Full sync completed. Created: {$created}, Updated: {$updated}, Deleted: {$deleted}" );

		return array(
			'success' => true,
			'created' => $created,
			'updated' => $updated,
			'deleted' => $deleted,
			'errors'  => $errors,
			'message' => "Sync completed. Created: {$created}, Updated: {$updated}, Deleted: {$deleted}",
		);
	}

	/**
	 * Create cache post from instapage data.
	 *
	 * @param array $instapage_data Instapage data from database.
	 * @return int|false Post ID on success, false on failure.
	 */
	private function create_cache_post( array $instapage_data ) {
		$post_title = ! empty( $instapage_data['title'] ) ? $instapage_data['title'] : $instapage_data['slug'];
		$post_slug  = sanitize_title( $instapage_data['slug'] );

		$post_data = array(
			'post_type'   => PostType::POST_TYPE,
			'post_title'  => sanitize_text_field( $post_title ),
			'post_name'   => $post_slug,
			'post_status' => 'publish', // Default: caching enabled
			'meta_input'  => array(
				'_instapage_id'        => intval( $instapage_data['id'] ),
				'_instapage_slug'      => sanitize_text_field( $instapage_data['slug'] ),
				'_cache_file_exists'   => $this->check_cache_file_exists( $instapage_data['slug'] ),
				'_cache_created_at'    => current_time( 'mysql' ),
				'_cache_hit_count'     => 0,
				'_cache_last_checked'  => current_time( 'mysql' ),
			),
		);

		// Add cache file size if file exists
		if ( $this->check_cache_file_exists( $instapage_data['slug'] ) ) {
			$file_path = $this->get_cache_file_path( $instapage_data['slug'] );
			if ( file_exists( $file_path ) ) {
				$post_data['meta_input']['_cache_file_size'] = filesize( $file_path );
				$post_data['meta_input']['_cache_updated_at'] = date( 'Y-m-d H:i:s', filemtime( $file_path ) );
			}
		}

		$post_id = wp_insert_post( $post_data );

		if ( is_wp_error( $post_id ) ) {
			$this->log( "Failed to create post for instapage ID {$instapage_data['id']}: " . $post_id->get_error_message(), 'error' );
			return false;
		}

		return $post_id;
	}

	/**
	 * Update existing cache post with instapage data.
	 *
	 * @param int   $post_id        Post ID to update.
	 * @param array $instapage_data Instapage data from database.
	 * @return bool Success status.
	 */
	private function update_cache_post( int $post_id, array $instapage_data ): bool {
		$post_title = ! empty( $instapage_data['title'] ) ? $instapage_data['title'] : $instapage_data['slug'];
		$post_slug  = sanitize_title( $instapage_data['slug'] );

		$post_data = array(
			'ID'         => $post_id,
			'post_title' => sanitize_text_field( $post_title ),
			'post_name'  => $post_slug,
		);

		$result = wp_update_post( $post_data );

		if ( is_wp_error( $result ) ) {
			$this->log( "Failed to update post {$post_id}: " . $result->get_error_message(), 'error' );
			return false;
		}

		// Update meta data
		update_post_meta( $post_id, '_instapage_slug', sanitize_text_field( $instapage_data['slug'] ) );
		update_post_meta( $post_id, '_cache_file_exists', $this->check_cache_file_exists( $instapage_data['slug'] ) );
		update_post_meta( $post_id, '_cache_last_checked', current_time( 'mysql' ) );

		// Update cache file info if file exists
		if ( $this->check_cache_file_exists( $instapage_data['slug'] ) ) {
			$file_path = $this->get_cache_file_path( $instapage_data['slug'] );
			if ( file_exists( $file_path ) ) {
				update_post_meta( $post_id, '_cache_file_size', filesize( $file_path ) );
				update_post_meta( $post_id, '_cache_updated_at', date( 'Y-m-d H:i:s', filemtime( $file_path ) ) );
			}
		}

		return true;
	}

	/**
	 * Find cache post by original instapage ID.
	 *
	 * @param int $instapage_id Original instapage ID.
	 * @return \WP_Post|null Post object or null if not found.
	 */
	private function find_cache_post_by_instapage_id( int $instapage_id ): ?\WP_Post {
		$posts = get_posts(
			array(
				'post_type'      => PostType::POST_TYPE,
				'meta_key'       => '_instapage_id',
				'meta_value'     => $instapage_id,
				'posts_per_page' => 1,
				'post_status'    => 'any',
			)
		);

		return $posts ? $posts[0] : null;
	}

	/**
	 * Clean up orphaned posts that no longer exist in instapage_pages table.
	 *
	 * @param array $instapage_pages Current instapage pages from database.
	 * @return int Number of deleted posts.
	 */
	private function cleanup_orphaned_posts( array $instapage_pages ): int {
		$instapage_ids = array_column( $instapage_pages, 'id' );
		$deleted       = 0;

		// Get all cache posts
		$cache_posts = get_posts(
			array(
				'post_type'      => PostType::POST_TYPE,
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'meta_key'       => '_instapage_id',
			)
		);

		foreach ( $cache_posts as $post ) {
			$instapage_id = get_post_meta( $post->ID, '_instapage_id', true );

			if ( ! in_array( $instapage_id, $instapage_ids, true ) ) {
				// Delete cache files first
				$this->delete_cache_files_for_post( $post->ID );

				// Delete the post
				wp_delete_post( $post->ID, true );
				$deleted++;
				$this->log( "Deleted orphaned cache post for instapage ID: {$instapage_id}" );
			}
		}

		return $deleted;
	}

	/**
	 * Check if cache file exists for given slug.
	 *
	 * @param string $slug Instapage slug.
	 * @return bool True if cache file exists.
	 */
	private function check_cache_file_exists( string $slug ): bool {
		$file_path = $this->get_cache_file_path( $slug );
		return file_exists( $file_path );
	}

	/**
	 * Get cache file path for given slug.
	 *
	 * @param string $slug Instapage slug.
	 * @return string Cache file path.
	 */
	private function get_cache_file_path( string $slug ): string {
		return sprintf(
			'%s/%s/index.html',
			$this->cache_base_dir,
			trim( $slug, '/' )
		);
	}

	/**
	 * Delete cache files for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return bool Success status.
	 */
	private function delete_cache_files_for_post( int $post_id ): bool {
		$slug = get_post_meta( $post_id, '_instapage_slug', true );

		if ( empty( $slug ) ) {
			return false;
		}

		$cache_dir = sprintf(
			'%s/%s',
			$this->cache_base_dir,
			trim( $slug, '/' )
		);

		if ( is_dir( $cache_dir ) ) {
			$fs = new \WP_Filesystem_Direct( '' );
			return $fs->rmdir( $cache_dir, true );
		}

		return true;
	}

	/**
	 * Enable/Disable caching for specific post.
	 *
	 * @param int  $post_id Post ID.
	 * @param bool $enable  Whether to enable caching.
	 * @return bool Success status.
	 */
	public function toggle_cache_status( int $post_id, bool $enable = true ): bool {
		$result = update_post_meta( $post_id, 'instapage_cache_enabled', $enable ? '1' : '0' );

		if ( false === $result ) {
			$this->log( "Failed to toggle cache status for post {$post_id}", 'error' );
			return false;
		}

		// If disabling, delete cache files
		if ( ! $enable ) {
			$this->delete_cache_files_for_post( $post_id );
		}

		$this->log( "Cache status toggled for post {$post_id}: " . ( $enable ? 'enabled' : 'disabled' ) );

		return true;
	}

	/**
	 * Update cache file status for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return bool Success status.
	 */
	public function update_cache_file_status( int $post_id ): bool {
		$slug = get_post_meta( $post_id, '_instapage_slug', true );

		if ( empty( $slug ) ) {
			return false;
		}

		$cache_exists = $this->check_cache_file_exists( $slug );
		update_post_meta( $post_id, '_cache_file_exists', $cache_exists );
		update_post_meta( $post_id, '_cache_last_checked', current_time( 'mysql' ) );

		if ( $cache_exists ) {
			$file_path = $this->get_cache_file_path( $slug );
			if ( file_exists( $file_path ) ) {
				update_post_meta( $post_id, '_cache_file_size', filesize( $file_path ) );
				update_post_meta( $post_id, '_cache_updated_at', date( 'Y-m-d H:i:s', filemtime( $file_path ) ) );
			}
		}

		return true;
	}
}
