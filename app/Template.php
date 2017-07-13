<?php 

class Template {
	public $template;

	public $assets;

	public function __construct($template = null){
		$this->template = $template;

		$this->asssets = (object) [
			'js' => [],
			'css' => [],
		];
	}

	public function initialize(){

	}
	public function script($filepath, $priority = 10, $head = false){
		$this->assets->js[$priority][] = [$filepath, $head, 'path'];
	}
	public function style($filepath, $priority = 10){
		$this->assets->css[$priority][] = [$filepath, 'path'];
	}
	public function inlineScript($filepath, $priority = 10, $in_head = false){
		$this->assets->js[$priority][] = [$filepath, $in_head, 'inline'];
	}
	public function inlineStyle($filepath, $priority = 10){
		$this->assets->css[$priority][] = [$filepath, 'inline'];
	}

	public function scripts($head = false){
		$scripts = $this->assets->js;
		ksort($scripts);

		$html = "";
		foreach($scripts as $priority => $arr){
			foreach($arr as list($filepath, $in_head, $type)){
				if(!(($in_head && $head) || (!$in_head && !$head))){
					continue;
				}
				if($type == 'path'){
					$html .= "<script type='text/javascript' src='". $filepath ."'></script>\n";
				}else if($type == 'inline'){
					$html .= "<script type='text/javascript'>". $filepath ."</script>\n";
				}
			}
		}
		return $html;
	}

	public function styles(){
		$styles = $this->assets->css;
		ksort($styles);

		$html = "";
		foreach($styles as $priority => $arr){
			foreach($arr as list($filepath, $type)){
				if($type == 'path'){
					$html .= "<link rel='stylesheet' href='". $filepath ."'>\n";
				}else if($type == 'inline'){
					$html .= "<style type='text/css'>". $filepath . "</style>\n";
				}
			}
		}

		return $html;
	}

	function load($template_dir){

	}

	function make($file, $params){
		echo $view = view($file, $params);
	}
}