<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


include __DIR__ . '/../app/app-load.php';

App::bootstrap();

//$r = new Uri('/home/ajax/contact-us', '/home/{method}/{view?}');
//$r->where('method', 'ajax|post|get')->where(['view' => 'your-mom']);

//echo "<pre>";
//print_r($r->matched());
//echo "</pre>";
//Session::put(['car' => 'friend']);
//echo "<br>session = " . Session::get('car');

Router::get()->dispatch();
