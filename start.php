<?php

use Layla\DBManager;
use Layla\API;

// --------------------------------------------------------------
// Load helpers
// --------------------------------------------------------------
require __DIR__.DS.'helpers'.EXT;

// --------------------------------------------------------------
// Load bundles
// --------------------------------------------------------------
//Bundle::start('thirdparty_dbmanager');
Bundle::start('thirdparty_bootsparks');

// --------------------------------------------------------------
// Load namespaces
// --------------------------------------------------------------
Autoloader::namespaces(array(
	'Domain' => __DIR__,
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
DBManager::$hidden = Config::get('domain::dbmanager.hidden');


$api_version = Config::get('layla.domain.api.version');

// --------------------------------------------------------------
// Map the Base Controller
// --------------------------------------------------------------
Autoloader::map(array(
	'Domain_Base_Controller' => __DIR__.DS.'controllers'.DS.'base'.EXT,
));

Route::filter('api_auth', function()
{
	if( ! isset($_SERVER['PHP_AUTH_USER']) || ! isset($_SERVER['PHP_AUTH_PW']))
	{
		//return Response::json(array(), 401);
	}

	//Auth::attempt();
});

Bundle::start('thirdparty_bob');

// --------------------------------------------------------------
// Load the routes
// --------------------------------------------------------------
Route::group(array('before' => 'api_auth'), function() use ($api_version)
{
	Route::api(Config::get('routes'), 'domain', Config::get('layla.domain.url_prefix'));
});

// --------------------------------------------------------------
// Set aliases
// --------------------------------------------------------------
Autoloader::alias('Domain\\Libraries\\Response', 'Response');
