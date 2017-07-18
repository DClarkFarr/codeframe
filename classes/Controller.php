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
		<style type='text/css'>
		  #fof{display:block; width:100%; padding:150px 0; line-height:1.6em; text-align:center;}
		  #fof .hgroup{display:block; width:80%; margin:0 auto; padding:0;}
		  #fof .hgroup h1, #fof .hgroup h2{margin:0 0 0 40px; padding:0; float:left; text-transform:uppercase;}
		  #fof .hgroup h1{margin-top:-90px; font-size:200px;}
		  #fof .hgroup h2{font-size:60px;}
		  #fof .hgroup h2 span{display:block; font-size:30px;}
		  #fof p{margin:25px 0 0 0; padding:0; font-size:16px;}
		  #fof p:first-child{margin-top:0;}
		</style>
		<div class="wrapper row2">
		  <div id="container" class="clear">
		    <section id="fof" class="clear">
		      <div class="hgroup clear">
		        <h1>404</h1>
		        <h2>Error ! <span>Page Not Found</span></h2>
		      </div>
		      <p>For Some Reason The Page You Requested Could Not Be Found On Our Server</p>
		      <p><a href="javascript:history.go(-1)">&laquo; Go Back</a> / <a href="#">Go Home &raquo;</a></p>
		    </section>
		  </div>
		</div>
		<?php
		return ob_get_clean();
	}

}