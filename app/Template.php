<?php 

class Template {
	public $template;

	public $assets;

	public $themes = [];

	public $default_theme = 'default';

	public $parts;

	public $template_root;

	public function __construct($template = null, $callback = null){
		$this->template = $template;

		$this->parts = (object) [];

		$this->asssets = (object) [
			'js' => [],
			'css' => [],
		];
		
		$this->template_root = $this->getRoot();

		if(is_callable($callback)){
			$callback = $callback->bindTo($this);
			$callback();
		}

		$this->initialize();
	}

	public function getRoot(){
		return __DIR__;
	}

	public function initialize(){

		$this->addTheme($this->default_theme, [
			'header' => 'www.header',
			'head' => 'head.php',
			'sidebar' => 'sidebar.php',
			'footer' => 'footer.php',
			'body' => 'body.php',
		], function($view_params){
			//execute theme specific initializer, like adding scripts
		});
	}
	public function addTheme($name, $parts, $callback = null){
		$this->themes[$name] = (object) ['parts' => $parts, 'callback' => $callback];
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

		$dir = !strstr('/', $template_dir) ? Config::get('app.paths.templates') . '/' . $template_dir : $template_dir;

		if(!is_dir($dir)){
			return false;
		}

		$template_path = $dir . '/' . 'template.php';

		if(!is_file($template_path)){
			return false;
		}

		$class = 'Templates\\' . ucfirst($template_dir);

		if(!class_exists($class)){
			include $template_path;
		}

		if(!class_exists($class)){
			return false;
		}

		$assets = $this->assets;

		$template = new $class(null, function() use ($assets){	
			$this->assets = $assets;
		});

		return $template;
	}

	function make($file, $params, $theme_name = null){
		if(!$theme_name){
			$theme_name = $this->default_theme;
		}

		if(!isset($this->themes[$theme_name])){
			return false;
		}

		$theme = $this->themes[$theme_name];

		$resolvers = $theme->parts;
		$callback = $theme->callback;

		if(is_callable($callback)){
			$callback($params);
		}

		$this->parts->view = view($file, $params);

		if($resolvers){
			foreach($resolvers as $key => $resolver){
				if(is_callable($resolver)){
					$this->parts->$key = $resolver($params);
				}else if(is_string($resolver)){
					$basename = basename($resolver);
					$ext = pathinfo($basename, PATHINFO_EXTENSION);

					$dirname = substr($resolver, 0, - strlen($basename));

					$filepath = $resolver;
					if($ext == 'php' && ((strlen($dirname) && $dirname[0] != '/' && strpos($dirname, '/') !== false) || !$dirname)){
						$filepath = $this->template_root . '/' . $resolver;
					}
					
					$this->parts->$key = view($filepath, $params, 'templates');
				}
			}

			return $this->parts->$key;
		}

		return false;
	}
}