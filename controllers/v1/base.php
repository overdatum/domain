<?php

/**
* 
*/
class Domain_Base_Controller extends Controller
{
	/**
	 * Enable restful routing
	 *
	 * @var bool
	 **/
	public $restful = true;

	public $model;

	public $join = array();

	public $options = array();

	protected function model($id = null)
	{
		if( ! is_null($id))
		{
			$this->model = $this->model->where_id($id)->first();
			if(is_null($this->model))
			{
				// Resource not found, return 404
				Response::error(404)->send();
				exit;
			}
		}

		return $this->model;
	}

	public function get_multiple()
	{
		// Overriding default options with the "user-set" ones
		$options = array_merge(array(
			'offset' => 0,
			'limit' => 20,
			'sort_by' => 'name',
			'order' => 'ASC'
		), Input::all(), $this->options);

		// Preparing our query
		$results = $this->model->with($this->includes);

		if(count($this->join) > 0)
		{
			foreach ($this->join as $table => $settings) {
				$results = $results->join($table, $settings['join'][0], $settings['join'][1], $settings['join'][2]);
				if($settings['columns'])
				{
					$options['sort_by'] = (in_array($options['sort_by'], $settings['columns']) ? $table : $this->model->table()).'.'.$options['sort_by'];
				}
			}
		}
		
		if(stripos($options['sort_by'], '.') === 0)
		{
			$options['sort_by'] = $this->model->table().'.' . $options['sort_by'];
		}
		
		// Add where's to our query
		if(array_key_exists('search', $options))
		{
			foreach($options['search']['columns'] as $column)
			{
				$results = $results->or_where($column, '~*', $options['search']['string']);
			}
		}

		$total = (int) $results->count();

		// Add order_by, skip & take to our results query
		$results = $results->order_by($options['sort_by'], $options['order'])->skip($options['offset'])->take($options['limit'])->get();

		$response = array(
			'results' => to_array($results),
			'total' => $total,
			'pages' => ceil($total / $options['limit'])
		);

		return Response::json($response);
	}

	public function get_single($id)
	{
		// Get the Result
		$result = $this->model->with($this->includes)->where_id($id)->first();
		
		if(count($this->join) > 0)
		{
			foreach ($this->join as $table => $join) {
				$result = $result->join($table, $join[0], $join[1], $join[2]);
			}
		}

		if(is_null($result))
		{
			// Resource not found, return 404
			return Response::error(404);
		}

		return Response::eloquent($result);	
	}

	public function create_single($data, $sync = array())
	{
		$model = $this->model;
		
		// Create a new Object
		$model->fill($data);

		// Try to save
		if($model->save() === false)
		{
			// Return 400 response with errors
			return Response::json((array) $model->errors->messages, 400);
		}
		else
		{
			foreach ($sync as $relationship => $relation_ids)
			{
				$model->$relationship()->sync($relation_ids ? array_filter(array_flip($relation_ids), 'strlen') : array());	
			}
			
			// Return the model's id
			return Response::json($model->get_key());
		}
	}

	public function update_single($data, $sync = array())
	{
		$model = $this->model;
		
		// Create a new Object
		$model->fill($data);

		foreach ($sync as $relationship => $relation_ids)
		{
			$model->$relationship()->sync($relation_ids ? array_filter(array_flip($relation_ids), 'strlen') : array());	
		}	

		// Try to save
		if($model->save() === false)
		{
			return Response::json((array) $model->errors->messages, 400);
		}
	}

	public function delete_single()
	{
		$this->model->delete();
	}

	/**
	 * Catch-all method for requests that can't be matched.
	 *
	 * @param  string    $method
	 * @param  array     $parameters
	 * @return Response
	 */
	public function __call($method, $parameters)
	{
		return Response::error('404');
	}

}