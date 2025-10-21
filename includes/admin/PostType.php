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
		$hooker->add_filter( 'parent_file', $this, 'set_parent_menu_for_taxonomy' );
		$hooker->add_filter( 'submenu_file', $this, 'set_submenu_file_for_taxonomy' );
		$hooker->add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', $this, 'add_taxonomy_column' );
		$hooker->add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', $this, 'render_taxonomy_column', 10, 2 );
		$hooker->add_action( 'restrict_manage_posts', $this, 'add_taxonomy_filter' );
		$hooker->add_action( 'bulk_edit_custom_box', $this, 'render_bulk_edit_fields', 10, 2 );
		$hooker->add_action( 'save_post_' . self::POST_TYPE, $this, 'save_bulk_edit_fields' );
	}

	/**
	 * Register the custom post type.
	 */
	public function register(): void {
		$this->register_post_type();
		$this->register_taxonomy();
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
			'show_in_menu'       => 'instapage_dashboard', // Integrate with Instapage menu
			'query_var'          => true,
			'rewrite'            => false,
			'capability_type'    => 'instapage_cache',
			'map_meta_cap'       => false,
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'menu_icon'          => 'dashicons-performance',
			'supports'           => array( 'title' ),
			'show_in_rest'       => false,
		);

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Register the custom taxonomy.
	 */
	private function register_taxonomy(): void {
		$labels = array(
			'name'                       => _x( 'Cache Categories', 'Taxonomy General Name', 'instapage-cache' ),
			'singular_name'              => _x( 'Cache Category', 'Taxonomy Singular Name', 'instapage-cache' ),
			'menu_name'                  => __( 'Cache Categories', 'instapage-cache' ),
			'all_items'                  => __( 'All Cache Categories', 'instapage-cache' ),
			'parent_item'                => __( 'Parent Cache Category', 'instapage-cache' ),
			'parent_item_colon'          => __( 'Parent Cache Category:', 'instapage-cache' ),
			'new_item_name'              => __( 'New Cache Category Name', 'instapage-cache' ),
			'add_new_item'               => __( 'Add New Cache Category', 'instapage-cache' ),
			'edit_item'                  => __( 'Edit Cache Category', 'instapage-cache' ),
			'update_item'                => __( 'Update Cache Category', 'instapage-cache' ),
			'view_item'                  => __( 'View Cache Category', 'instapage-cache' ),
			'separate_items_with_commas' => __( 'Separate cache categories with commas', 'instapage-cache' ),
			'add_or_remove_items'        => __( 'Add or remove cache categories', 'instapage-cache' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'instapage-cache' ),
			'popular_items'              => __( 'Popular Cache Categories', 'instapage-cache' ),
			'search_items'               => __( 'Search Cache Categories', 'instapage-cache' ),
			'not_found'                  => __( 'Not Found', 'instapage-cache' ),
			'no_terms'                   => __( 'No cache categories', 'instapage-cache' ),
			'items_list'                 => __( 'Cache categories list', 'instapage-cache' ),
			'items_list_navigation'      => __( 'Cache categories list navigation', 'instapage-cache' ),
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

		if ( 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && self::TAXONOMY === sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) ) {
			$parent_file = 'instapage_dashboard';
		}

		return $parent_file;
	}

	/**
	 * Set submenu file for taxonomy pages.
	 *
	 * @param string $submenu_file The submenu file.
	 * @return string
	 */
	public function set_submenu_file_for_taxonomy( string $submenu_file ): string {
		global $pagenow;

		if ( 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && self::TAXONOMY === sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) ) {
			$submenu_file = 'edit-tags.php?taxonomy=' . self::TAXONOMY . '&post_type=' . self::POST_TYPE;
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
			$role->add_cap( 'delete_instapage_cache' );
			$role->add_cap( 'edit_instapage_caches' );
			$role->add_cap( 'edit_others_instapage_caches' );
			$role->add_cap( 'publish_instapage_caches' );
			$role->add_cap( 'read_private_instapage_caches' );
			$role->add_cap( 'delete_instapage_caches' );
			$role->add_cap( 'delete_private_instapage_caches' );
			$role->add_cap( 'delete_published_instapage_caches' );
			$role->add_cap( 'delete_others_instapage_caches' );
			$role->add_cap( 'edit_private_instapage_caches' );
			$role->add_cap( 'edit_published_instapage_caches' );

			// Taxonomy capabilities
			$role->add_cap( 'manage_instapage_cache' );
		}
	}

	/**
	 * Add taxonomy column to post list.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public function add_taxonomy_column( array $columns ): array {
		$new_columns = array();

		foreach ( $columns as $key => $value ) {
			$new_columns[ $key ] = $value;
			if ( 'title' === $key ) {
				$new_columns[ self::TAXONOMY ] = __( 'Category', 'instapage-cache' );
			}
		}

		return $new_columns;
	}

	/**
	 * Render taxonomy column.
	 *
	 * @param string $column Column name.
	 * @param int    $post_id Post ID.
	 */
	public function render_taxonomy_column( string $column, int $post_id ): void {
		if ( self::TAXONOMY !== $column ) {
			return;
		}

		$terms = get_the_terms( $post_id, self::TAXONOMY );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			echo '—';
			return;
		}

		$term_links = array_map(
			function ( $term ) {
				return sprintf(
					'<a href="%s">%s</a>',
					esc_url( add_query_arg( self::TAXONOMY, $term->slug, admin_url( 'edit.php?post_type=' . self::POST_TYPE ) ) ),
					esc_html( $term->name )
				);
			},
			$terms
		);

		echo wp_kses_post( implode( ', ', $term_links ) );
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

		$selected = isset( $_GET[ self::TAXONOMY ] ) ? sanitize_text_field( wp_unslash( $_GET[ self::TAXONOMY ] ) ) : '';

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

	/**
	 * Render bulk edit fields.
	 *
	 * @param string $column_name Column name.
	 * @param string $post_type Post type.
	 */
	public function render_bulk_edit_fields( string $column_name, string $post_type ): void {
		if ( self::POST_TYPE !== $post_type || self::TAXONOMY !== $column_name ) {
			return;
		}

		?>
		<fieldset class="inline-edit-col-left">
			<div class="inline-edit-col">
				<label>
					<span class="title"><?php esc_html_e( 'Category', 'instapage-cache' ); ?></span>
					<span class="input-text-wrap">
						<?php
						wp_dropdown_categories(
							array(
								'show_option_all' => __( '— No Change —', 'instapage-cache' ),
								'taxonomy'        => self::TAXONOMY,
								'name'            => 'bulk_' . self::TAXONOMY,
								'orderby'         => 'name',
								'hierarchical'    => true,
								'depth'           => 3,
								'show_count'      => false,
								'hide_empty'      => false,
								'value_field'     => 'term_id',
							)
						);
						?>
					</span>
				</label>
			</div>
		</fieldset>
		<?php
	}

	/**
	 * Save bulk edit fields.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_bulk_edit_fields( int $post_id ): void {
		if ( ! isset( $_POST[ 'bulk_' . self::TAXONOMY ] ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_instapage_cache' ) ) {
			return;
		}

		$term_id = intval( $_POST[ 'bulk_' . self::TAXONOMY ] );

		if ( $term_id > 0 ) {
			wp_set_post_terms( $post_id, array( $term_id ), self::TAXONOMY );
		}
	}
}
