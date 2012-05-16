<?php

Route::get('dbmanager/tables', function() {
	$response = DBManager::tables();
	return Response::json($response);
});

Route::get('dbmanager/table/(:any)', function($table) {
	$response = DBManager::table($table)->info();
	return Response::json($response);
});

Route::post('dbmanager/table', function() {
	$response = DBManager::new_table($input);
	return Response::json($response);
});