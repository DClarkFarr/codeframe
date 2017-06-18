<?php 


class App {

	static function bootstrap(){
		
		Config::bootstrap();

		DB::bootstrap();

		Router::bootstrap();

		Session::bootstrap();

		Cache::bootstrap();

		self::registerNamespaces([
			'Controllers' => Config::get('app.paths.controllers'),
			'Models' => Config::get('app.paths.models'),
			'routes' => Config::get('app.paths.routes'),
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