<?php

/**
 * Transform Eloquent models to a JSON object.
 *
 * @param  Eloquent|array  $models
 * @return object
 */
function to_array($models)
{
	if ($models instanceof Laravel\Database\Eloquent)
	{
		return $models->to_array();
	}

	return array_map(function($m) { return $m->to_array(); }, $models);
}