<?php 

return [
	
	'environment' => 'development', //or production

	'debug' => 1, // or 0

	'router' => array(
		'default_name' => 'default',
	),
	'cache' => array(
		'path' => realpath(__DIR__ . '/../storage/cache/'),
	),
	'paths' => array(
		'application' => APPLICATION_ROOT,
		'models' => realpath( __DIR__ . '/../Models'),
	),
];