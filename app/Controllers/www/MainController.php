<?php 
namespace Controllers\www;

use Controllers\Controller;

class MainController extends Controller {
	function __construct(){
		parent::__contruct();
		echo "<br> I'm a controller gost";
	}
}
