<?php

namespace CodeSoup\InstapageCache\Admin;

// Exit if accessed directly
defined( 'WPINC' ) || die;

class APIController {

    use \CodeSoup\InstapageCache\Traits\HelpersTrait;

    public function __construct() {
        // Do something if required
    }

    /**
     * Register route for file uploads from remote repository
     */
    public function register_routes()
    {
        register_rest_route(
            'instapage-cache/v1',
            '/pages',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array($this, 'get_items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                ),
            )
        );

        register_rest_route(
            'instapage-cache/v1',
            '/delete',
            array(
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array($this, 'delete_item'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                ),
            )
        );
    }




    public function get_items( \WP_REST_Request $request )
    {
        // Query args
        $params   = $request->get_params();
        $paged    = empty($params['page']) ? 1 : $params['page'];
        $per_page = empty($params['per_page']) ? 15 : $params['per_page'];
        $offset   = intval( ($paged - 1) * $per_page );

        global $wpdb;

        // Base SQL for retrieving items
        $sql = ["SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}instapage_pages"];

        // Search string
        if ( ! empty($params['search']) )
        {
            $sql[] = $wpdb->prepare("WHERE slug LIKE %s", '%' . $wpdb->esc_like($params['search']) . '%');
        }

        // How many per page
        $sql[] = sprintf( "LIMIT %d", $per_page );
        $sql[] = sprintf( "OFFSET %d", $offset );
        
        // Run query for items
        $qry = $wpdb->get_results(implode(' ', $sql), ARRAY_A);


        /**
         * Return
         */
        $items = array();

        foreach( $qry as $row )
        {
            $file = sprintf(
                '%s/%s/index.html',
                $this->get_constant('CACHE_BASE_DIR'),
                trim($row['slug'], '/')
            );

            $new           = $row;
            $new['cached'] = intval( file_exists( $file ) );

            $items[] = $new;
        }

        // Get the total number of results
        $total = $wpdb->get_var("SELECT FOUND_ROWS()");

        return rest_ensure_response([
            'posts' => $items,
            'found' => intval($total),
            'pages' => ceil( $total / $per_page ),
        ]);
    }



    public function delete_item( \WP_REST_Request $request )
    {
        // Query args
        $del  = false;
        $slug = $request->get_param('slug');

        // Delete all cached files
        if ( 'all' === $slug )
        {
            $fs  = new \WP_Filesystem_Direct('');
            $del = $fs->rmdir( $this->get_constant('CACHE_BASE_DIR'), true );
        }
        // Delete specific slug
        else if ( ! empty($slug) )
        {
            $fs  = new \WP_Filesystem_Direct('');
            $del = $fs->rmdir( $this->get_cache_dir_path( $slug ), true );
        }
        
        return rest_ensure_response([
            'deleted' => $del,
        ]);
    }


    /**
     * Validate user permissions when trying to deploy from docker
     */
    public function get_items_permissions_check( \WP_REST_Request $request ) {

        return true;

        $wp_user = wp_get_current_user();

        if ( ! empty($wp_user) )
        {
            return $wp_user->has_cap('manage_snapshots');
        }

        return false;
    }


    /**
     * Get the query params for collections
     *
     * @return array
     */
    public function get_collection_params() {
        return array(
            'slug'   => array(
                'description'       => 'Page Slug',
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'page'     => array(
                'description'       => 'Current page in index.',
                'type'              => 'integer',
                'min'               => 1,
                'default'           => 1,
                'sanitize_callback' => 'absint',
            ),
            'per_page' => array(
                'description'       => 'Maximum number of items to be returned in result set.',
                'type'              => 'integer',
                'min'               => '1',
                'default'           => 20,
                'sanitize_callback' => 'absint',
            ),
            'search'   => array(
                'description'       => 'Limit results to those matching a string.',
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'status'   => array(
                'description'       => 'Page status.',
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        );
    }
}