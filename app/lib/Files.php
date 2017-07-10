<?php 

function includeFiles($path, $recursive = true, $callback = null, $depth = 0){
	if(!is_file($path) && !is_dir($path)){
		return false;
	}
	$path = rtrim($path, '/') . '/';
	$scandir = scandir($path);
	foreach($scandir as $filename){
		if($filename == '.' || $filename == '..'){
			continue;
		}
		if(is_dir($path . $filename)){
			if($recursive){
				includeFiles($path . $filename, $recursive, $callback, $depth + 1);
			}
		}else if(is_file($path . $filename)){
			if(is_callable($callback)){
				$callback($path, $filename, $depth);
			}else{
				include $path . $filename;
			}
		}
	}
	return true;
}

function registerNamespace($path, $namespace){
	$app_dir = Config::get('app.paths.application');

	if(is_file($path)){
		$base_path = trim(str_replace($app_dir, '', $path), '/');
		if( stripos($base_path, $namespace) !== false ){
			include $path;
		}
		return false;
	}
	includeFiles($path, true, function($path, $filename, $depth) use ($namespace, $app_dir){
		$base_path = trim(str_replace($app_dir, '', $path), '/');
		if( stripos($base_path, $namespace) !== false ){
			include $path . $filename;
		}
	});
}