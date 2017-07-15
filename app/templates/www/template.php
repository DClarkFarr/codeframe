<?php 
namespace Templates;

class Www extends \Template {

	public function getRoot(){
		return __DIR__;
	}

	function revolution(){
		$this->style("/assets/js/plugins/revolution/css/settings.css");
		$this->style("/assets/js/plugins/revolution/css/layers.css");
		$this->style("/assets/js/plugins/revolution/css/navigation.css");

		$this->script("/assets/js/plugins/revolution/js/jquery.themepunch.tools.min.js");
		$this->script("/assets/js/plugins/revolution/js/jquery.themepunch.revolution.min.js");
	}
	function core(){
		$this->style("/assets/css/bootstrap.css");
		$this->style("/assets/css/font-awesome.min.css");
		$this->style("/assets/css/elegant-icons.css");

		$this->script("/assets/js/jquery-2.1.1.min.js", 10, true);
		$this->script("/assets/js/bootstrap.min.js", 10, true);
	}
	function main(){
		$this->style("/assets/css/main.css");
		$this->style("/assets/css/my-custom-styles.css");
		$this->style("/https://fonts.googleapis.com/css?family=Raleway:700,400,400italic,500");
		$this->style("/https://fonts.googleapis.com/css?family=Lato:400,400italic,700,300,300italic");	
		
		$this->script("/assets/js/plugins/owl-carousel/owl.carousel.min.js");
		$this->script("/assets/js/bravana.js", 11);
	}
	function plugins(){
		$this->script("/assets/js/plugins/easing/jquery.easing.min.js");
		$this->script("/assets/js/plugins/countto/jquery.countTo.js");
		$this->script("/assets/js/plugins/jquery-waypoints/jquery.waypoints.min.js");
		$this->script("/assets/js/plugins/parsley-validation/parsley.min.js");
	}

	function initialize(){
		$this->addTheme('main', [
			'header' => 'www.header',
			'head' => 'head.php',
			'sidebar' => 'sidebar.php',
			'footer' => 'footer.php',
			'body' => 'body.php',
		], function($view_params){
			//execute theme specific initializer, like adding scripts
			$this->core();
			$this->main();
			$this->plugins();
		});
		
	}
}