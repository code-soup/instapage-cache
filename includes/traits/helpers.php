<?php

namespace CodeSoup\InstapageCache\Traits;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * Helper methods
 */
trait HelpersTrait
{
    private function get_url_part($url, $part = 'path')
    {
        $parsed = wp_parse_url($url);

        return isset($parsed[ $part ])
            ? $parsed[ $part ]
            : '';
    }

    private function get_cache_dir_path( $slug ) {
        return sprintf(
            '%s/%s',
            $this->get_constant('CACHE_BASE_DIR'),
            $slug
        );
    }

    /**
     * Return absolute path to plugin dir
     * Always returns path without trailing slash
     *
     * @return [type] [description]
     */
    private function get_plugin_dir_path( string $path = '' ): string {

        // Force baseurl to be plugin root directory.
        $base = dirname( __FILE__, 3 );

        return $this->join_path($base, $path);
    }


    /**
     * Return plugin directory URL
     * Always returns URL without trailing slash
     *
     * @return [type] [description]
     */
    private function get_plugin_dir_url( string $path = '' ): string {

        // Force baseurl to be plugin root directory.
        $base = plugins_url( '/', dirname( __FILE__, 2 ) );

        return $this->join_path($base, $path, '/');
    }


    /**
     * Returns PLUGIN_NAME constant
     *
     * @return string
     */
    private function get_plugin_name(): string {

        return $this->get_constant( 'PLUGIN_NAME' );
    }


    /**
     * Returns PLUGIN_VERSION constant
     *
     * @return string
     */
    private function get_plugin_version(): string {

        return $this->get_constant( 'PLUGIN_VERSION' );
    }


    /**
     * Returns PLUGIN_PREFIX constant as ID
     * Converts to-slug-like-id
     * and appends additional text at the end for custom unique id
     *
     * @return string
     */
    private function get_plugin_id( string $append = '' ): string {

        $dashed = str_replace( '_', '-', $this->get_constant( 'PLUGIN_NAME' ) );

        return sanitize_title( $dashed ) . $append;
    }


    /**
     * Get plugin contstant by name
     *
     * @param  string $name [description]
     * @return [type]       [description]
     */
    private function get_constant( string $key ): string {

        $constants = \CodeSoup\InstapageCache\Init::$constants;
        $name      = trim( strtoupper( $key ) );

        // Check if constant is defined first
        if ( ! isset($constants[ $name ] ) ) {

            // Force string to avoid compiler errors
            $to_string = print_r( $name, true );

            // Log to error for debugging
            $this->log( "Invalid constant requested: $to_string" );

            // Exit
            return false;
        }

        // Return value by key
        return $constants[ $name ];
    }


    /**
     * Join two paths into single absolute path or URL
     * 
     * @param  string $base Base location
     * @param  string $path Path to append
     * @return string       Combined path
     */
    private function join_path( string $base = '', string $path = '', string $seperator = DIRECTORY_SEPARATOR ):string {

        // Strip slashes on both ends.
        if ( $path )
        {
            $path = rtrim($path, '/');
            $path = ltrim($path, '/');
        }

        // Strip trailingslash just in case.
        $base = untrailingslashit($base);
        $url  = array_filter(array($base,$path));
        $url  = implode($seperator,$url);

        return untrailingslashit($url);
    }


    /**
     * Save something to WordPress debug.log
     * Useful for debugging your code, this method will print_r any variable into log
     *
     * @param mixed $message
     */
    private function log( $variable ) {

        error_log( print_r( $variable, true ) );
    }


    private function debug( $variable ) {

        printf(
            '<pre>%s</pre>',
            print_r( esc_html( $variable ) )
        );
    }


    /**
     * Get plugin option
     * @param  [type] $name    [description]
     * @param  [type] $section [description]
     * @return [type]          [description]
     */
    private function get_option( $name, $section ) {
        return Settings_Page::get_option($name, $section);
    }


    /**
     * Get List of all disabled pages
     */
    private function get_disabled_pages()
    {
        return json_decode( get_option('codesoup_ilc_cache_disabled', wp_json_encode( array() )), true );
    }


    private function is_page_caching_disabled( int $page_id, $pages = array() )
    {
        return ( array_search($page_id, $this->get_disabled_pages()) !== false );
    }

    /**
     * Disable caching
     */
    private function disable_caching( int $page_id )
    {
        $pages   = $this->get_disabled_pages();
        $pages[] = $page_id;

        update_option( 'codesoup_ilc_cache_disabled', wp_json_encode( array_unique($pages) ), false );

        return $pages;
    }


    /**
     * Enable caching
     */
    private function enable_caching( int $page_id )
    {
        $pages = $this->get_disabled_pages();
        $key   = array_search($page_id, $pages);

        if ($key !== false) {
            unset($pages[$key]);
        }

        update_option( 'codesoup_ilc_cache_disabled', wp_json_encode( array_unique($pages) ), false );

        return $pages;
    }
}
