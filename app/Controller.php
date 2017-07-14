<?php 
namespace Controllers;

class Controller {
	var $route;

	var $get;

	var $view;

	var $template;

	var $extends;
	
	function __construct($route){
		$this->route = $route;

		$this->get = (object) ['params' => [], 'uri' => []];

		$this->view = (object) [];

		$this->template = new \Template;

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

	function view($file, $args = []){
		if($this->template){
			return $this->template($file, $args);
		}else{
			return view($file, $this->makeView($args));
		}
	}
	function template($file, $args = [], $extends = null){
		if($extends === null){
			$extends = $this->extends;
		}
		if($extends){
			$res = $this->template->load($extends);
			if($res){
				$this->template = $res;
			}
		}

		return $this->template->make($file, $this->makeView($args));
	}
	function makeView($override = []){
		return array_merge([
			'controller' => $this,
			'view' => $this->view,
			'template' => $this->template,
		], (array) $this->view, $override);
	}

}