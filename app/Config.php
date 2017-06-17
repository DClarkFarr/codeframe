<?php 

class Config {
	use \Traits\Storage;
	static $properties;

	static $autoload_files = [
		'database' => 'database.php',
		'app' => 'app.php',
		'session' => 'session.php',
	];

	static function bootstrap(){
		self::$properties = (object) [];
		self::includeFiles();
	}
	static function includeFiles(){
		foreach(self::$autoload_files as $property => $file){
			$path = __DIR__ . '/config/' . $file;
			if(is_file($path)){
				self::put($property, (object) include $path);
			}
		}
	}

	static function put($key, $val = null){	
		self::_put(self::$properties, $key, $val);
	}

	static function get($key){
		return self::_get(self::$properties, $key);
	}
}