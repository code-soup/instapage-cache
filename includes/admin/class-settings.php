<?php

namespace CodeSoup\InstapageCache\Admin;

// Exit if accessed directly
defined( 'WPINC' ) || die;


class Settings_Page {

    use \CodeSoup\InstapageCache\Traits\HelpersTrait;

    // Main plugin instance.
    protected static $instance = null;

    // Plugin options
    protected static $options = null;

    // Settings page tabs
    private $tabs;

    public function __construct() {

        // Main plugin instance.
        $instance     = \CodeSoup\InstapageCache\Init::get_instance();
        $hooker       = $instance->get_hooker();

        $hooker->add_actions([
        	['admin_menu', $this],
        	['admin_init', $this],
        ]);
    }

    
    /**
     * Register new page
     */
    public function admin_menu()
    {
        add_submenu_page(
	        'instapage_dashboard',
	        'Cache Settings',
	        'Caching',
	        'manage_options',
	        'instapage-cache',
	        array( &$this, 'render_settings_page'),
	    );
    }


    /**
     * Register settings sections and fields
     */
    public function admin_init()
    {
        $option_page   = 'codesoup_ilc_settigns_page';
        $option_name   = 'codesoup_ilc_settings';
        self::$options = get_option( $option_name );

        register_setting( $option_page, $option_name );

        /**
         * Tabs
         */
        $this->tabs = array(
            array(
                'tab_id'      => 'general',
                'tab_title'   => 'General',
                'option_page' => $option_page,
                'option_name' => $option_name,
            ),
            array(
                'tab_id'      => 'caching',
                'tab_title'   => 'Caching',
                'option_page' => $option_page,
                'option_name' => $option_name,
            ),
        );

        
        /**
         * Fields
         */
        foreach ( $this->tabs as $tab )
        {
            $fields = require_once "settings/fields/{$tab['tab_id']}.php";
            /**
             * Register section
             */
            add_settings_section(
                $tab['tab_id'],
                $tab['tab_title'],
                NULL,
                $tab['option_page'],
            );

            /**
             * Register fields to section
             */
            foreach ( $fields as $field )
            {
                $name           = str_replace( '-', '_', sanitize_title( $field['id'] ) );
                $field['name']  = $name;
                $field['value'] = self::get_option( $name, $tab['tab_id'] );

                add_settings_field(
                    $field['id'],
                    $field['label'],
                    [$this, 'render_field'],
                    $tab['option_page'],
                    $tab['tab_id'],
                    array(
                        'field'   => $field,
                        'option'  => $option_name,
                        'section' => $tab['tab_id'],
                    ),
                );
            }
        }
    }

    
    public function render_settings_page()
    {
        require 'settings/index.php';
    }

    
    public function render_field( $args )
    {
        require 'settings/form/index.php';
    }


    public static function get_option( $name = '', $section = 'general' )
    {
        // Empty
        $options = empty(self::$options)
            ? get_option( 'codesoup_ilc_settings' )
            : self::$options;


        if ( ! empty($section) && ! empty($name) )
        {
            return isset( $options[ $section ][ $name ] )
                ? $options[ $section ][ $name ]
                : NULL;
        }

        return $options;
    }
}