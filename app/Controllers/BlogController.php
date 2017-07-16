<?php
namespace Controllers;

use Controllers\BaseController;

class BlogController extends BaseController {
	function postssAction(){
		return 'echo posts magic is: ' . $this->get('your-mom');
	}
}