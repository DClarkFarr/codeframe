<?php 
namespace Controllers\Blog;

use Controllers\Controller;

class PostsController {
	function indexAction(){
		return 'hey index of posts';
	}
	function secondAction(){
		return 'hey posts second action';
	}
	function categoriesAction(){
		return 'hey posts categories';
	}
}