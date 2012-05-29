<?php

class Domain_V1_Auth_Controller extends Domain_Base_Controller {
	
	/**
	 * Login
	 *
	 * @return Response
	 */
	public function post_login()
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
	}

}