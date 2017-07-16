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
		'root' => coalesce(ifGlobal('DOCUMENT_ROOT'), $_SERVER['DOCUMENT_ROOT']),
		'application' => coalesce(ifGlobal('APPLICATION_ROOT'), realpath(__DIR__ . '/../')),
		'routes' => coalesce(ifGlobal('ROUTES_PATH'), realpath(__DIR__ . '/../routes')),
		'controllers' => coalesce(ifGlobal('CONTROLLERS_PATH'), realpath(__DIR__ . '/../Controllers')),
		'models' => realpath( __DIR__ . '/../Models'),
		'templates' => realpath(__DIR__ . '/../templates'),
		'views' => realpath(__DIR__ . '/../views'),
	),
];