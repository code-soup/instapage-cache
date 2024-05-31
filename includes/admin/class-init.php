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
}
