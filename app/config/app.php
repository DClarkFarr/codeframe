<?php 

return [
	
	'environment' => 'development', //or production

	'debug' => 1, // or 0

	'router' => array(
		'default_name' => 'default',
	),
	'cache' => array(
		'path' => realpath(__DIR__ . '/../') . '/storage/cache/',
	),
	'paths' => array(
		'application' => realpath( __DIR__ . '/../'),
		'controllers' => realpath( __DIR__ . '/../') . '/Controllers',
		'views' => realpath( __DIR__ . '/../') . '/Views',
		'models' => realpath( __DIR__ . '/../') . '/Models',
		'routes' => realpath( __DIR__ . '/../') . '/routes',
	),
];