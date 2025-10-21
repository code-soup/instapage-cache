<?php

namespace CodeSoup\InstapageCache;

// Exit if accessed directly
defined( 'WPINC' ) || die;


/**
 * @file
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 */
class Deactivator {

    public static function deactivate() {
        /**
         * Disable Cron
         */
        \CodeSoup\InstapageCache\Admin\Init::cron_clear_cache('disable');
    }
}
