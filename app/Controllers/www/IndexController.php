<?php 
namespace Controllers;

use Controllers\Controller;

class IndexController extends Controller {
	
	public $extends = 'www';

	function indexAction(){

		$this->template->script('/js/driver.js');
		
		return $this->view('www.index.index');
	}
}
