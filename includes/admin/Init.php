<?php
/**
 * Admin Init class.
 *
 * @package CodeSoup\InstapageCache
 */

declare( strict_types=1 );

namespace CodeSoup\InstapageCache\Admin;

use CodeSoup\InstapageCache\Services\SyncService;
use function CodeSoup\InstapageCache\plugin;

/** If this file is called directly, abort. */
defined( 'ABSPATH' ) || die;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Init {

	/**
	 * Post type instance.
	 *
	 * @var PostType
	 */
	private PostType $post_type;

	/**
	 * Sync service instance.
	 *
	 * @var SyncService
	 */
	private SyncService $sync_service;

	/**
	 * Taxonomy meta instance.
	 *
	 * @var TaxonomyMeta
	 */
	private TaxonomyMeta $taxonomy_meta;

	/**
	 * Init constructor.
	 */
	public function __construct() {
		$this->post_type     = new PostType();
		$this->sync_service  = new SyncService();
		$this->taxonomy_meta = new TaxonomyMeta();
		$this->add_hooks();
	}

	/**
	 * Add the admin hooks.
	 */
	private function add_hooks(): void {
		$hooker = plugin()->get( 'hooker' );

		// Admin-specific hooks
		$hooker->add_actions(
			array(
				array( 'admin_enqueue_scripts', $this ),
			)
		);

		// Post type hooks (always register, needed on frontend too)
		$hooker->add_actions(
			array(
				array( 'init', $this->post_type, 'register' ),
				array( 'init', $this->post_type, 'add_capabilities' ),
				array( 'admin_menu', $this->post_type, 'init_menu_hooks', 5 ),
			)
		);

		// Sync service hooks
		$hooker->add_actions(
			array(
				// Schedule cron
				array( 'init', $this, 'schedule_sync_cron' ),
				// Cron hook
				array( 'instapage_cache_sync_cron', $this->sync_service, 'full_sync' ),
				// Admin actions
				array( 'admin_post_instapage_cache_manual_sync', $this, 'handle_manual_sync' ),
				array( 'admin_post_instapage_cache_toggle', $this, 'handle_cache_toggle' ),
				array( 'admin_post_instapage_cache_clear', $this, 'handle_cache_clear' ),
			)
		);
	}

	/**
	 * Enqueue the admin styles.
	 */
	public function admin_enqueue_scripts(): void {

		$assets_handler = plugin()->get( 'assets' );
		$plugin_version = plugin()->config['PLUGIN_VERSION'];

		// Enqueue the main admin stylesheet.
		wp_enqueue_style(
			'codesoup_ilc-admin',
			$assets_handler->get_asset_url( 'admin-common.css' ),
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
			'codesoup_ilc-admin-common',
			$assets_handler->get_asset_url( 'admin-common.js' ),
			array( 'codesoup_ilc-runtime', 'codesoup_ilc-vendor' ),
			$plugin_version,
			true
		);
	}

	/**
	 * Schedule sync cron job.
	 */
	public function schedule_sync_cron(): void {
		if ( ! wp_next_scheduled( 'instapage_cache_sync_cron' ) ) {
			wp_schedule_event( time(), 'hourly', 'instapage_cache_sync_cron' );
		}
	}

	/**
	 * Handle manual sync admin action.
	 */
	public function handle_manual_sync(): void {
		if ( ! current_user_can( 'manage_instapage_cache' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'instapage-cache' ) );
		}

		check_admin_referer( 'instapage_cache_manual_sync' );

		$result = $this->sync_service->full_sync();

		$redirect_url = add_query_arg(
			array(
				'page'    => 'instapage-cache',
				'synced'  => $result['success'] ? '1' : '0',
				'message' => urlencode( $result['message'] ),
			),
			admin_url( 'admin.php' )
		);

		wp_redirect( $redirect_url );
		exit;
	}

	/**
	 * Handle cache toggle admin action.
	 */
	public function handle_cache_toggle(): void {
		if ( ! current_user_can( 'edit_instapage_cache' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'instapage-cache' ) );
		}

		check_admin_referer( 'instapage_cache_toggle' );

		$post_id = intval( $_GET['post_id'] ?? 0 );
		$enable  = intval( $_GET['enable'] ?? 1 );

		if ( ! $post_id ) {
			wp_die( esc_html__( 'Invalid post ID.', 'instapage-cache' ) );
		}

		$result = $this->sync_service->toggle_cache_status( $post_id, (bool) $enable );

		$redirect_url = add_query_arg(
			array(
				'page'    => 'instapage-cache',
				'toggled' => $result ? '1' : '0',
			),
			admin_url( 'admin.php' )
		);

		wp_redirect( $redirect_url );
		exit;
	}

	/**
	 * Handle cache clear admin action.
	 */
	public function handle_cache_clear(): void {
		if ( ! current_user_can( 'delete_instapage_cache' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'instapage-cache' ) );
		}

		check_admin_referer( 'instapage_cache_clear' );

		$post_id = intval( $_GET['post_id'] ?? 0 );

		if ( ! $post_id ) {
			wp_die( esc_html__( 'Invalid post ID.', 'instapage-cache' ) );
		}

		$result = $this->sync_service->delete_cache_files_for_post( $post_id );

		// Update cache status
		$this->sync_service->update_cache_file_status( $post_id );

		$redirect_url = add_query_arg(
			array(
				'page'    => 'instapage-cache',
				'cleared' => $result ? '1' : '0',
			),
			admin_url( 'admin.php' )
		);

		wp_redirect( $redirect_url );
		exit;
	}
}
