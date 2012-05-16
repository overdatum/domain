<?php
/*
|--------------------------------------------------------------------------
| Language API
|--------------------------------------------------------------------------
|
| 
|
*/

// --------------------------------------------------------------
// Get all Languages
// --------------------------------------------------------------
Route::get('language/all', function() {
	// Overriding default options with the "user-set" ones
	$options = array_merge(array(
		'offset' => 0,
		'limit' => 20,
		'sort_by' => 'languages.name',
		'order' => 'ASC'
	), Input::all());

	// Add tablename prefix to sort_by, if set
	if(Input::has('sort_by'))
	{
		$options['sort_by'] = 'languages.' . Input::get('sort_by');
	}

	// Preparing our query
	$languages = new Language;

	// Add where's to our query
	if(array_key_exists('search', $options))
	{
		foreach($options['search']['columns'] as $column)
		{
			$languages = $languages->or_where($column, '~*', $options['search']['string']);
		}
	}

	$total = (int) $languages->count();

	// Add order_by, skip & take to our results query
	$languages = $languages->order_by($options['sort_by'], $options['order'])->skip($options['offset'])->take($options['limit'])->get();

	$response = array(
		'results' => to_array($languages),
		'total' => $total,
		'pages' => ceil($total / $options['limit'])
	);

	return Response::json($response);
});

// --------------------------------------------------------------
// Get Language by id
// --------------------------------------------------------------
Route::get('language/(:num)', function($id) {
	// Get the language
	$language = Language::where_id($id)->first();
	
	if(is_null($language))
	{
		// Resource not found, return 404
		return Response::error(404);
	}

	return Response::eloquent($language);
});

// --------------------------------------------------------------
// Add Language
// --------------------------------------------------------------
Route::post('language', function() {
	// Create a new language object
	$language = new Language(Input::all());

	// Try to save
	if( ! $language->save())
	{
		// Return 400 response with errors
		return Response::json((array) $language->errors->messages, 400);
	}
	else
	{
		// Return the language's id
		return Response::json($language->get_key());
	}
});

// --------------------------------------------------------------
// Edit Language
// --------------------------------------------------------------
Route::put('language/(:num)', function($id) {
	// Find the language we are updating
	$language = Language::find($id);

	if(is_null($language))
	{
		// Resource not found, return 404
		return Response::error(404);
	}

	// Fill the language with the PUT data
	$language->fill(Input::all());

	// Try to save
	if( ! $language->save())
	{
		return Response::json((array) $language->errors->messages, 400);
	}
});

// --------------------------------------------------------------
// Delete Language
// --------------------------------------------------------------
Route::delete('language/(:num)', function($id) {
	// Find the language we are updating
	$language = Language::find($id);

	if(is_null($language))
	{
		// Resource not found, return 404
		return Response::error(404);
	}

	$language->delete();
});