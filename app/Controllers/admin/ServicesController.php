<?php 
namespace Controllers;

use Admin\BaseController;

class ServicesController extends BaseController {

	function websitesAction(){
		return $this->template->make('admin.services.websites', $this->makeView());
	}
	function seoAction(){
		return $this->template->make('admin.services.seo', $this->makeView());
	}
	function marketingAction(){
		return $this->template->make('admin.services.marketing', $this->makeView());
	}
}
