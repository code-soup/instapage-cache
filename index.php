<?php

defined('WPINC') || die;

/**
 * Plugin Name: Instapage Cache
 * Description: Boost performance and reduce server load with Instapage Cache. This add-on for Instapage lets you easily manage and clear cache to speed up page load time.
 * Version: 0.0.2
 * Requires Plugins: instapage
 * Requires at least: 6.0
 * Requires PHP: 8.2
 * Author: Code Soup
 * Author URI: https://www.codesoup.co
 * License: GPL-3.0+
 * Stable Tag: trunk
 * Text Domain: instapage-cache
 */

register_activation_hook( __FILE__, function() {

    // On activate do this
    \CodeSoup\InstapageCache\Activator::activate();
});

register_deactivation_hook( __FILE__, function () {
    
    // On deactivate do that
    \CodeSoup\InstapageCache\Deactivator::deactivate();
});

include "run.php";