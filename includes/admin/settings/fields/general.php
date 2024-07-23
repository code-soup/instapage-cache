<?php

// Exit if accessed directly
defined( 'WPINC' ) || die;

return array(
	array(
		'id'          => 'instapage-cache-enabled',
		'label'       => 'Enable Caching',
		'description' => '',
		'type'        => 'checkbox',
		'value'       => 'checked',
	),
	array(
		'id'          => 'cache-delete-interval',
		'label'       => 'Auto Clear Cache Every:',
		'description' => 'Minutes',
		'type'        => 'number',
		'value'       => 60,
	),
);