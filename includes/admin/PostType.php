<?php
/**
 * Post Type.
 *
 * @package CodeSoup\InstapageCache
 */

namespace CodeSoup\InstapageCache\Admin;

use CodeSoup\InstapageCache\Traits\HelpersTrait;
use function CodeSoup\InstapageCache\plugin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * PostType class.
 */
class PostType {

	use HelpersTrait;

	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	public const POST_TYPE = 'cs_instapage_cache';

	/**
	 * Taxonomy slug.
	 *
	 * @var string
	 */
	public const TAXONOMY = 'cs_instapage_cache_cat';

	/**
	 * Initialize hooks.
	 */
	public function init_menu_hooks(): void {
		$hooker = plugin()->get( 'hooker' );

		$hooker->add_filters([
			array( 'admin_menu', $this, 'register_menu_page', 20 ),
			array( 'parent_file', $this, 'set_parent_menu_for_taxonomy' ),
			array( 'submenu_file', $this, 'set_submenu_file_for_taxonomy' ),
			array( 'months_dropdown_results', '__return_empty_array' ),
			array( 'manage_' . self::POST_TYPE . '_posts_columns', $this, 'manage_columns' ),
			array( 'manage_' . self::POST_TYPE . '_posts_custom_column', $this, 'render_caching_column', 10, 2 ),
			// array( 'quick_edit_enabled_for_post_type', '__return_false' ),
		]);

		$hooker->add_actions([
			array( 'restrict_manage_posts', $this, 'add_taxonomy_filter' ),
			array( 'wp_ajax_toggle_post_cache', $this, 'handle_toggle_cache_ajax' ),
		]);
	}

	/**
	 * Register the custom post type.
	 */
	public function register(): void {
		$this->register_post_type();
		$this->register_taxonomy();
	}

	/**
	 * Register the settings menu page.
	 */
	public function register_menu_page(): void {
		add_submenu_page(
			'instapage_dashboard',
			__( 'Categories', 'instapage-cache' ),
			__( 'Categories', 'instapage-cache' ),
			'manage_instapage_cache',
			'edit-tags.php?taxonomy=' . self::TAXONOMY . '&post_type=' . self::POST_TYPE,
			''
		);
	}


	public function manage_columns( $columns ) {
		unset( $columns['date'] );
		$columns['caching'] = __( 'Caching', 'instapage-cache' );

		return $columns;
	}

	/**
	 * Render caching column content.
	 *
	 * @param string $column_name Column name.
	 * @param int    $post_id Post ID.
	 */
	public function render_caching_column( string $column_name, int $post_id ): void {
		if ( 'caching' !== $column_name ) {
			return;
		}

		$is_enabled = '1' === get_post_meta( $post_id, 'instapage_cache_enabled', true );
		$nonce = wp_create_nonce( 'toggle_post_cache_' . $post_id );

		$button_class = $is_enabled ? 'button-primary' : 'button-secondary';
		$button_text = $is_enabled ? __( 'Disable', 'instapage-cache' ) : __( 'Enable', 'instapage-cache' );
		$status_text = $is_enabled ? __( 'Active', 'instapage-cache' ) : __( 'Inactive', 'instapage-cache' );
		$new_status = $is_enabled ? '0' : '1';

		require plugin()->config['PLUGIN_BASE_PATH'] . 'templates/admin/caching-column.php';
	}

