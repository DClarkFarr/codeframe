<?php 
$files = [
	'mysqli.functions.php',
];
foreach($files as $file){
	if(is_file(__DIR__ . '/' . $file)){
		include __DIR__ . '/' . $file;
	}
}