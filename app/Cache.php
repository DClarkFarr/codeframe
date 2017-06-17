<?php 

use Illuminate\Cache\CacheManager;
use Illuminate\Filesystem\Filesystem;

class Cache {
	
	static $instance;

	static function bootstrap(){
		$app = new Illuminate\Container\Container();
		$app->singleton('files', function(){
		    return new Illuminate\Filesystem\Filesystem();
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

	static function __callStatic($name, $args){
		if(empty(self::$instance)){
			throw new RuntimeException('You need to run install first!');
		}

		if(method_exists(self::$instance, $name)){
			return call_user_func_array([self::$instance, $name], $args);
		}

		throw new \Exception('Cacke::' . $name . '() does not exist');
	}
}





