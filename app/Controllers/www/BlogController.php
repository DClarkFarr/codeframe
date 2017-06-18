<?php
namespace Controllers;

use Controllers\Controller;
class BlogController extends Controller {
	function postssAction(){
		return 'echo posts magic is: ' . $this->get('your-mom');
	}
}