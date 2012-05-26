<?php
// --------------------------------------------------------------
// Load helpers
// --------------------------------------------------------------
require __DIR__.DS.'helpers'.EXT;

// --------------------------------------------------------------
// Load bundles
// --------------------------------------------------------------
Bundle::start('layla_thirdparty_dbmanager');
Bundle::start('layla_thirdparty_bootsparks');

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

$api_version = Config::get('layla.domain.api.version');

// --------------------------------------------------------------
// Map the Base Controller
// --------------------------------------------------------------
Autoloader::map(array(
	'Layla_Domain_Base_Controller' => __DIR__.DS.'controllers'.DS.'v'.$api_version.DS.'base'.EXT,
));

Route::filter('api_auth', function()
{
	if( ! Input::get('PHP_AUTH_USER') || ! Input::get('PHP_AUTH_PW'))
	{
		return Response::make('{}', 401, array('content-type' => 'text/plain'));
	}
});

// --------------------------------------------------------------
// Load the routes
// --------------------------------------------------------------
Route::group(array('before' => 'api_auth'), function() use ($api_version)
{
	Route::get('v'.$api_version.'/account/all', 'layla_domain::v'.$api_version.'.account@account_all');
	Route::get('v'.$api_version.'/account/(:num)', 'layla_domain::v'.$api_version.'.account@account');
	Route::post('v'.$api_version.'/account', 'layla_domain::v'.$api_version.'.account@account');
	Route::put('v'.$api_version.'/account/(:num)', 'layla_domain::v'.$api_version.'.account@account');
	Route::delete('v'.$api_version.'/account/(:num)', 'layla_domain::v'.$api_version.'.account@account');

	Route::get('v'.$api_version.'/page/all', 'layla_domain::v'.$api_version.'.page@page_all');
	Route::get('v'.$api_version.'/page/(:num)', 'layla_domain::v'.$api_version.'.page@page');
	Route::post('v'.$api_version.'/page', 'layla_domain::v'.$api_version.'.page@page');
	Route::put('v'.$api_version.'/page/(:num)', 'layla_domain::v'.$api_version.'.page@page');
	Route::delete('v'.$api_version.'/page/(:num)', 'layla_domain::v'.$api_version.'.page@page');

	Route::get('v'.$api_version.'/language/all', 'layla_domain::v'.$api_version.'.language@language_all');
	Route::get('v'.$api_version.'/language/(:num)', 'layla_domain::v'.$api_version.'.language@language');
	Route::post('v'.$api_version.'/language', 'layla_domain::v'.$api_version.'.language@language');
	Route::put('v'.$api_version.'/language/(:num)', 'layla_domain::v'.$api_version.'.language@language');
	Route::delete('v'.$api_version.'/language/(:num)', 'layla_domain::v'.$api_version.'.language@language');

	Route::get('v'.$api_version.'/layout/all', 'layla_domain::v'.$api_version.'.layout@layout_all');
	Route::get('v'.$api_version.'/layout/(:num)', 'layla_domain::v'.$api_version.'.layout@layout');
	Route::post('v'.$api_version.'/layout', 'layla_domain::v'.$api_version.'.layout@layout');
	Route::put('v'.$api_version.'/layout/(:num)', 'layla_domain::v'.$api_version.'.layout@layout');
	Route::delete('v'.$api_version.'/layout/(:num)', 'layla_domain::v'.$api_version.'.layout@layout');

	Route::get('v'.$api_version.'/role/all', 'layla_domain::v'.$api_version.'.role@role_all');
	Route::get('v'.$api_version.'/role/(:num)', 'layla_domain::v'.$api_version.'.role@role');
	Route::post('v'.$api_version.'/role', 'layla_domain::v'.$api_version.'.role@role');
	Route::put('v'.$api_version.'/role/(:num)', 'layla_domain::v'.$api_version.'.role@role');
	Route::delete('v'.$api_version.'/role/(:num)', 'layla_domain::v'.$api_version.'.role@role');
});