<?php 
namespace Www;
use Controllers\Controller;

class BaseController extends Controller {
	public $extends = 'www';
	public $theme = 'main';

	function initialize(){
		$this->template = $this->template->load($this->extends);
		$this->template->theme($this->theme);
	}
}
