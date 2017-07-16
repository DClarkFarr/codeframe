<?php 
namespace Controllers;

use Controllers\BaseController;

class IndexController extends BaseController {

	function indexAction(){		

		return $this->view('index.index', $this->makeView());
	}
}
