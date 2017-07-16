<?php

$router = Router::get();

/*
Config::put('locale', 'en');

$router->global('{locale}', $router->components->uri, function($route, $router){
	$params = $route->component->payload->params;
	if(!empty($params['locale'])){
		Config::put('locale', $params['locale']);
		$router->components->uri = $route->unused();
	}

})->where('locale', '^[a-z]{2}$')->run();

Route::get('/', $router->components->uri)->register('home');
*/
$router->MVCtoController($router->components->uri, 'admin');
