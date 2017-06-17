<?php

$router = Router::get();

Config::put('locale', 'en');

$router->global('{locale}', $router->components->uri, function(&$uri, $unused_uri, $params, $route){
	Config::put('locale', $params['locale']);
	$uri = $unused_uri;
})->where('locale', '^[a-z]{2}$');

Route::get('your-mom');

Route::post('your-mom')->where('id', 'faceit');

$router->group('blog', $router->components->uri, function(&$uri, $unused_uri, $params, $route){
	
	Route::get('posts/{id}', $unused_uri)->chain($route)->where(['id' => '\d+']);

});

Route::get('news', $router->components->uri);

$router->route()->put('about-us', $router->components->uri);


/*
$router2 = new Router('alterny');
$router2->route()->delete('customers/{id}', $router->components->uri)->where(['id' => '\d+']);

echo "<pre>";
	print_r($router2);
echo "</pre>";
*/