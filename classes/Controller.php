<?php 
namespace Codeframe;

class Controller {
	var $route;

	var $get;

	var $view;

	var $template;
	var $theme = 'default';
	var $extends = 'default';
	
	function __construct($route){
		$this->route = $route;

		$this->get = (object) ['params' => [], 'uri' => []];

		$this->view = (object) [];

		$this->template = new Template($this->extends);

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
	function template($file, $args = [], $extends = null, $theme = null){
		if($extends === null){
			$extends = $this->extends;
		}
		if($theme === null){
			$theme = $this->theme;
		}
		if($extends){
			$res = $this->template->load($extends);

			if($res){
				$this->template = $res;
			}
		}
		if($theme){
			$this->template->theme($theme);
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

	function pageNotFound(){	
		return $this->html404();
	}
	function html404(){
		ob_start();
		?>
		<!doctype html>
		<html lang="en">
		<head>
		    <meta charset="utf-8">
		    <title>Page Not Found</title>
		    <meta name="viewport" content="width=device-width, initial-scale=1">
		    <style>
		        * {
		            line-height: 1.2;
		            margin: 0;
		        }
		        html {
		            color: #888;
		            display: table;
		            font-family: sans-serif;
		            height: 100%;
		            text-align: center;
		            width: 100%;
		        }
		        body {
		            display: table-cell;
		            vertical-align: middle;
		            margin: 2em auto;
		        }
		        h1 {
		            color: #555;
		            font-size: 2em;
		            font-weight: 400;
		        }
		        p {
		            margin: 0 auto;
		            width: 280px;
		        }
		        @media only screen and (max-width: 280px) {
		            body, p {
		                width: 95%;
		            }
		            h1 {
		                font-size: 1.5em;
		                margin: 0 0 0.3em;
		            }
		        }
		    </style>
		</head>
		<body>
		    <h1>Page Not Found</h1>
		    <p>Sorry, but the page you were trying to view does not exist.</p>
		</body>
		</html>
		<!-- IE needs 512+ bytes: https://blogs.msdn.microsoft.com/ieinternals/2010/08/18/friendly-http-error-pages/ -->
		<?php
		return ob_get_clean();
	}

}