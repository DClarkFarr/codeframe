<?php 

class Router {
	static $routers = [];
	var $routes = [];
	var $globals = [];
	var $groups = [];

	var $components;
	var $original;
	var $name;

	function __construct($name = null){
		if(!$name){
			$name = Config::get('app.router.default_name');
		}
		$this->registerRouter($name);

		$this->original = $this->components();
		$this->components = clone $this->original;
	}
	function registerRouter($name){
		$this->name = $name;
		self::$routers[$name] = $this;
	}

	static function bootstrap(){

		$router = new self;

		$router->includeRoutes();
	}
	static function get($name = null){
		if(!$name){
			$name = Config::get('app.router.default_name');
		}
		return isset(self::$routers[$name]) ? self::$routers[$name] : false;
	}
	function route(){
		return Route::create(null, null, $this->components->uri, $this->name);
	}
	function reset(){
		return $this->components = clone $this->original;
	}
	function components(){
		$obj = (object) [
			'subdomain' => false,
			'host' => $_SERVER['HTTP_HOST'],
			'uri' => $_SERVER['REQUEST_URI'],
			'method' => $_SERVER['REQUEST_METHOD'],
		];

		$host = explode('.', $obj->host);

		if(count($host) > 2){
			$obj->subdomain = implode('.', array_slice($host, 0, -2));
			$obj->host = trim(str_replace($obj->subdomain, '', $obj->host), '.');
		}

		return $obj;
	}
	function includeRoutes($path = null){
		if(!$path){
			$path = Config::get('app.routes.path');
		}
		

		if(is_file($path)){
			include $path;
		}else if(is_dir($path)){
			foreach(scandir($path) as $file){
				if(is_file(rtrim($path, '/') . '/' . $file)){
					include rtrim($path, '/') . '/' . $file;
				}
				
			}
		}
	}
	function global($pattern, &$uri, $callback){
		$r = Route::any($pattern, $uri);
		$r->global();

		$this->globals[] = array('pattern' => $pattern, 'uri' => $uri, 'callback' => $callback);
		
		if($r->isMatched()){
			if(is_callable($callback)){
				$callback($uri, $r->unused(), $r->params(), $r);
			}
		}
		return $r;
	}
	function group($pattern, &$uri, $callback){
		$r = Route::any($pattern, $uri);
		$r->group();

		$this->groups[] = array('pattern' => $pattern, 'uri' => $uri, 'callback' => $callback);

		if($r->isMatched()){
			if(is_callable($callback)){
				$callback($uri, $r->unused(), $r->params(), $r);
			}
		}
	}
	function parseRoutes(){
		if(!$this->routes){
			return false;
		}
		$matched = [];
		foreach($this->routes as $key => $r){
			$r->update();

			if($r->type == 'global'){
				
			}else if($r->type == 'group'){

			}else{
				if($r->isMatched()){
					$matched[] = $r->traceback;
				}
			}
		}
		return $matched;
	}
	function dispatch(){
		if($this->routes){
			$traceback_ids = $this->parseRoutes();

			echo "<pre>";
			print_r($traceback_ids);
			print_r($this->routes);
			echo "</pre>";
			
		}

		return $this->show404();
	}
	function show404(){
		echo '<p>page not found!</p>';
	}
}