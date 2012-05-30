<?php

use Layla\DBManager;

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
	'Domain_Base_Controller' => __DIR__.DS.'controllers'.DS.'v'.$api_version.DS.'base'.EXT,
));

Route::filter('api_auth', function()
{
	if( ! isset($_SERVER['PHP_AUTH_USER']) || ! isset($_SERVER['PHP_AUTH_PW']))
	{
		//return Response::json(array(), 401);
	}

	//Auth::attempt();
});

// --------------------------------------------------------------
// Load the routes
// --------------------------------------------------------------
Route::group(array('before' => 'api_auth'), function() use ($api_version)
{
	Route::get('v'.$api_version.'/account/all', 'domain::v'.$api_version.'.account@account_all');
	Route::get('v'.$api_version.'/account/(:num)', 'domain::v'.$api_version.'.account@account');
	Route::post('v'.$api_version.'/account', 'domain::v'.$api_version.'.account@account');
	Route::put('v'.$api_version.'/account/(:num)', 'domain::v'.$api_version.'.account@account');
	Route::delete('v'.$api_version.'/account/(:num)', 'domain::v'.$api_version.'.account@account');

	Route::get('v'.$api_version.'/page/all', 'domain::v'.$api_version.'.page@page_all');
	Route::get('v'.$api_version.'/page/(:num)', 'domain::v'.$api_version.'.page@page');
	Route::post('v'.$api_version.'/page', 'domain::v'.$api_version.'.page@page');
	Route::put('v'.$api_version.'/page/(:num)', 'domain::v'.$api_version.'.page@page');
	Route::delete('v'.$api_version.'/page/(:num)', 'domain::v'.$api_version.'.page@page');

	Route::get('v'.$api_version.'/language/all', 'domain::v'.$api_version.'.language@language_all');
	Route::get('v'.$api_version.'/language/(:num)', 'domain::v'.$api_version.'.language@language');
	Route::post('v'.$api_version.'/language', 'domain::v'.$api_version.'.language@language');
	Route::put('v'.$api_version.'/language/(:num)', 'domain::v'.$api_version.'.language@language');
	Route::delete('v'.$api_version.'/language/(:num)', 'domain::v'.$api_version.'.language@language');

	Route::get('v'.$api_version.'/layout/all', 'domain::v'.$api_version.'.layout@layout_all');
	Route::get('v'.$api_version.'/layout/(:num)', 'domain::v'.$api_version.'.layout@layout');
	Route::post('v'.$api_version.'/layout', 'domain::v'.$api_version.'.layout@layout');
	Route::put('v'.$api_version.'/layout/(:num)', 'domain::v'.$api_version.'.layout@layout');
	Route::delete('v'.$api_version.'/layout/(:num)', 'domain::v'.$api_version.'.layout@layout');

	Route::get('v'.$api_version.'/role/all', 'domain::v'.$api_version.'.role@role_all');
	Route::get('v'.$api_version.'/role/(:num)', 'domain::v'.$api_version.'.role@role');
	Route::post('v'.$api_version.'/role', 'domain::v'.$api_version.'.role@role');
	Route::put('v'.$api_version.'/role/(:num)', 'domain::v'.$api_version.'.role@role');
	Route::delete('v'.$api_version.'/role/(:num)', 'domain::v'.$api_version.'.role@role');
});