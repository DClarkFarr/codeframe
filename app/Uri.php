<?php 

Class Uri {
	var $uri = "";
	var $pattern;

	var $regex;

	var $bindings;

	var $payload;

	//chainable functions
	function __construct($uri = null, $pattern = null, $regex = [], $auto = null){	

		$this->payload = (object) [];
		$this->bindings = [];

		$default_regex = [
			'var_required' => '@\{([A-Za-z0-9_\-]+)\}@',
			'var_optional' => '@\{([A-Za-z0-9_\-]+)\?\}@',
		];

		$this->regex = (object) array_merge($default_regex, $regex);

		$this->load($uri, $pattern, $auto);

		return $this;
	}	
	function uri($uri){
		$this->uri = $uri;
		return $this->load($this->uri, $this->pattern, true);
	}
	function where($key, $val = '', $apply = true){
		if(!is_array($key)){
			$arr = [$key => $val];
		}else{
			$arr = $key;
		}


		foreach($arr as $key => $val){
			$this->bindings[$key] = $val;
		}

		if($apply){
			$this->payload();
		}
		return $this;
	}
	function load($uri, $pattern, $auto = null){
		$this->uri = $uri;
		$this->pattern = $pattern;

		if($uri && $pattern && $auto){
			$this->payload();
		}else{
			$this->parse();
		}
		return $this;
	}
	function bind(){
		if(empty($this->bindings)){
			return $this;
		}
		
		foreach($this->bindings as $param_name => $pattern){
			$seg_key = array_search($param_name, $this->payload->pattern->variables);
			if(is_numeric($seg_key)){
				//$segment = $this->payload->uri->segments[$seg_key];
				$rule = $this->payload->pattern->rules[$seg_key];

				$value = isset($this->payload->params[$param_name]) ? $this->payload->params[$param_name] : false;

				if($value !== false){
					if(!preg_match('@' . $pattern . '@', $value)){
						$this->payload->matched = $this->payload->segments[$seg_key] = false;
						unset($this->payload->params[$param_name]);
					}
				}else if(!$rule->optional){
					$this->payload->matched = $this->payload->segments[$seg_key] = false;
				}
			}
		}

		return $this;
	}
	function update(){
		$this->payload();
		return $this;
	}


	//non-chainable functions
	function params(){
		return !empty($this->payload->params) ?  $this->payload->params : [];
	}
	function isMatched(){
		return !empty($this->payload->matched) ? true : false;
	}
	function hasPayload(){
		return count(get_object_vars($this->payload)) > 0 ? 1 : 0;
	}
	function payload(){
		$this->payload = $this->parse();

		$this->bind();

		return $this->payload;
	}
	
	function matched(){
		$result = [];
		foreach($this->payload->segments as $seg_key => $matched){
			if(!$matched){
				break;
			}

			$result[] = $this->fetchResultset($seg_key);
		}

		return $result;
	}
	function unmatched(){
		$result = [];
		foreach($this->payload->segments as $seg_key => $matched){
			if($matched){
				continue;
			}

			$result[] = $this->fetchResultset($seg_key);
		}

		return $result;
	}
	function used(){
		$segment_objs = $this->matched();
		$uri = "";
		foreach($segment_objs as $obj){
			$uri .= ($uri ? '/' : '') . $obj->segment; 
		}
		return $uri;
	}
	function unused(){
		$segment_objs = $this->unmatched();
		$uri = "";
		foreach($segment_objs as $obj){
			$uri .= ($uri ? '/' : '') . $obj->segment; 
		}
		return $uri;
	}

	//util functions
	function parse($uri = null, $pattern = null){
		if($uri === null){
			$uri = $this->uri;
		}
		if($pattern === null){
			$pattern = $this->pattern;
		}

		$pattern_result = $this->patternParse($pattern);
		$uri_result = $this->uriParse($uri);
		$result = (object)['matched' => true, 'uri' => $uri_result, 'pattern' => $pattern_result, 'params' => [], 'segments' => []];

		$loops = max(count($pattern_result->rules), count($uri_result->segments));
		for($key = 0; $key < $loops; $key++){
			$segment = !empty($uri_result->segments[$key]) ? $uri_result->segments[$key] : false;
			$rule = !empty($pattern_result->rules[$key]) ? $pattern_result->rules[$key] : false;
			
			if(!$rule){
				$result->segments[$key] = false;
			}else if($rule->type == 'variable'){
				if($segment){
					$result->params[$rule->value] = $segment;
					$result->segments[$key] = true;
				}else if(!$rule->optional){
					$result->matched = $result->segments[$key] = false;
				}
			}else if($rule->type == 'static'){
				if($rule->value != $segment){
					$result->matched = $result->segments[$key] = false;
				}else{
					$result->segments[$key] = true;
				}
			}
		}
			
		return $result;
	}
	function fetchResultset($seg_key){
		$segment = !empty($this->payload->uri->segments[$seg_key]) ? $this->payload->uri->segments[$seg_key] : null;

		$rule = !empty($this->payload->pattern->rules[$seg_key]) ? $this->payload->pattern->rules[$seg_key] : null;

		$binding = !empty($rule->type) == 'variable' && !empty($this->bindings[$rule->value]) ? $this->bindings[$rule->value] : null;

		
		$variable = !empty($this->payload->pattern->variables[$seg_key]) ? $this->payload->pattern->variables[$seg_key] : null;
		$param = null;
		if($variable){
			$param = $this->payload->params[$variable];
		}
		return (object)['segment' => $segment, 'rule' => $rule, 'binding' =>  $binding, 'param' => $param];
	}
	function uriParse($uri){
		$result = (object) [
			'original' => $uri,
			'prepared' => $this->uriPrepare($uri),
			'segments' => [],
		];

		$result->segments = explode('/', $result->prepared);

		return $result;
	}
	function uriPrepare($uri){
		return trim($uri, ' /');
	}
	function patternParse($pattern = null){
		if($pattern === null){
			$pattern = $this->pattern;
		}

		$segments = explode('/', $this->patternPrepare($pattern));

		$matches = [];
		$result = (object) ['pattern' => $pattern, 'rules' => [], 'variables' => []];
		foreach($segments as $seg){
			if(preg_match($this->regex->var_optional, $seg, $matches)){
				$result->rules[] = (object) ['type' => 'variable', 'optional' => true, 'value' => $matches[1], 'full' => $matches[0]];
				$result->variables[(count($result->rules) - 1)] = $matches[1];
			}else if(preg_match($this->regex->var_required, $seg, $matches)){
				$result->rules[] = (object) ['type' => 'variable', 'optional' => false, 'value' => $matches[1], 'full' => $matches[0]];
				$result->variables[(count($result->rules) - 1)] = $matches[1];
			}else if($seg){
				$result->rules[] = (object) ['type' => 'static',  'value' => $seg];
			}
		}
		return $result;
	}
	function patternPrepare($pattern){
		return trim($pattern, ' /');
	}
	
}
