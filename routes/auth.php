<?php
/*
|--------------------------------------------------------------------------
| Auth API
|--------------------------------------------------------------------------
|
| 
|
*/

// --------------------------------------------------------------
// Authenticate
// --------------------------------------------------------------
Route::post('auth/login', function()
{
	$rules = array(
		'email' => 'required|email',
		'password' => 'required',
	);

	$validator = new Validator(Input::all(), $rules);
	if ( ! $validator->valid())
	{
		return Response::json((array) $validator->errors->messages, 400);
	}
	
	if (Auth::attempt(Input::get('email'), Input::get('password')))
	{
		return Response::json(Auth::$token);
	}

	return Response::error(401);
});