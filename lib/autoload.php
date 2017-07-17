<?php 
$files = [
	'Arrays.php',
	'Files.php',
	'Mysqli.php',
	'Strings.php',
	'Urls.php',
	'Variables.php',
];

foreach($files as $file){
	if(is_file(__DIR__ . '/' . $file)){
		include __DIR__ . '/' . $file;
	}
}

includeFiles(__DIR__ . '/Traits');
