<?php // Silence is golden

// Exit if accessed directly
defined( 'WPINC' ) || die;

$name     = str_replace( '-', '_', sanitize_title( $args['field']['id'] ) );
$defaults = array(
    'name'        => $name,
    'placeholder' => '',
    'class_name'  => '',
    'value'       => '',
    'section'     => $args['section'],
    'option_name' => $args['option']
);

$params = wp_parse_args( $args['field'], $defaults );

include sprintf(
    '%s/index.php',
    $params['type']
);