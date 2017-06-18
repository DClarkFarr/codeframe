<?php 
$files = [
	'mysqli.functions.php',
	'Files.php',
	'Arrays.php',
];

foreach($files as $file){
	if(is_file(__DIR__ . '/' . $file)){
		include __DIR__ . '/' . $file;
	}
}

includeFiles(__DIR__ . '/Traits');
