<?php 
namespace Codeframe;

class App {

	static $properties;

	static function bootstrap($app_root = null){
	
		Config::bootstrap($app_root);

		if(Config::get('app.debug')){
			self::enableDebug();
		}

		date_default_timezone_set(Config::get('timezones.server_default'));

		DB::bootstrap();

		Schema::bootstrap();

		Session::bootstrap();

		Cache::bootstrap();

		Router::bootstrap();
		
		self::extendControllersViewsRoutes();

		self::registerNamespaces([
			'Models' => Config::get('app.paths.models'),
			'Controllers' => Config::get('app.paths.controllers'),
			'Services' => Config::get('app.paths.services'),
			'Utils' => Config::get('app.paths.utils'),
			'routes' => Config::get('app.paths.routes'),
		]);
	}

	static function paths(){
		return self::$properties->paths;
	}
	static function namespaces(){
		return self::$properties->namespaces;
	}

	static function registerNamespaces($arr){

		foreach($arr as $namespace => $path){
			if(!$path){
				continue;
			}
			registerNamespace($path, $namespace);
		}

	}

	static function extendControllersViewsRoutes($controllers = '', $views = '', $routes = ''){
		self::initProperties();

		self::extendPath('controllers', $controllers);
		self::extendNamespace('controllers', $controllers);
		self::extendPath('views', $views);
		self::extendPath('routes', $routes);

	}
	static function extendPath($key, $val){
		self::$properties->paths->$key = rtrim(Config::get('app.paths.' . $key) . '/' . trim($val, '/'), '/');
	}
	static function extendNamespace($key, $val){
		self::$properties->namespaces->$key = trim(ucfirst($key) . '\\' . $val, '\\');
	}
	static function initProperties(){
		if(is_object(self::$properties)){
			return;
		}

		if(!is_object(self::$properties)){
			self::$properties = (object) ['paths' => '', 'routes' => '', 'namespaces' => ''];
		}

		self::$properties->paths = (object)[ 'controllers' => '', 'views' => ''];
		self::$properties->namespaces = (object)[ 'controllers' => ''];
	}
	static function enableDebug(){
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
	}
	
}