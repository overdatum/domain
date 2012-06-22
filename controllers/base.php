<?php
/**
 * Part of the Domain API for Layla.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file licence.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@getlayla.com so I can send you a copy immediately.
 *
 * @package    Layla Domain
 * @version    1.0
 * @author     Koen Schmeets <koen@getlayla.com>
 * @license    MIT License
 * @link       http://getlayla.com
 */

use Layla\DBManager;

/**
* This Base Controller provides methods to setup a role based,
* CRUD DB layer over HTTP with almost very little effort
*/
class Domain_Base_Controller extends Controller
{
	/**
	 * Enable restful routing
	 *
	 * @var bool
	 **/
	public $restful = true;

	/**
	 * The model we are working with
	 * 
	 * @var Database\Eloquent\Model
	 */
	public $model;

	/**
	 * Indicates if a language table should be joined or included
	 * 
	 * The table will be joined in case there is no search or order
	 * on one of the table's columns
	 * The expected language table name is the model's name
	 * post-fixed with "_lang". For example "users" => "user_lang"
	 *
	 * @var bool
	 */
	public $multilanguage = false;

	/**
	 * Indicates if the main model is versioned
	 * 
	 * When turned on, upon saving the version will be "increased" and
	 * upon retrieval only the latest version will be returned
	 * 
	 * @var bool
	 */
	public $versioned = false;

	/**
	 * Indicates what tables should be joined (only for get_ methods)
	 * 
	 * 	<code>
	 * 		$this->joins[] = array(
	 * 			'table' => 'mobilenumbers',
	 * 			'join' => array('users.id', '=', 'mobilenumbers.user_id')
	 *		);
	 * </code>
	 * 
	 * @var array
	 */
	public $joins = array();

	/**
	 * Options are used for limiting the results on get requests
	 * And can be used to add custom data to the input on post / put requests
	 * 
	 * @todo make a seperate data array
	 * 
	 * @var array
	 */
	public $options = array();

	public $settings = array();

	public $mapped_columns = array();

	public $get_columns = array();

