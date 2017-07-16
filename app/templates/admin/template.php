<?php 
namespace Templates;

class Admin extends \Template {

	public function getRoot(){
		return __DIR__;
	}
	
	function core(){

		$this->style('/css/bootstrap.css', 1);
		$this->style('/css/font-awesome.css', 2);
		$this->style('/css/animate.css', 3);

		
		$this->script('/js/jquery.min.js', 0, true);
		$this->script('/js/jquery.migrate.js', 1, true);
		$this->script('/js/jquery.appear.js', 1);
		$this->script('/js/bootstrap.js', 2);
	}
	function main(){
		$this->style('http://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic');
		$this->style('/css/style.css', 11);
		$this->style('/css/responsive.css', 12);
		$this->style('/css/custom.css', 15);

		$this->script('/js/script.js', 11);
	}
	function plugins(){
		$this->style('/css/magnific-popup.css');
		$this->style('/css/owl.carousel.css');
		$this->style('/css/owl.theme.css');
		$this->style('/css/jquery.bxslider.css');

		$this->script('/js/jquery.magnific-popup.min.js');
		$this->script('/js/owl.carousel.min.js');
		$this->script('/js/retina-1.1.0.min.js');
		$this->script('/js/jquery.bxslider.min.js');
		$this->script('/js/plugins-scroll.js');
		$this->script('/js/waypoint.min.js');


	}
	function revolution(){
		$this->style('/css/fullwidth.css');
		$this->style('/css/settings.css');

		$this->script('/js/jquery.themepunch.revolution.min.js');
	}

	function initialize(){
		$this->addTheme('main', [
			'header' => 'header.php',
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