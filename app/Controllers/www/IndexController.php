<?php 
namespace Controllers;

use Controllers\Controller;

class IndexController extends Controller {
	
	function indexAction(){
		
		return $this->view('www.index.index');
	}
}
