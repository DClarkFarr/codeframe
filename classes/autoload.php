<?php 
$files = [
	'App.php',
	'Cache.php',
	'Config.php',
	'Controller.php',
	'DB.php',
	'Route.php',
	'Session.php',
	'Template.php',
	'Uri.php',
];

foreach($files as $file){
	if(is_file(__DIR__ . '/' . $file)){
		include __DIR__ . '/' . $file;
	}
}
