<?php 

class Route {

	static $head_opts = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];
	static $head_defaults = [
		'any' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
		'get' => ['GET'],
		'post' => ['POST'],
		'put' => ['PUT'],
		'delete' => ['DELETE'],
		'options' => ['OPTIONS'],
		'match' => false,
	];
	var $methods;
	var $component;
	var $component_class = 'Uri';

	var $type = 'route';
	var $traceback;

	var $global_traceback = false;
	var $group_traceback = false;

	var $context = [];

	var $controller = false;
	var $router_name;

	function __construct($head, $pattern, $uri = null, $router_name = null){
		$this->setRouter($router_name);
		$this->load($head, $pattern, $uri);

		return $this;
	}
	function controller($controller = false){
		$this->controller = $controller;

		return $this;
	}
	function register($name = null){
		$router = $this->getRouter();
		$old = $this->traceback;

		$this->traceback = $this->getTraceback($name);
		$this->getRouter()->routes[$this->traceback] = $this;

		if($old){
			if(isset($router->globals[$old])){
				$router->globals[$this->traceback] = $router->globals[$old];
			}
			if(isset($router->groups[$old])){
				$router->groups[$this->traceback] = $router->groups[$old];
			}
			unset($router->routes[$old], $router->groups[$old], $router->globals[$old]);
		}
		
		return $this;
	}
	function run(){
		$this->update();

		if(!empty($this->global_traceback)){
			$global = $this->getRouter()->globals[$this->global_traceback];
			$callback = $global['callback'];
			if($this->isMatched()){
				if(is_callable($callback)){
					$callback($this, $this->getRouter());
				}
			}
		}

		if(!empty($this->group_traceback)){
			$group = $this->getRouter()->groups[$this->group_traceback];
			$callback = $group['callback'];
			if($this->isMatched()){
				if(is_callable($callback)){
					$callback($this, $this->getRouter());
				}
			}
		}

		return $this;
	}
	function getTraceback($name = null){
		if(!$name){
			$name = 'route-' . (count($this->getRouter()->routes) + 1);
		}
		$name = str_replace([' ', '/', '.', '\\'], '', $name);
		$int = 0;
		while(isset($this->getRouter()->routes[$name])){
			$arr = explode('-', trim($name, '-'));
			if(count($arr) < 2){
				$name .= '-' . ($int += 1);
			}else{
				$last = array_pop($arr);
				if(is_numeric($last)){
					if($int){
						$int += 1;
					}else{
						$int = $last + 1;
					}
					$name .= implode('-', $arr) . '-' . $int;
				}else{
					$name .= '-' . ($int += 1);
				}
			}
		}
		return $name;
	}

	function load($head, $pattern, $uri = null){
		if($head){
			$this->methods = array_map('ucwords', $head);
		}

		$this->component = (new $this->component_class($uri, $pattern))->setRoute($this)->update();
	}
	function contextMatchedUri(){
		$str = "";
		if($this->hasContext()){
			foreach($this->context as $traceback_id){
				$str .= $this->getRouter()->routes[$traceback_id]->used() . '/';
			}
		}
		return $str;
	}
	function fullMatchedUri(){
		return $this->contextMatchedUri() . $this->used();
	}

	function contextMatchedSegments(){
		$arr = [];
		if($this->hasContext()){
			foreach($this->context as $traceback_id){
				$arr = arrays_add($arr, $this->getRouter()->routes[$traceback_id]->matched());
			}
		}
		return $arr;
	}
	function fullMatchedSegments(){
		return arrays_add($this->contextMatchedSegments(), $this->matched());
	}

	function chain(Route $route){
		if($route->hasContext()){
			foreach($route->context as $traceback){
				array_push($this->context, $traceback);
			}
		}
		array_push($this->context, $route->traceback);
		return $this;
	}
	function hasContext(){
		return !empty($this->context) ? 1 : 0;
	}
	function setRouter($router_name){
		$this->router_name = $router_name;
		return $this;
	}

	function global(){
		$this->type = 'global';
		$this->global_traceback = 'global-' . (count($this->getRouter()->globals) + 1);

		if(preg_match('@route\-\d+@', $this->traceback)){
			$this->register($this->global_traceback);
		}
		
		$this->update();
		return $this;
	}
	function callback(){
		if($this->isMatched()){
			$router = $this->getRouter();

			

			if($this->type == 'global'){
				$callback = $router->globals[$this->global_traceback];
			}else if($this->type == 'group'){
				$callback = $router->groups[$this->group_traceback];
			}

			if(is_callable($callback['callback'])){
				$callback['callback']($this, $router);
			}
		}
		return $this;

	}
	function group(){
		$this->type = 'group';
		$this->group_traceback = 'group-' . (count($this->getRouter()->groups) + 1);

		if(preg_match('@route\-\d+@', $this->traceback)){
			$this->register($this->group_traceback);
		}

		$this->update();
		return $this;
	}
	
	function getRouter(){
		return Router::get($this->router_name);
	}
	function isMatched(){
		if($this->component->payload->matched && in_array($this->getRouter()->components->method, $this->methods) ){
			return true;
		}
		return false;
	}

	function getController(){
		if(is_callable($this->controller)){
			return $this->callbackController($this->controller);
		}else if(is_string($this->controller)){
			return $this->selectController($this->controller);
		}else{
			return $this->findController($this->fullMatchedSegments());
		}
	}
	function callbackController($callback){
		if(is_callable($callback)){
			return $callback($this);
		}
	}
	function selectController($classAndAction){
		@list($class, $action) = explode('@', $classAndAction);
		if(!$action){
			$action = 'indexAction';
		}

		if(!class_exists($class)){
			return [false, 'Controller not found'];
		}
		if(!method_exists($class, $action)){
			return [false, 'Action not found'];
		}

		return [true, [
			'class' => $class,
			'action' => $action,
			'segments' => $this->unmatched(),
		]];
	}
	function findController($rows){
		if(count($rows)){
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

		$class = App::namespaces()->controllers . '\\IndexController';
		$action = 'indexAction';

		if(!class_exists($class)){
			return [false, 'Controller not found'];
		}
		if(!method_exists($class, $action)){
			return [false, 'Action not found'];
		}

		return [true, [
			'class' => $class,
			'action' => $action,
			'segments' => $this->unmatched(),
		]];
		
	}
	function segmentsToClass($segments){
		$classname = App::namespaces()->controllers;

		if($segments){
			foreach($segments as $segment){
				if(empty($segment->rule)){
					$classname .= '\\' . $this->strToClass($segment->segment);
				}else if($segment->rule->type == 'static'){
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

	function __call($name, $params){
		if(method_exists($this->component, $name)){
			$res = call_user_func_array([$this->component, $name], $params);
			if(is_object($res) && get_class($res) == $this->component_class){
				return $this;
			}else{
				return $res;
			}
		}else if(in_array($name, ['any', 'get', 'post', 'put', 'delete', 'options', 'match'])){
			if($name != 'match'){
				array_unshift($params, self::$head_defaults[$name]);
			}
			call_user_func_array([$this, 'load'], $params);

			return $this;
		}

		throw new Exception('Route::' . $name . '() not found');
	}
	static function __callStatic($name, $params){
		if(in_array($name, ['any', 'get', 'post', 'put', 'delete', 'options', 'match'])){
			if($name != 'match'){
				array_unshift($params, self::$head_defaults[$name]);
			}
			return call_user_func_array([self::class, 'create'], $params); 
		}
		throw new Exception('Route::' . $name . '() not found');
	}

	//static functions
	static function create($head, $pattern, $uri = null, $router_name = null){
		if(!$router_name){
			$router_name = Config::get('app.router.default_name');
		}

		$r = new self($head, $pattern, $uri, $router_name);
		$r->register();
		return $r;
	}

}