	/**
	 * Method for retrieving multiple records
	 * 
	 * @param array $input The user-defined options
	 */
	public function read_multiple($input)
	{
		// Default settings
		$this->settings = array_merge(array(
			'relating' => array(),
			'sortable' => array(),
			'searchable' => array(),
			'filterable' => array()
		), $this->settings);

		// Default options
		$this->options = array_merge(array(
			'offset' => 0,
			'limit' => 20,
			'sort_by' => 'name',
			'order' => 'ASC',
			'search' => array(
				'string' => '',
				'columns' => array()
			)
		), $this->options, $input);

		// Make sure we have a valid sort_by option
		$options = $this->options;

		$found = false;
		foreach ($this->settings['sortable'] as $table => $columns)
		{
			if(in_array($this->options['sort_by'], $columns))
			{
				$found = true;
				$this->options['sort_by'] = $table.'.'.$this->options['sort_by'];
			}
		}

		if( ! $found)
		{
			return Response::make("The specified sort_by option \"".$this->options['sort_by']."\" is not supported", 400);
		}

		// We are looking for something...
		if($this->options['search']['string'] !== '')
		{
			if( ! isset($this->options['search']['columns']))
			{
				return Response::make("Please specify the column that you want to search through", 400);
			}

			$unfound_columns = array();		
			foreach ($this->options['search']['columns'] as $i => $column)
			{
				$found = false;
				foreach ($this->settings['searchable'] as $table => $columns)
				{
					if (in_array($column, $columns))
					{
						$this->options['search']['columns'][$i] = $table.'.'.$column;
						$found = true;
					}
				}

				if( ! $found)
				{
					$unfound_columns[] = $column;
				}
			}
		
			$unfound_column_count = count($unfound_columns);

			// Make sure we have valid search columns
			if ($unfound_column_count > 0)
			{
				if($unfound_column_count == 1)
				{
					return Response::make("The specified search column \"".$unfound_columns[0]."\" is not supported", 400);
				}
				else
				{
					return Response::make("The specified search columns \"".implode(', ', $unfound_columns)."\" are not supported", 400);
				}
			}
		}

		// Eger-load relationships
		$query = $this->model->with($this->includes);

		// Get the table name
		$table = $this->model->table();

		// Initialize the array that will hold the columns that will be mapped to avoid conflicts
		$this->mapped_columns = array();

		// Initialize the array that will hold the columns we want to fetch
		$this->get_columns = array($table.'.*');

		// Multilanguage, lets check if we need to join the records (for search or sort)
		if ($this->multilanguage)
		{
			// Set the lowercase model name, without the namespace
			$model = strtolower(class_basename($this->model));

			// Set the language table anme
			$language_table = $model.'_lang';

			// Set the language table's foreign key
			$foreign_key = $model.'_id';

			// Find out if we need a join
			if ($this->needs_join($language_table) || $this->versioned)
			{
				// Add a join for the language table
				$this->joins[] = array(
					'table' => $language_table,
					'join' => array(function($join) use ($table, $language_table, $foreign_key)
					{
						$join->on($table.'.id', '=', $language_table.'.'.$foreign_key);
						$join->on($language_table.'.language_id', '=', 1);
					})
				);

				if($this->versioned)
				{
					// Add a left outer join to be able to only get
					// the language rows at the latest version
					$this->joins[] = array(
						'table' => $language_table.' AS max',
						'join' => array(function($join) use ($table, $language_table, $foreign_key)
						{
							$join->on($table.'.id', '=', 'max.'.$foreign_key);
							$join->on('max.version', '>', $language_table.'.version');
							$join->on('max.language_id', '=', 1);
						}, '', '', 'LEFT OUTER')
					);

					$this->get_columns[] = $language_table.'.version';

					$query = $query->where('max.version', 'IS', DB::raw('NULL'));
				}
			}
			
			else
			{
				$this->model->includes[] = 'lang';
			}
		}

		elseif($this->versioned)
		{
			$query = $query->where('version', '=',
				DB::raw('('.
					DB::table($table.' AS max')
						->select(DB::raw('MAX(version) AS version'))
						->where($table.'.id', '=', DB::raw('max.id'))
						->sql().
				')')
			);
		}

		$query = $this->apply_joins($query);

		// Add where's to our query
		if ($this->options['search']['string'] !== '')
		{
			$columns = $this->options['search']['columns'];
			$string = $this->options['search']['string'];
			$query = $query->where(function($query) use ($columns, $string)
			{
				foreach ($columns as $column)
				{
					$query->or_where($column, ' LIKE ', '%'.$string.'%');
				}
			});
		}

		$total = (int) $query->count();

		// Add order_by, skip & take to our results query
		$results = $query
			->order_by($this->options['sort_by'], $this->options['order'])
			->skip($this->options['offset'])
			->take($this->options['limit'])
			->get($this->get_columns);

		$response = array(
			'results' => to_array($results),
			'total' => $total,
			'pages' => ceil($total / $this->options['limit'])
		);

		foreach ($response['results'] as &$row)
		{
			$row = $this->apply_mappings($row);
		}

		return Response::json($response);
	}

	public function read($id, $input)
	{
		$this->options = array_merge($this->options, $input);

		$table = $this->model->table();

		// Set the lowercase model name, without the namespace
		$model = strtolower(class_basename($this->model));

		// Set the language table anme
		$language_table = $model.'_lang';

		// Set the language table's foreign key
		$foreign_key = $model.'_id';

		// Get the Result
		$query = $this->model->with($this->includes);

		$this->get_columns = array($table.'.*');

		$this->mapped_columns = array();

		if($this->multilanguage)
		{
			// Set the lowercase model name, without the namespace
			$model = strtolower(class_basename($this->model));

			// Set the language table anme
			$language_table = $model.'_lang';

			// Set the language table's foreign key
			$foreign_key = $model.'_id';

			$versioned = $this->versioned;

			$version = null;

			if($versioned)
			{
				$version = isset($this->options['version']) ? $this->options['version'] : DB::table($language_table)->where($foreign_key, '=', $this->model->id)->max('version');
			}

			// Add a join for the language table
			$this->joins[] = array(
				'table' => $language_table,
				'join' => array(function($join) use ($table, $language_table, $foreign_key, $versioned, $version)
				{
					$join->on($table.'.id', '=', $language_table.'.'.$foreign_key);
					$join->on($language_table.'.language_id', '=', 1);

					if($versioned)
					{
						$join->on($language_table.'.version', '=', DB::raw($version));
					}
				})
			);
		}
		else
		{
			if($this->versioned)
			{
				$version = isset($this->options['version']) ? $this->options['version'] : $this->model()->where_id($this->model->id)->max('version');
	
				$query = $query->where($table.'.version', '=', $version);
			}
		}

		$query = $this->apply_joins($query);

		$query = $this->find($query, $id);

		$result = $query->first($this->get_columns);

		if(is_null($result))
		{
			// Resource not found, return 404
			return Response::error(404);
		}

		$result = $result->to_array();

		if (count($this->joins) > 0)
		{
			$result = $this->apply_mappings($result, $this->mapped_columns);
		}

		return Response::json($result);	
	}

