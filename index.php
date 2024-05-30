<?php

defined('WPINC') || die;

/**
 * Plugin Name: Instapage Local Cache
 * Description: Optimize Instapage with local caching. Easily manage and clear cache to boost performance and speed up page load times.
 * Version: 0.0.1
 * Requires at least: 6.0
 * Requires PHP: 8.2
 * Author: Code Soup
 * Author URI: https://www.codesoup.co
 * License: GPL-3.0+
 * Text Domain: codesoup-instapage-cache
 */

register_activation_hook( __FILE__, function() {

    // On activate do this
    \CodeSoup\Activator::activate();
});

register_deactivation_hook( __FILE__, function () {
    
    // On deactivate do that
    \CodeSoup\Deactivator::deactivate();
});

include "run.php";