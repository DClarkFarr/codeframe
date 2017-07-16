<?php 
namespace Admin;
use Controllers\Controller;

class BaseController extends Controller {
	public $extends = 'admin';
	public $theme = 'main';

	function initialize(){
		$this->template = $this->template->load($this->extends);
		$this->template->theme($this->theme);

	}
}
