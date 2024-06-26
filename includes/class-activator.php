<?php

namespace CodeSoup\InstapageCache;

// Exit if accessed directly.
defined( 'WPINC' ) || die;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 */
class Activator {

    public static function activate() {
        // Put code that you want to run on activation in here.
        \CodeSoup\InstapageCache\Admin\Init::capabilities_setup();
    }
}
