<?php 
namespace Controllers;

use Www\BaseController;

class IndexController extends BaseController {

	function indexAction(){		
		$this->template->revolution();

		return $this->template->make('www.index.index', $this->makeView());
	}
}
