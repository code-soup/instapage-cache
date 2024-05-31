<?php // Silence is golden

// Exit if accessed directly
defined( 'WPINC' ) || die;

printf(
    '<input id="%s" name="%s[%s][%s]" type="checkbox" value="checked" %s />',
    $params['id'],
    $params['option_name'],
    $params['section'],
    $params['name'],
    $params['value'],
    empty($params['value']) ? '' : ' checked'
);