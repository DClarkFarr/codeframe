<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . '/../../app/app-load.php';

App::bootstrap();

Router::get()->dispatch();

