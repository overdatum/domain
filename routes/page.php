<?php
/*
|--------------------------------------------------------------------------
| Page API
|--------------------------------------------------------------------------
|
| 
|
*/

// --------------------------------------------------------------
// Get all pages
// --------------------------------------------------------------
Route::get('page/all', function() {
	// Overriding default options with the "user-set" ones
	$options = array_merge(array(
		'offset' => 0,
		'limit' => 20,
		'sort_by' => 'order',
		'order' => 'ASC'
	), Input::all());

	// Add tablename prefix to sort_by, if set
	if(Input::has('sort_by'))
	{
		$page_lang_columns = array('url', 'meta_title', 'meta_keywords', 'meta_description', 'menu', 'content');
		$options['sort_by'] = (in_array(Input::get('sort_by'), $page_lang_columns) ? 'page_lang' : 'pages').'.'.Input::get('sort_by');
	}

	// Preparing our query
	$pages = Page::with(array('account', 'lang'))->join('page_lang', 'pages.id', '=', 'page_lang.page_id');

	// Add where's to our query
	if(array_key_exists('search', $options))
	{
		foreach($options['search']['columns'] as $column)
		{
			$pages = $pages->or_where($column, '~*', $options['search']['string']);
		}
	}

	$total = (int) $pages->count();

	// Add order_by, skip & take to our results query
	$pages = $pages->order_by($options['sort_by'], $options['order'])->skip($options['offset'])->take($options['limit'])->get('pages.*');

	$response = array(
		'results' => to_array($pages),
		'total' => $total,
		'pages' => ceil($total / $options['limit'])
	);

	return Response::json($response);
});

// --------------------------------------------------------------
// Get page by id
// --------------------------------------------------------------
Route::get('page/(:num)', function($id) {
	// Get the Page
	$page = Page::with(array('layout', 'lang'))->where_id($id)->first();
	
	if(is_null($page))
	{
		// Resource not found, return 404
		return Response::error(404);
	}

	return Response::eloquent($page);
});

// --------------------------------------------------------------
// New page
// --------------------------------------------------------------
Route::post('page', function() {
	return;
});

// --------------------------------------------------------------
// Edit page by id
// --------------------------------------------------------------
Route::put('page/(:num)', function($id) {
	return;
});