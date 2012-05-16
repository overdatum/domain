<?php

// --------------------------------------------------------------
// Load bundles
// --------------------------------------------------------------
$bundles = require __DIR__ . DS . 'bundles'.EXT;
foreach ($bundles as $bundle => $config)
{
	Bundle::register($bundle, $config);
	if($config['auto'])
	{
		Bundle::start($bundle);
	}
}

// --------------------------------------------------------------
// Load directories
// --------------------------------------------------------------
Autoloader::directories(array(
	__DIR__.DS.'models',
));

// --------------------------------------------------------------
// Load namespaces
// --------------------------------------------------------------
Autoloader::namespaces(array(
	'Layla\\Domain' => __DIR__.DS.'libraries',
));

// --------------------------------------------------------------
// Filters
// --------------------------------------------------------------
Route::filter('authority', function($resource)
{
	$action = Request::$route->parameters['0'];
	if(Authority::cannot($action, $resource))
	{
		return Response::make('', 401);
	}
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::make('', 401);
});

// --------------------------------------------------------------
// Setting system tables
// --------------------------------------------------------------
DBManager::$hidden = Config::get('layla_domain::dbmanager.hidden');

// --------------------------------------------------------------
// Set Aliases
// --------------------------------------------------------------
Autoloader::alias('Layla\\Domain\\Model', 'Eloquent');

// --------------------------------------------------------------
// Load Routes
// --------------------------------------------------------------
require __DIR__.DS.'routes'.DS.'account'.EXT;
require __DIR__.DS.'routes'.DS.'dbmanager'.EXT;
require __DIR__.DS.'routes'.DS.'language'.EXT;
require __DIR__.DS.'routes'.DS.'layout'.EXT;
require __DIR__.DS.'routes'.DS.'media'.EXT;
require __DIR__.DS.'routes'.DS.'page'.EXT;
require __DIR__.DS.'routes'.DS.'role'.EXT;
require __DIR__.DS.'routes'.DS.'auth'.EXT;