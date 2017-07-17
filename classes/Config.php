<?php 
namespace Codeframe;

class Config {
	use Traits\Storage;
	static $properties;
	
	static $app_root;

	static $autoload_files = [
		'database' => 'database.php',
		'app' => 'app.php',
		'session' => 'session.php',
	];

	static function bootstrap($app_root){
		self::$app_root = $app_root;
		self::$properties = (object) [];
		self::includeFiles();
		self::loadEnv();
	}
	static function includeFiles(){
		foreach(self::$autoload_files as $property => $file){
			$path = self::$app_root .'/'. $file;
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

	static function loadEnv(){

		if(is_file(Config::get('app.paths.root') . '/.env')){
			$env = include Config::get('app.paths.root') . '/.env';
			if(is_array($env)){
				foreach($env as $key => $val){
					self::put($key, $val);
				}
			}
		}
	}
}