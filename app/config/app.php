<?php 

return [
	'routes.path' => realpath(__DIR__ . '/../routes/'),
	'router' => array(
		'default_name' => 'default',
	),
	'cache' => array(
		'path' => realpath(__DIR__ . '/../storage/cache/'),
	),
];