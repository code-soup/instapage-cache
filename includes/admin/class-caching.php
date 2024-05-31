<?php

namespace CodeSoup\InstapageCache\Admin;

// Exit if accessed directly
defined( 'WPINC' ) || die;

class Caching
{
    use \CodeSoup\InstapageCache\Traits\HelpersTrait;

    /**
     * Instapage Host
     * This is where all requests were made
     */
    private $host = 'plugin.pageserve.co';

        
    /**
     * Path to cache directory for specific page
     */
    private $path;


    public function __construct()
    {
        // Caching not enabled
        if ( ! Settings_Page::get_option('instapage_cache_enabled') ) {
            return;
        }

        add_filter( 'pre_http_request', [$this, 'maybe_block_request'], 10, 3 );
        add_filter( 'http_response', [$this, 'cache_instapage_response'], 10, 3 );
        add_filter( 'pre_get_posts', [$this, 'maybe_serve_cached_response'] );
    }


    /**
     * Check if local cache exists
     * If cache exists block request
     *
     * @link https://developer.wordpress.org/reference/hooks/pre_http_request/
     */
    public function maybe_block_request($block_request, $parsed_args, $url)
    {
        $this->set_path( $url );

        // Is request to instapage
        if ( $this->is_instapage( $url ) ) 
        {
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
     * Cache Response
     *
     * @link https://developer.wordpress.org/reference/hooks/http_response/
     */
    public function cache_instapage_response($response, $parsed_args, $url)
    {
        // Is request to instapage
        if ( ! $this->is_instapage( $url ) ) {
            return $response;
        }

        // Update path
        $this->set_path( $url );

        // Save HTML response for next time
        if ( wp_mkdir_p( $this->get_cache_dir() ) )
        {
            file_put_contents($this->get_cache_file_path(), wp_remote_retrieve_body($response));
        }

        return $response;
    }


    /**
     * Maybe serve cached
     * @param  [type] $query [description]
     * @return [type]        [description]
     */
    public function maybe_serve_cached_response($query)
    {
        if (! $query->is_main_query() || is_admin() ) {
            return;
        }

        $this->set_path( $_SERVER['REQUEST_URI'] );

        if ( ! $this->is_instapage_published() || ! $this->is_page_cached() ) {
            return;
        }
        
        readfile( $this->get_cache_file_path() );
        echo '<!-- cached-response -->';

        exit;
    }


    /**
     * Set Instapage path
     */
    private function set_path( $url )
    {
        $this->path = trim($this->get_url_part( $url, 'path'), '/');
    }


    /**
     *
     * GETTERS
     * 
     */
    
    /**
     * Instapage Page ID in WP
     */
    private function get_page_id()
    {
        global $wpdb;

        return $wpdb->get_var(sprintf(
            "SELECT * FROM %s WHERE slug = '%s'",
            "{$wpdb->prefix}instapage_pages",
            trim($this->path, '/')
        ));
    }

    /**
     * Cache directory for specific page
     */
    private function get_cache_dir() {
        return sprintf(
            '%s/%s',
            $this->get_constant('CACHE_BASE_DIR'),
            $this->path
        );
    }


    private function get_cache_file_path() {
        return sprintf(
            '%s/%s/index.html',
            $this->get_constant('CACHE_BASE_DIR'),
            $this->path
        );
    }


    
    /**
     * 
     * CONDITIONALS
     * 
     */


    /**
     * Check if request is made to instapage host
     */
    private function is_instapage( string $url )
    {
        return $this->host === $this->get_url_part( $url, 'host');
    }


    /**
     * Verify Instapage is pubished in WP
     */
    private function is_instapage_published()
    {
        return ! empty( $this->get_page_id() );
    }


    /**
     * Verify if page is localy cached
     */
    private function is_page_cached()
    {
        return file_exists( $this->get_cache_file_path() );
    }
}