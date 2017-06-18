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
		$scores = [];
		foreach($this->routes as $key => $r){
			$r->update();

			if($r->type == 'global'){
				
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
	function findController($rows){

		for($i = count($rows) - 1; $i > -1; $i--){
			$class = $this->segmentsToClass(array_slice($rows, 0, $i + 1)) . 'Controller';
			if(class_exists($class)){
				$unused = array_slice($rows, $i + 1);
				if($unused){
					$action = reset($unused)->segment . 'Action';
					if(method_exists($class, $action)){
						array_shift($rows);
					}else{
						$action = 'indexAction';
					}
				}else{
					$action = 'indexAction';
				}
				
				if(method_exists($class, $action)){
					return [true, [
						'class' => $class,
						'action' => $action,
						'segments' => $rows ? $rows : null
					]];
				}
				return [false, 'Action not Found'];
			}
		}

		return [false, 'Controller not found'];
		
	}
	function segmentsToClass($segments){
		$classname = 'Controllers';
		if($segments){
			foreach($segments as $segment){
				if($segment->rule->type == 'static'){
					$classname .= '\\' . $this->strToClass($segment->rule->value);
				}else if($segment->rule->type == 'variable'){
					$classname .= '\\' . $this->strToClass( property_exists($segment, 'param') ?  $segment->param : $segment->rule->value);
				}
				
			}

			return $classname;
		}
		return false;
	}
	function strToClass($str){
		$str = str_replace(array('-', '_', '.'), ' ', $str);
		$str = preg_replace('@\s{2,}@', ' ', $str);
		$str = ucwords($str);
		$str = str_replace(' ', '', $str);
		return $str;
	}
	function dispatch(){
		if($this->routes){
			$parsed = $this->parseRoutes();

			if(isset($parsed['tracebacks'][0])){
				$best_match = $this->routes[$parsed['tracebacks'][0]];

				list($status, $message) = $this->findController($best_match->fullMatchedSegments());

				if($status){
					$controller = new $message['class']($best_match);
					echo $controller->{$message['action']}();
					return;
				}
			}
		}

		echo $this->show404();
	}
	function show404(){
		return '<p>page not found!</p>';
	}
}