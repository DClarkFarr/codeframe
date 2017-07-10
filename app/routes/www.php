<?php

$router = Router::get();

Config::put('locale', 'en');


$router->global('{locale}', $router->components->uri, function($route, $router){
	$params = $route->component->payload->params;
	if(!empty($params['locale'])){
		Config::put('locale', $params['locale']);
		$router->components->uri = $route->unused();
	}

})->where('locale', '^[a-z]{2}$')->run();

Route::get('/', $router->components->uri)->register('home');

Route::get('your-mom', $router->components->uri)->register('your.mom');


Route::get('blog/{your-mom}', $router->components->uri)
	->where(['your-mom' => '\w+'])
	->register('blog')
	->controller(function($route){
		return [true, [
			'class' => 'Controllers\Blog\PostsController',
			'action' => 'secondAction',
			'segments' => $route->unmatched(),
		]];
	});

$router->group('blog', $router->components->uri, function($route, $router){
	$res = Route::get('posts/{id}', $route->unused())
		->chain($route)
		->where(['id' => '\d+'])
		->register('posts')
		->controller('Controllers\Blog\PostsController@secondAction');
})->run();

Route::get('news', $router->components->uri)->register('news');

$router->route()->put('about-us', $router->components->uri)->register('about');

$router->MVCtoController($router->components->uri, 'www');

/*
$router2 = new Router('alterny');
$router2->route()->delete('customers/{id}', $router->components->uri)->where(['id' => '\d+']);

echo "<pre>";
	print_r($router2);
echo "</pre>";
*/