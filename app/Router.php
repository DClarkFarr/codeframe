<?php 

class Router {
	static $routers = [];
	var $routes = [];
	var $globals = [];
	var $groups = [];
	var $mvcs = [];

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
	
	function global($pattern, $uri, $callback = null){
		if(is_callable($uri)){
			$callback = $uri;
			$uri = null;
		}
		$r = Route::any($pattern, $uri);
		$r->global();

		$this->globals[$r->traceback] = array('pattern' => $pattern, 'uri' => $uri, 'callback' => $callback);

		return $r;
	}

	function group($pattern, $uri, $callback){
		$r = Route::any($pattern, $uri);
		$r->group();

		$this->groups[$r->traceback] = array('pattern' => $pattern, 'uri' => $uri, 'callback' => $callback);

		return $r;
	}
	function parseRoutes(){
		if(!$this->routes){
			return false;
		}
		$scores = [];

		foreach($this->routes as $key => $r){
			$payload = $r->payload();

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
	function parseMvcs(){
		if(!$this->mvcs){
			return;
		}
		$scores = [];

		foreach($this->mvcs as $key => $result){
			$scores[$key] = strlen(implode('/', $result['segments']));
		}
		asort($scores);
		return ['scores' => $scores, 'mvcs' => $this->mvcs];
	}
	function dispatch(){
		if($this->routes){
			$parsedRoutes = $this->parseRoutes();
			$parsedMvcs = $this->parseMvcs();

			$mvc_score = $mvc = false;
			if(isset($parsedMvcs['scores'][0])){
				$mvc_score = $parsedMvcs['scores'][0];
				$mvc = $parsedMvcs['mvcs'][0];
			}	

			if(isset($parsedRoutes['tracebacks'][0])){
				$route_traceback = $parsedRoutes['tracebacks'][0];
				$route_score = $parsedRoutes['scores'][$route_traceback];

				$route = $this->routes[$route_traceback];

				list($status, $message) = $route->getController();

				if($status && ((is_numeric($mvc_score) && $route_score <= $mvc_score) || $mvc_score === false)){
					$controller = new $message['class']($route);
					echo $controller->{$message['action']}();
					return;
				}
			}

			if(!empty($mvc)){
				$controller = new $mvc['controller']($mvc['route']);
				echo $controller->{$mvc['action']}();
				return;
			}
		}

		echo $this->show404();
	}
	function MVCtoController($uri, $controllers_dir){
		$route = Route::any(null, $uri)->update();

		$segments = $route->all();
		$shifted = false;
		
		$controller_segments = [];
		while($segments){
			if($segments[0]->segment === null){
				array_shift($segments);
				continue;
			}

			$controller_segments[] = $segments[0];

			$class = $route->segmentsToClass($controller_segments);
			//$class = implode('\\', explode('\\\\', $class));

			if(class_exists($class . 'Controller')){
				array_shift($segments);

			}else{
				array_pop($controller_segments);
				break;
			}
		}
		$controller_class = 'not found';

		if(!empty($controller_segments)){
			$controller_class = $route->segmentsToClass($controller_segments) . 'Controller';
			//$controller_class = implode('\\', explode('\\\\', $controller_class));
		}else{
			$controller_class = App::namespaces()->controllers . '\\IndexController';
		}

		if(!class_exists($controller_class)){
			//echo 'class does not exist';
			return false;
		}

		$pattern = '';
		foreach($controller_segments as $s){
			$pattern .= ($pattern ? '/' : '') . $s->segment;
		}

		$action = 'not found';

		if($segments){
			$action_segment = $segments[0];
			$action_method = $route->strToClass($action_segment->segment) . 'Action';
			if(method_exists($controller_class, $action_method)){
				$segment = array_shift($segments);
				$action = $action_method;
				$pattern .= ($pattern ? '/' : '') . $action_segment->segment;
			}
		}else{
			if(!$pattern){
				$pattern = '/';
			}
			$action = 'indexAction';
		}

		if(!method_exists($controller_class, $action)){
			//echo 'action not found';
			return false;
		}

		$route->load(false, $pattern, false);
		$route->update();

		$segment_slugs = [];
		foreach($segments as $s){
			$segment_slugs[] = $s->segment;
		}

		$this->mvcs[] = [
			'controller' => $controller_class,
			'action' => $action,
			'segments' => $segment_slugs,
			'route' => $route,
		];

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

