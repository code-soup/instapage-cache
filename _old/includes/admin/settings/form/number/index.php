<?php // Silence is golden

// Exit if accessed directly
defined( 'WPINC' ) || die;

$desc = '';

if ( ! empty($params['description']) )
{
    $desc = sprintf(
        '<p><small>%s</small></p>',
        esc_attr( $params['description'] )
    );
}

printf(
    '<input id="%s" name="codesoup_ilc_settings[%s][%s]" type="number" value="%s" />%s',
    $params['id'],
    $params['section'],
    $params['name'],
    $params['value'],
    $desc
);