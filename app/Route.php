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
	var $component_uri;
	var $component_pattern;

	var $type = 'route';
	var $traceback;

	var $global_traceback = false;
	var $group_traceback = false;

	var $context = [];

	var $router_name;

	function __construct($head, $pattern, $uri = null, $router_name = null){
		$this->setRouter($router_name);
		$this->load($head, $pattern, $uri);
	}
	function load($head, $pattern, $uri){
		if($head){
			$this->methods = array_map('ucwords', $head);
		}
		if($pattern){
			$this->component_pattern = $pattern;
		}
		if($uri){
			$this->component_uri = $uri ? $uri : $this->componentUri();
		}
		
		$this->component = new $this->component_class($this->component_uri, $this->component_pattern);
	}

	function chain(Route $route){
		array_push($this->context, $route->traceback);
		return $this;
	}
	function setRouter($router_name){
		$this->router_name = $router_name;
		return $this;
	}
	function global(){
		$this->type = 'global';
		$this->global_traceback = count($this->getRouter()->globals);
		$this->update();
		return $this;
	}
	function group(){
		$this->type = 'group';
		$this->group_traceback = count($this->getRouter()->groups);
		$this->update();
		return $this;
	}
	function componentUri(){
		$cc = strtolower($this->component_class);
		if($cc == 'uri'){
			return $this->getRouter()->components->uri;
		}else if($cc == 'host'){
			return $this->getRouter()->host;
		}else if($cc == 'subdomain'){
			return $this->getRouter()->subdomain;
		}
	}
	function getRouter(){
		return Router::get($this->router_name);
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

	function isMatched(){
		if($this->component->payload->matched && in_array($this->getRouter()->components->method, $this->methods) ){
			return true;
		}
		return false;
	}


	//static functions
	static function create($head, $pattern, $uri = null, $router_name = null){
		if(!$router_name){
			$router_name = Config::get('app.router.default_name');
		}

		$r = new self($head, $pattern, $uri, $router_name);
		$r->traceback = count($r->getRouter()->routes);
		$r->getRouter()->routes[] = $r;
		return $r;
	}

}