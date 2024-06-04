<?php

namespace CodeSoup\InstapageCache\Admin;

// Exit if accessed directly
defined( 'WPINC' ) || die;


/**
 * @file
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Init {

	use \CodeSoup\InstapageCache\Traits\HelpersTrait;

	// Main plugin instance.
	protected static $instance = null;

	
	// Assets loader class.
	protected $assets;

	protected $screen;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// Main plugin instance.
		$instance     = \CodeSoup\InstapageCache\Init::get_instance();
		$hooker       = $instance->get_hooker();
		$this->assets = $instance->get_assets();
		$this->screen = empty($_GET['page']) ? null : sanitize_title( $_GET['page'] );


		// Admin hooks.
		$hooker->add_actions([
			['admin_enqueue_scripts', $this],
			['rest_api_init', $this],
		]);

		new Settings_Page;
	}

	/**
	 * Register the CSS/JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function admin_enqueue_scripts() {

		if ( 'instapage-cache' !== $this->screen )
			return;

		wp_enqueue_style(
			$this->get_plugin_id('/wp/css'),
			$this->assets->get('admin.css'),
			array(),
			$this->get_plugin_version(),
			'all'
		);

		$script_id = $this->get_plugin_id('/wp/js');

		wp_enqueue_script(
			$script_id,
			$this->assets->get('admin.js'),
			array(),
			$this->get_plugin_version(),
			false
		);

		wp_localize_script(
            $script_id,
            'codesoup_ilc',
            array(
                'root'     => get_rest_url(),
                'nonce'    => wp_create_nonce( 'wp_rest' ),
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'post_id'  => get_the_ID(),
            )
        );
	}


	/**
	 * REST API Endpoint
	 */
	public function rest_api_init()
	{
        $rest = new APIController();
        $rest->register_routes();
    }


    /**
	 * - Generate user roles and capabilities
	 * - Add custom caps to admin
	 */
	public static function capabilities_setup() {

		// Role for Compliance Manager
        $caps = array(
            'manage_instapage',
        );

        add_role('instapage_manager', 'Instapage Manager', $caps);

        // Grant Compliance Manager capabilities to Administrator
        $admin_role = get_role('administrator');

        if ($admin_role)
        {
            foreach ($caps as $cap) {
                $admin_role->add_cap($cap);
            }
        }
	}
}