	public function create($input, $sync = array())
	{
		$model = $this->model;
		
		// Create a new Object
		$model->fill($input);

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
				$model->$relationship()->sync($relation_ids ? array_flip(array_filter(array_flip($relation_ids), 'strlen')) : array());	
			}
			
			// Return the model's id
			return Response::json($model->get_key());
		}
	}

	public function update($input, $sync = array())
	{
		$model = $this->model;
		
		// Create a new Object
		$model->fill($input);

		foreach ($sync as $relationship => $relation_ids)
		{
			$model->$relationship()->sync($relation_ids ? array_flip(array_filter(array_flip($relation_ids), 'strlen')) : array());	
		}	

		// Try to save
		if($model->save() === false)
		{
			return Response::json((array) $model->errors->messages, 400);
		}
	}

	public function delete()
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

	protected function find($query, $id = null)
	{
		if( ! is_null($id))
		{
			if(is_numeric($id))
			{
				return $query->where($this->model->table().'.id', '=', $id);
			}
			else
			{
				if($this->multilanguage)
				{
					// Set the lowercase model name, without the namespace
					$model = strtolower(class_basename($this->model));

					// Set the language table anme
					$language_table = $model.'_lang';

					return $query->where($language_table.'.slug', '=', $id);
				}
				else
				{
					return $query->where($this->model->table().'.slug', '=', $id);
				}
			}
		}
	}

	protected function apply_mappings($result)
	{
		foreach ($this->mapped_columns as $table => $columns)
		{
			foreach ($columns as $column => $temp_name)
			{
				$result[ends_with($table, '_lang') ? 'lang' : $table][$column] = $result[$temp_name];
				unset($result[$temp_name]);
			}
		}

		return $result;
	}

	protected function apply_joins($query)
	{
		if(count($this->joins) > 0)
		{
			foreach ($this->joins as $table => $join)
			{
				extract($join);

				$segments = explode(' AS ', $table);
				if(count($segments) > 1)
				{
					list($table_name, $alias_name) = $segments;
				}
				else
				{
					$table_name = $table;
					$alias_name = $table;
				}

				if ( ! isset($this->settings['relating'][$table_name]))
				{
					$this->settings['relating'][$table_name] = $this->get_columns($table_name);
				}

				foreach ($this->settings['relating'][$table_name] as $column)
				{
					$this->mapped_columns[$alias_name][$column] = 'temp_'.$alias_name.'_'.$column;

					$this->get_columns[] = $alias_name.'.'.$column.' AS '. 'temp_'.$alias_name.'_'.$column;
				}

				$defaults = array(
					'', '', '', 'INNER'
				);
				
				$join = $join + $defaults;

				list($column1, $operator, $column2, $type) = $join;

				$query = $query->join($table, $column1, $operator, $column2, $type);
			}
		}

		return $query;
	}

	protected function get_columns($table)
	{
		$table = explode(' AS ', $table);
		$table = $table[0];

		$table_info = DBManager::table($table)->info();

		return array_map(function($column)
		{
			return $column['name'];
		}, $table_info);
	}

	protected function needs_join($table)
	{
		$segments = explode('.', $this->options['sort_by']);
		$sort_by = end($segments);

		if( ! isset($this->settings['sortable'][$table]))
		{
			$this->settings['sortable'][$table] = array();
		}

		if( ! isset($this->settings['searchable'][$table]))
		{
			$this->settings['searchable'][$table] = array();
		}

		$join = false;
		if(in_array($sort_by, $this->settings['sortable'][$table]))
		{
			$join = true;
		}

		foreach ($this->options['search']['columns'] as $column)
		{
			if(in_array($column, $this->settings['searchable'][$table]))
			{
				$join = true;
			}
		}

		return $join;
	}

}