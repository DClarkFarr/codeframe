<?php 

function view($path, $params, $prefix = 'views'){
	$ext = pathinfo($path, PATHINFO_EXTENSION);
	if($ext != 'php' && $ext != 'html'){
		$path = Codeframe\Config::get('app.paths.' . $prefix) . '/' . str_replace('.', '/', $path) . '.php';
	}

	extract($params);
	ob_start();

	if(is_file($path)){
		include $path;
	}

	return ob_get_clean();
}
function includeFiles($path, $recursive = true, $callback = null, $depth = 0){
	if(!is_file($path) && !is_dir($path)){
		return false;
	}

	$parse = function($path, $filename, $callback, $depth) use ($recursive){
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

		return true;
	};

	if(is_file($path)){
		return $parse($path, '', $callback, $depth);
	}

	$path = rtrim($path, '/') . '/';
	$scandir = scandir($path);
	foreach($scandir as $filename){
		if($filename == '.' || $filename == '..'){
			continue;
		}
		$parse($path, $filename, $callback, $depth);
	}
	return true;
}

function registerNamespace($path, $namespace){
	$app_dir = CodeFrame\Config::get('app.paths.application');
	$namespace = str_replace('\\', '/', $namespace);

	if(is_file($path)){
		$base_path = trim(str_replace($app_dir, '', $path), '/');
		if( stripos($base_path, $namespace) !== false ){
			include $path;
		}
		return false;
	}

	includeFiles($path, true, function($path, $filename, $depth) use ($namespace, $app_dir){
		$base_path = trim(str_replace($app_dir, '', $path), '/');

		if( stripos($base_path, $namespace) !== false && pathinfo($path . $filename, PATHINFO_EXTENSION) == 'php'){
			include $path . $filename;
		}
	});
}

function makeDir($path){
	if(!$path || is_dir($path)){
		return;
	}
	$base = ($path[0] == '/' ? '/' : '');
	$path = trim($path, '/');

	foreach(explode('/', $path) as $seg){
		$base .= $seg . '/';
		if(!is_dir($base)){
			mkdir($base);
		}
	}
}