	/**
	 * Handle cache toggle AJAX request.
	 */
	public function handle_toggle_cache_ajax(): void {
		$post_id = intval( $_POST['post_id'] ?? 0 );
		check_ajax_referer( 'toggle_post_cache_' . $post_id );

		if ( ! current_user_can( 'manage_instapage_cache' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'instapage-cache' ) ) );
		}

		$enable = intval( $_POST['enable'] ?? 1 );

		if ( ! $post_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid post ID', 'instapage-cache' ) ) );
		}

		$sync_service = new \CodeSoup\InstapageCache\Services\SyncService();
		$result = $sync_service->toggle_cache_status( $post_id, (bool) $enable );

		if ( $result ) {
			$new_status = $enable ? __( 'Enabled', 'instapage-cache' ) : __( 'Disabled', 'instapage-cache' );
			wp_send_json_success(
				array(
					'status' => $new_status,
					'enabled' => (bool) $enable,
				)
			);
		} else {
			wp_send_json_error( array( 'message' => __( 'Failed to toggle cache status', 'instapage-cache' ) ) );
		}
	}



	/**
	 * Register the custom post type.
	 */
	private function register_post_type(): void {
		$labels = array(
			'name'                  => _x( 'Instapage Cache', 'Post type general name', 'instapage-cache' ),
			'singular_name'         => _x( 'Instapage Cache', 'Post type singular name', 'instapage-cache' ),
			'menu_name'             => _x( 'Instapage Cache', 'Admin Menu text', 'instapage-cache' ),
			'name_admin_bar'        => _x( 'Instapage Cache', 'Add New on Toolbar', 'instapage-cache' ),
			'add_new'               => __( 'Add New', 'instapage-cache' ),
			'add_new_item'          => __( 'Add New Instapage Cache', 'instapage-cache' ),
			'new_item'              => __( 'New Instapage Cache', 'instapage-cache' ),
			'edit_item'             => __( 'Edit Instapage Cache', 'instapage-cache' ),
			'view_item'             => __( 'View Instapage Cache', 'instapage-cache' ),
			'all_items'             => __( 'Page Cache', 'instapage-cache' ),
			'search_items'          => __( 'Search Instapage Cache', 'instapage-cache' ),
			'parent_item_colon'     => __( 'Parent Instapage Cache:', 'instapage-cache' ),
			'not_found'             => __( 'No instapage cache found.', 'instapage-cache' ),
			'not_found_in_trash'    => __( 'No instapage cache found in Trash.', 'instapage-cache' ),
			'featured_image'        => _x( 'Instapage Cache Cover Image', 'Overrides the "Featured Image" phrase for this post type. Added in 4.3', 'instapage-cache' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the "Set featured image" phrase for this post type. Added in 4.3', 'instapage-cache' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the "Remove featured image" phrase for this post type. Added in 4.3', 'instapage-cache' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the "Use as featured image" phrase for this post type. Added in 4.3', 'instapage-cache' ),
			'archives'              => _x( 'Instapage Cache archives', 'The post type archive label used in nav menus. Default "Post Archives". Added in 4.4', 'instapage-cache' ),
			'insert_into_item'      => _x( 'Insert into instapage cache', 'Overrides the "Insert into post"/"Insert into page" phrase (used when inserting media into a post). Added in 4.4', 'instapage-cache' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this instapage cache', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase (used when viewing media attached to a post). Added in 4.4', 'instapage-cache' ),
			'filter_items_list'     => _x( 'Filter instapage cache list', 'Screen reader text for the filter links heading on the post type listing screen. Default "Filter posts list"/"Filter pages list". Added in 4.4', 'instapage-cache' ),
			'items_list_navigation' => _x( 'Instapage cache list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default "Posts list navigation"/"Pages list navigation". Added in 4.4', 'instapage-cache' ),
			'items_list'            => _x( 'Instapage cache list', 'Screen reader text for the items list heading on the post type listing screen. Default "Posts list"/"Pages list". Added in 4.4', 'instapage-cache' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'instapage_dashboard',
			'query_var'          => true,
			'rewrite'            => false,
			'capability_type'    => 'instapage_cache',
			'capabilities' => array(
				'create_instapage_cache' => false,
				'create_instapage_caches' => false,
			),
			'map_meta_cap'       => false,
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'menu_icon'          => 'dashicons-performance',
			'supports'           => array('title'),
			'show_in_rest'       => false,
		);

		register_post_type( self::POST_TYPE, $args );

		/**
		 * Hides 'Add new' button
		 */
		global $wp_post_types;

    	$wp_post_types['cs_instapage_cache']->cap->create_posts = '';
		$wp_post_types['cs_instapage_cache']->cap->delete_posts = '';
	}

	/**
	 * Register the custom taxonomy.
	 */
	private function register_taxonomy(): void {
		$labels = array(
			'name'                       => _x( 'Categories', 'Taxonomy General Name', 'instapage-cache' ),
			'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'instapage-cache' ),
			'menu_name'                  => __( 'Categories', 'instapage-cache' ),
			'all_items'                  => __( 'All Categories', 'instapage-cache' ),
			'parent_item'                => __( 'Parent Category', 'instapage-cache' ),
			'parent_item_colon'          => __( 'Parent Category:', 'instapage-cache' ),
			'new_item_name'              => __( 'New Category Name', 'instapage-cache' ),
			'add_new_item'               => __( 'Add New Category', 'instapage-cache' ),
			'edit_item'                  => __( 'Edit Category', 'instapage-cache' ),
			'update_item'                => __( 'Update Category', 'instapage-cache' ),
			'view_item'                  => __( 'View Category', 'instapage-cache' ),
			'separate_items_with_commas' => __( 'Separate categories with commas', 'instapage-cache' ),
			'add_or_remove_items'        => __( 'Add or remove categories', 'instapage-cache' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'instapage-cache' ),
			'popular_items'              => __( 'Popular Categories', 'instapage-cache' ),
			'search_items'               => __( 'Search Categories', 'instapage-cache' ),
			'not_found'                  => __( 'Not Found', 'instapage-cache' ),
			'no_terms'                   => __( 'No categories', 'instapage-cache' ),
			'items_list'                 => __( 'Categories list', 'instapage-cache' ),
			'items_list_navigation'      => __( 'Categories list navigation', 'instapage-cache' ),
		);

		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => false,
			'show_ui'                    => true,
			'show_in_menu'               => 'instapage_dashboard',
			'show_admin_column'          => true,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
			'show_in_rest'               => false,
			'capabilities'               => array(
				'manage_terms' => 'manage_instapage_cache',
				'edit_terms'   => 'manage_instapage_cache',
				'delete_terms' => 'manage_instapage_cache',
				'assign_terms' => 'edit_instapage_cache',
			),
		);

		register_taxonomy( self::TAXONOMY, array( self::POST_TYPE ), $args );
	}

	/**
	 * Set parent menu for taxonomy pages.
	 *
	 * @param string $parent_file The parent file.
	 * @return string
	 */
	public function set_parent_menu_for_taxonomy( string $parent_file ): string {
		global $pagenow;

		if ( strpos( $parent_file, 'cs_instapage_cache_cat' ) !== false || strpos( $parent_file, 'cs_instapage_cache' ) !== false ) {
			$parent_file = 'instapage_dashboard';
		}

		return $parent_file;
	}

	/**
	 * Set submenu file for taxonomy pages.
	 *
	 * @param string $submenu_file The submenu file.
	 * @return string|null
	 */
	public function set_submenu_file_for_taxonomy( string|null $submenu_file ): string|null {
		global $pagenow;

		if ( empty ($submenu_file) )
			return $submenu_file; 

		// Mark category active when editing single term
		if (
			! empty( $_GET['tag_ID'] )
			&& ( strpos( $submenu_file, 'cs_instapage_cache_cat' ) !== false || strpos( $submenu_file, 'cs_instapage_cache' ) !== false )
			) {
			$submenu_file = 'edit-tags.php?taxonomy=cs_instapage_cache_cat&post_type=cs_instapage_cache';
		}

		return $submenu_file;
	}

	/**
	 * Add custom capabilities to administrator role.
	 */
	public function add_capabilities(): void {
		$role = get_role( 'administrator' );

		if ( $role ) {
			// Post type capabilities
			$role->add_cap( 'edit_instapage_cache' );
			$role->add_cap( 'read_instapage_cache' );
			// $role->add_cap( 'delete_instapage_cache' );
			$role->add_cap( 'edit_instapage_caches' );
			$role->add_cap( 'edit_others_instapage_caches' );
			$role->add_cap( 'publish_instapage_caches' );
			$role->add_cap( 'read_private_instapage_caches' );
			// $role->add_cap( 'delete_instapage_caches' );
			// $role->add_cap( 'delete_private_instapage_caches' );
			// $role->add_cap( 'delete_published_instapage_caches' );
			// $role->add_cap( 'delete_others_instapage_caches' );
			$role->add_cap( 'edit_private_instapage_caches' );
			$role->add_cap( 'edit_published_instapage_caches' );

			// Taxonomy capabilities
			$role->add_cap( 'manage_instapage_cache' );
		}
	}


	/**
	 * Add taxonomy filter dropdown.
	 */
	public function add_taxonomy_filter(): void {
		global $pagenow;

		$screen = get_current_screen();

		if ( ! $screen || 'edit' !== $screen->base || self::POST_TYPE !== $screen->post_type ) {
			return;
		}

		$selected = isset( $_GET[ self::TAXONOMY ] )
			? sanitize_text_field( wp_unslash( $_GET[ self::TAXONOMY ] ) )
			: '';

		wp_dropdown_categories(
			array(
				'show_option_all' => __( 'All Categories', 'instapage-cache' ),
				'taxonomy'        => self::TAXONOMY,
				'name'            => self::TAXONOMY,
				'orderby'         => 'name',
				'selected'        => $selected,
				'hierarchical'    => true,
				'depth'           => 3,
				'show_count'      => true,
				'hide_empty'      => false,
				'value_field'     => 'slug',
			)
		);
	}
}
