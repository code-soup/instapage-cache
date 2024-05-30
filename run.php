<?php

namespace CodeSoup;

// If this file is called directly, abort.
defined('WPINC') || die;

// Autoload all classes via composer.
require "vendor/autoload.php";

/**
 * Make main plugin class available via global function call.
 *
 * @since    1.0.0
 */
function plugin_instance() {

    return \CodeSoup\Init::get_instance();
}

// Init plugin
$plugin = plugin_instance();
$plugin->set_constants([
    'MIN_WP_VERSION_SUPPORT_TERMS' => '6.0',
    'MIN_WP_VERSION'               => '6.0',
    'MIN_PHP_VERSION'              => '8.2',
    'MIN_MYSQL_VERSION'            => '',
    'PLUGIN_PREFIX'                => 'codesoup_ilc',
    'PLUGIN_NAME'                  => 'Instapage Local Cache',
    'PLUGIN_VERSION'               => '0.0.1',
]);

$plugin->init();