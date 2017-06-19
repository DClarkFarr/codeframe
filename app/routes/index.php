<?php

$router = Router::get();

Config::put('locale', 'en');


$router->global('{locale}', function($route, $router){
	
	$params = $route->component->payload->params;
	if(!empty($params['locale'])){
		Config::put('locale', $params['locale']);
		$router->components->uri = $route->unused();
	}

})->where('locale', '^[a-z]{2}$')->callback();

Route::get('/');

Route::get('your-mom')->register('your.mom');

Route::get('blog/{your-mom}')
	->where(['your-mom' => '\w+'])
	->register('blog')
	->controller(function($route){
		return [true, [
			'class' => 'Controllers\Blog\PostsController',
			'action' => 'secondAction',
			'segments' => $route->unmatched(),
		]];
	});

$router->group('blog', function($route, $router){
	
	Route::get('posts/{id}', $route->unused())
		->chain($route)
		->where(['id' => '\d+'])
		->register('posts');

})->callback();

Route::get('news')->register('news');

$router->route()->put('about-us')->register('about');


/*
$router2 = new Router('alterny');
$router2->route()->delete('customers/{id}', $router->components->uri)->where(['id' => '\d+']);

echo "<pre>";
	print_r($router2);
echo "</pre>";
*/