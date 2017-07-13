<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('APPLICATION_ROOT', realpath(__DIR__ . '/../../app/'));
define('APP_PATH', APPLICATION_ROOT . '/App.php');
define('ROUTES_PATH', APPLICATION_ROOT . '/routes/www.php');
define('CONTROLLERS_PATH', APPLICATION_ROOT . '/Controllers/www');
define('DOCUMENT_ROOT', __DIR__);

include __DIR__ . '/../../app/app-load.php';

App::bootstrap();

Router::get()->dispatch();
