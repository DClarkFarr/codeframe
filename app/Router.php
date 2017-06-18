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
		$this->register($name);

		$this->original = $this->components();
		$this->components = clone $this->original;
	}
	function register($name){
		$this->name = $name;
		self::$routers[$name] = $this;
	}

	static function bootstrap(){

		$router = new self;

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
			'url' => self::getUrl(),
			'method' => $_SERVER['REQUEST_METHOD'],
			'document_root' => $_SERVER['DOCUMENT_ROOT'],
			'site_root' => getcwd(),
		];

		$obj->document_to_site = str_replace($obj->document_root, '', $obj->site_root);

		$obj->subdomain = extract_subdomains($_SERVER['HTTP_HOST']);
		$obj->host = extract_domain($_SERVER['HTTP_HOST']);
		$obj->uri = explode('?', $_SERVER['REQUEST_URI'])[0];

		return $obj;
	}
	
	function global($pattern, $uri, $callback){
		$r = Route::any($pattern, $uri);
		$r->global();

		$this->globals[$r->traceback] = array('pattern' => $pattern, 'uri' => $uri, 'callback' => $callback);
		
		if($r->isMatched()){
			if(is_callable($callback)){
				$callback($r, $this);
			}
		}

		return $r;
	}
	function group($pattern, &$uri, $callback){
		$r = Route::any($pattern, $uri);
		$r->group();

		$this->groups[$r->traceback] = array('pattern' => $pattern, 'uri' => $uri, 'callback' => $callback);

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
		$scores = [];

		foreach($this->routes as $key => $r){
			$r->update();

			if($r->type == 'global'){
				/*
				$global = $this->globals[$r->global_traceback];

				$callback = $global['callback'];
				if($r->isMatched()){
					$callback($r, $this);
				}
				
				*/

			}else if($r->type == 'group'){

			}else{
				if($r->isMatched()){
					$scores[$r->traceback] = strlen($r->unused());
				}
			}
		}
		asort($scores);
		return ['scores' => $scores, 'tracebacks' => array_keys($scores)];
	}
	function dispatch(){
		if($this->routes){
			$parsed = $this->parseRoutes();

			if(isset($parsed['tracebacks'][0])){
				$best_match = $this->routes[$parsed['tracebacks'][0]];
				list($status, $message) = $best_match->getController();

				if($status){
					$controller = new $message['class']($best_match);
					echo $controller->{$message['action']}();
					return;
				}
			}
		}

		echo $this->show404();
	}

	static function getUrl(){
		return self::getProtocol() . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	static function getProtocol(){
		if (isset($_SERVER['HTTPS']) &&
		    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
		    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
		    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
		  $protocol = 'https://';
		}
		else {
		  $protocol = 'http://';
		}
		return $protocol;
	}
	function show404(){
		return '<p>page not found!</p>';
	}
}

