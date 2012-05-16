<?php
/*
|--------------------------------------------------------------------------
| Account API
|--------------------------------------------------------------------------
|
| 
|
*/

// --------------------------------------------------------------
// Get all Accounts
// --------------------------------------------------------------
Route::get('account/all', function() {
	// Overriding default options with the "user-set" ones
	$options = array_merge(array(
		'offset' => 0,
		'limit' => 20,
		'sort_by' => 'accounts.name',
		'order' => 'ASC'
	), Input::all());

	// Add tablename prefix to sort_by, if set
	if(Input::has('sort_by'))
	{
		$options['sort_by'] = 'accounts.' . Input::get('sort_by');
	}

	// Preparing our query
	$accounts = Account::with(array('roles', 'roles.lang', 'language'));

	// Add where's to our query
	if(array_key_exists('search', $options))
	{
		foreach($options['search']['columns'] as $column)
		{
			$accounts = $accounts->or_where($column, '~*', $options['search']['string']);
		}
	}

	$total = (int) $accounts->count();

	// Add order_by, skip & take to our results query
	$accounts = $accounts->order_by($options['sort_by'], $options['order'])->skip($options['offset'])->take($options['limit'])->get();

	$response = array(
		'results' => to_array($accounts),
		'total' => $total,
		'pages' => ceil($total / $options['limit'])
	);

	return Response::json($response);
});

// --------------------------------------------------------------
// Get Account by id
// --------------------------------------------------------------
Route::get('account/(:num)', function($id) {
	// Get the Account
	$account = Account::with(array('roles', 'language'))->where_id($id)->first();
	
	if(is_null($account))
	{
		// Resource not found, return 404
		return Response::error(404);
	}

	return Response::eloquent($account);
});

// --------------------------------------------------------------
// Add Account
// --------------------------------------------------------------
Route::post('account', function() {
	// We are adding an account, password is accessible and required
	Account::$rules['password'] = 'required';
	Account::$rules['email'] .= '|unique:accounts,email';
	Account::$accessible[] = 'password';

	// Create a new Account object
	$account = new Account(Input::all());

	// Remove empty keys (multiple's first option)
	$role_ids = Input::has('role_ids') ? array_filter(array_flip(Input::get('role_ids')), 'strlen') : array();

	// Sync the roles (attach & detach the appropiate ones)
	$account->roles()->sync($role_ids);

	// Try to save
	if($account->save() === false)
	{
		// Return 400 response with errors
		return Response::json((array) $account->errors->messages, 400);
	}
	else
	{
		// Return the account's id
		return Response::json($account->get_key());
	}
});

// --------------------------------------------------------------
// Edit Account
// --------------------------------------------------------------
Route::put('account/(:num)', function($id) {
	// Find the account we are updating
	$account = Account::find($id);

	if(is_null($account))
	{
		// Resource not found, return 404
		return Response::error(404);
	}

	// If the password is set, we allow it to be updated
	if(Input::get('password') !== '') Account::$accessible[] = 'password';

	// Fill the account with the PUT data
	$account->fill(Input::all());

	// Remove empty keys (multiple's first option)
	$role_ids = Input::has('role_ids') ? array_filter(array_flip(Input::get('role_ids')), 'strlen') : array();

	// Sync the roles (attach & detach the appropiate ones)
	$account->roles()->sync($role_ids);

	// Try to save
	if($account->save() === false)
	{
		return Response::json((array) $account->errors->messages, 400);
	}
});

// --------------------------------------------------------------
// Delete Account
// --------------------------------------------------------------
Route::delete('account/(:num)', function($id) {
	// Find the account we are updating
	$account = Account::find($id);

	if(is_null($account))
	{
		// Resource not found, return 404
		return Response::error(404);
	}

	$account->delete();
});