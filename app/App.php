<?php 


class App {

	static $properties;

	static function bootstrap(){
		
		

		Config::bootstrap();

		DB::bootstrap();

		Session::bootstrap();

		Cache::bootstrap();

		Router::bootstrap();

		
		self::extendControllersViewsRoutes('www', 'www', 'index.php');

		self::registerNamespaces([
			self::namespaces()->controllers => self::paths()->controllers,
			'Models' => Config::get('app.paths.models'),
			'routes' => self::paths()->routes,
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

	static function extendControllersViewsRoutes($controllers, $views, $routes){
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
		self::$properties->namespaces->$key = ucfirst($key) . '\\' . trim($val, '\\');
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
	
}