<?php 
namespace Controllers;

use Admin\BaseController;

class IndexController extends BaseController {

	function indexAction(){		
		$this->template->revolution();
		$this->template->script('/js/admin.index.js');
		
		return $this->template->make('admin.index.index', $this->makeView());
	}
	function aboutUsAction(){

		return $this->template->make('admin.index.about-us', $this->makeView());
	}
}
