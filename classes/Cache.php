<?php 
namespace Codeframe;

use Illuminate\Cache\CacheManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Container\Container;

class Cache {
	
	static $instance;

	static function bootstrap(){
		makeDir(Config::get('app.cache.path'));

		$app = new Container();
		$app->singleton('files', function(){
		    return new Filesystem();
		});

		$app->singleton('config', function(){
		    return [
		        'cache.default' => 'files',
		        'cache.stores.files' => [
		            'driver' => 'file',
		            'path' => Config::get('app.cache.path'),
		        ]
		    ];
		});

		$cacheManager = new CacheManager($app);

		self::$instance = $cacheManager->driver();	
	}

	static function put($key, $val = null, $minutes = null){
		if(!$minutes){
			$minutes = (60 * 24 * 2); //two days by default
		}
		if(!is_array($key)){
			$key = [$key => $val];
		}
		foreach($key as $k => $v){
			self::$instance->put($k, $v, $minutes);
		}
	}

	static function __callStatic($name, $args){
		if(empty(self::$instance)){
			throw new \RuntimeException('You need to run install first!');
		}

		if(method_exists(self::$instance, $name)){
			$res = call_user_func_array([self::$instance, $name], $args);
			return $res;
		}

		throw new \Exception('Cache::' . $name . '() does not exist');
	}
}





