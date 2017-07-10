<?php 

include(__DIR__ . '/../vendor/autoload.php');

include __DIR__ . '/lib/autoload.php';

if(defined('FILE_APP') && is_file(FILE_APP)){
	include FILE_APP;
}else{
	throw new Exception("FILE APP not defined, or file did not exist");
}
include __DIR__ . '/Cache.php';

include __DIR__ . '/Config.php';

include __DIR__ . '/Controller.php';

include __DIR__ . '/DB.php';

include __DIR__ . '/Route.php';

include __DIR__ . '/Router.php';

include __DIR__ . '/Session.php';

include __DIR__ . '/Uri.php';
