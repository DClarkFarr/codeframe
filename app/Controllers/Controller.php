<?php 
namespace Controllers;

class Controller {
	var $route;

	var $get;
	
	function __construct($route){
		$this->route = $route;

		$this->get = (object) ['params' => [], 'uri' => []];

		$this->makeGetFromRoute();

		$this->initialize();
	}

	function initialize(){

	}

	function makeGetFromRoute(){
		$segments = arrays_add($this->route->matched(), $this->route->unmatched());
		if(!$segments){
			return;
		}

		foreach($segments as $segment){

			$this->get->uri[] = $segment->segment;
			if(!empty($segment->rule->type) && $segment->rule->type == 'variable'){
				$this->get->params[$segment->rule->value] = $segment->segment;
			}
		}
	}
	function get($key = null){
		if($key == null){
			return $this->get->uri;
		} 
		return !empty($this->get->params[$key]) ? $this->get->params[$key] : null;
	}
}