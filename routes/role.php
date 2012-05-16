<?php
/*
|--------------------------------------------------------------------------
| Role API
|--------------------------------------------------------------------------
|
| 
|
*/

// --------------------------------------------------------------
// Get all Roles
// --------------------------------------------------------------
Route::get('role/all', function() {
	// Overriding default options with the "user-set" ones
	$options = array_merge(array(
		'offset' => 0,
		'limit' => 20,
		'sort_by' => 'roles.name',
		'order' => 'ASC'
	), Input::all());

	// Add tablename prefix to sort_by, if set
	if(Input::has('sort_by'))
	{
		$options['sort_by'] = 'roles.' . Input::get('sort_by');
	}

	// Preparing our query
	$roles = Role::with('lang');

	// Add where's to our query
	if(array_key_exists('search', $options))
	{
		foreach($options['search']['columns'] as $column)
		{
			$roles = $roles->or_where($column, '~*', $options['search']['string']);
		}
	}

	$total = (int) $roles->count();

	// Add order_by, skip & take to our results query
	$roles = $roles->order_by($options['sort_by'], $options['order'])->skip($options['offset'])->take($options['limit'])->get();

	$response = array(
		'results' => to_array($roles),
		'total' => $total,
		'pages' => ceil($total / $options['limit'])
	);

	return Response::json($response);
});

// --------------------------------------------------------------
// Get Role by id
// --------------------------------------------------------------
Route::get('role/(:num)', function($id) {
	// Get the role
	$role = Role::where_id($id)->first();
	
	if(is_null($role))
	{
		// Resource not found, return 404
		return Response::error(404);
	}

	return Response::eloquent($role);
});

// --------------------------------------------------------------
// Add Role
// --------------------------------------------------------------
Route::post('role', function() {
	// Create a new role object
	$role = new Role(Input::all());

	// Try to save
	if( ! $role->save())
	{
		// Return 400 response with errors
		return Response::json((array) $role->errors->messages, 400);
	}
	else
	{
		// Return the role's id
		return Response::json($role->get_key());
	}
});

// --------------------------------------------------------------
// Edit Role
// --------------------------------------------------------------
Route::put('role/(:num)', function($id) {
	// Find the role we are updating
	$role = Role::find($id);

	if(is_null($role))
	{
		// Resource not found, return 404
		return Response::error(404);
	}

	// Fill the role with the PUT data
	$role->fill(Input::all());

	// Try to save
	if( ! $role->save())
	{
		return Response::json((array) $role->errors->messages, 400);
	}
});

// --------------------------------------------------------------
// Delete Role
// --------------------------------------------------------------
Route::delete('role/(:num)', function($id) {
	// Find the role we are updating
	$role = Role::find($id);

	if(is_null($role))
	{
		// Resource not found, return 404
		return Response::error(404);
	}

	$role->delete();
});