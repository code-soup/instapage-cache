<?php // Silence is golden

// Exit if accessed directly
defined( 'WPINC' ) || die;

printf(
    '<input id="%s" name="codesoup_ilc_settings[%s][%s]" type="text" value="%s" />',
    $params['id'],
    $params['section'],
    $params['name'],
    $params['value']
);