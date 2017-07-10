<?php 


class App {

	static function bootstrap(){
		
		Config::bootstrap();

		DB::bootstrap();

		Router::bootstrap();

		Session::bootstrap();

		Cache::bootstrap();

		self::registerNamespaces([
			'Models' => Config::get('app.paths.models'),
			'Controllers' => CONTROLLERS_PATH,
			'routes' => ROUTES_PATH,
		]);
		
	}

	static function registerNamespaces($arr){

		foreach($arr as $namespace => $path){
			if(!$path){
				continue;
			}
			registerNamespace($path, $namespace);
		}

	}
}