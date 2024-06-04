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
        	['admin_menu', $this, 'admin_menu', 100],
        	['admin_init', $this],
        ]);

        $hooker->add_filter( 'user_has_cap', $this, 'user_has_cap', 10, 4 );
    }


    /**
     * Bypass Instapage capability checks
     * This method includes 'manage_options' capability if caller is Instapage Class
     * This will allow u
     * @return [type]          [description]
     */
    public function user_has_cap( $allcaps, $caps, $args, $wp_user )
    {
        if ( in_array('manage_options', $caps) )
        {
            $trace = debug_backtrace();

            foreach( $trace as $call )
            {
                if ( ! isset($call['class']) )
                    continue;

                if ( $call['function'] === 'currentUserCanManage' )
                {
                    // $allcaps['manage_instapage'] = 1;
                    $allcaps['manage_options'] = 1;
                }
            }
        }        

        return $allcaps;
    }

    
    /**
     * Register new page
     */
    public function admin_menu()
    {
        /**
         * Instapage comes with manage_options capability which does not allow custom user roles
         * This is a fix
         */
        if ( class_exists('InstapageCmsPluginHelper') && class_exists('InstapageCmsPluginWPConnector') )
        {
            // Remove original Instapage Dashboard
            remove_menu_page('instapage_dashboard');

            // Callback
            $callback = '';

            // In case user already has manage_options dashboard will be loaded twice
            // This will prevent dashboard loading twice
            if ( ! current_user_can('manage_options') )
            {
                $instawp  = new \InstapageCmsPluginWPConnector();
                $callback = array( $instawp, 'loadPluginDashboard');
            }
            
            // Re-add Instapage Dashboard with updated capability
            add_menu_page(
                __('Instapage: General settings'),
                __('Instapage'),
                'manage_instapage',
                'instapage_dashboard',
                $callback,
                \InstapageCmsPluginHelper::getMenuIcon(),
                31
           );
        }

        add_submenu_page(
	        'instapage_dashboard',
	        'Cache Settings',
	        'Caching',
	        'manage_instapage',
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
                'tab_id'      => 'caching',
                'tab_title'   => 'Caching',
                'option_page' => $option_page,
                'option_name' => $option_name,
            ),
            array(
                'tab_id'      => 'general',
                'tab_title'   => 'General',
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