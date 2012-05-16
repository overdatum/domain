<?php
/*
|--------------------------------------------------------------------------
| Layout API
|--------------------------------------------------------------------------
|
| 
|
*/

// --------------------------------------------------------------
// Get all Layouts
// --------------------------------------------------------------
Route::get('layout/all', function() {
	// Overriding default options with the "user-set" ones
	$options = array_merge(array(
		'offset' => 0,
		'limit' => 100,
		'sort_by' => 'layouts.name',
		'order' => 'ASC'
	), Input::all());

	// Add tablename prefix to sort_by, if set
	if(Input::has('sort_by'))
	{
		$options['sort_by'] = 'layouts.' . Input::get('sort_by');
	}

	// Preparing our query
	$layouts = new Layout;

	// Add where's to our query
	if(array_key_exists('search', $options))
	{
		foreach($options['search']['columns'] as $column)
		{
			$layouts = $layouts->or_where($column, '~*', $options['search']['string']);
		}
	}

	$total = (int) $layouts->count();

	// Add order_by, skip & take to our results query
	$layouts = $layouts->order_by($options['sort_by'], $options['order'])->skip($options['offset'])->take($options['limit'])->get();

	$response = array(
		'results' => to_array($layouts),
		'total' => $total,
		'pages' => ceil($total / $options['limit'])
	);

	return Response::json($response);
});

// --------------------------------------------------------------
// Get Layout by id
// --------------------------------------------------------------
Route::get('layout/(:num)', function($id) {
	// Get the layout
	$layout = Layout::where_id($id)->first();
	
	if(is_null($layout))
	{
		// Resource not found, return 404
		return Response::error(404);
	}

	return Response::eloquent($layout);
});

// --------------------------------------------------------------
// Add Layout
// --------------------------------------------------------------
Route::post('layout', function() {
	// Create a new layout object
	$layout = new Layout(Input::all());

	// Try to save
	if( ! $layout->save())
	{
		// Return 400 response with errors
		return Response::json((array) $layout->errors->messages, 400);
	}
	else
	{
		// Return the layout's id
		return Response::json($layout->get_key());
	}
});

// --------------------------------------------------------------
// Edit Layout
// --------------------------------------------------------------
Route::put('layout/(:num)', function($id) {
	// Find the layout we are updating
	$layout = Layout::find($id);

	if(is_null($layout))
	{
		// Resource not found, return 404
		return Response::error(404);
	}

	// Fill the layout with the PUT data
	$layout->fill(Input::all());

	// Try to save
	if( ! $layout->save())
	{
		return Response::json((array) $layout->errors->messages, 400);
	}
});

// --------------------------------------------------------------
// Delete Layout
// --------------------------------------------------------------
Route::delete('layout/(:num)', function($id) {
	// Find the layout we are updating
	$layout = Layout::find($id);

	if(is_null($layout))
	{
		// Resource not found, return 404
		return Response::error(404);
	}

	$layout->delete();
});