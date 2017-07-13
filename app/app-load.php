<?php 

include(__DIR__ . '/../vendor/autoload.php');

include __DIR__ . '/lib/autoload.php';

if(defined('APP_PATH') && is_file(APP_PATH)){
	include APP_PATH;
}else{
	throw new Exception("APP_PATH not defined, or file did not exist");
}
include __DIR__ . '/Cache.php';

include __DIR__ . '/Config.php';

include __DIR__ . '/Controller.php';

include __DIR__ . '/DB.php';

include __DIR__ . '/Route.php';

include __DIR__ . '/Router.php';

include __DIR__ . '/Template.php';

include __DIR__ . '/Session.php';

include __DIR__ . '/Uri.php';
