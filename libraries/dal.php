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

namespace Domain\Libraries;

use StdClass;

use Laravel\Str;
use Laravel\Database as DB;

use Layla\DBManager;


/**
* This DAL class provides methods for setting up a secured,
* CRUD DB layer over HTTP with very little effort
*/
class DAL {

	/**
	 * The model we are working with
	 * 
	 * @var Database\Eloquent\Model
	 */
	public $model;

	/**
	 * The model's table
	 * 
	 * @var string
	 */
	public $table;

	/**
	 * The language model
	 * 
	 * @var Database\Eloquent\Model
	 */
	public $language_model;

	/**
	 * The language table
	 * 
	 * @var string
	 */
	public $language_table;

	/**
	 * The language table's foreign key
	 * 
	 * @var string
	 */
	public $language_table_foreign_key;

	/**
	 * The parent
	 */
	public $parent;

	/**
	 * The data
	 */
	public $data = array();

	/**
	 * The input
	 * 
	 * @var array
	 */
	public $input = array();

	/**
	 * The response code
	 * 
	 * @var int
	 */
	public $code = 200;

	/**
	 * The settings
	 * 
	 * @var array
	 */
	public $settings = array(
		'relating' => array(),
		'sortable' => array(),
		'searchable' => array(),
		'filterable' => array()
	);

	/**
	 * Options are used for limiting the results on get requests
	 * And can be used to add custom data to the input on post / put requests
	 * 
	 * @var array
	 */
	public $options = array(
		'offset' => 0,
		'limit' => 20,
		'sort_by' => 'name',
		'order' => 'ASC',
		'filter' => array(
		),
		'search' => array(
			'string' => '',
			'columns' => array()
		)
	);

	/**
	 * Indicates if a language table should be joined or included
	 * 
	 * The table will be joined in case there is no search or order
	 * on one of the table's columns
	 * The expected language table name is the model's name
	 * post-fixed with "_lang". For example, the expected language
	 * table for "users" is "user_lang"
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
	 * Indicates if slug is enabled and from
	 * what column it should be generated
	 * 
	 * @var string
	 */
	public $slug;

	/**
	 * The model's id
	 */
	public $id;

	/**
	 * The language_id
	 */
	public $language_id;

	/**
	 * The relationships that have to be synced
	 * 
	 * @var array
	 */
	public $sync = array();

	/**
	 * Indicates what tables should be joined (only for get_ methods)
	 * 
	 * <code>
	 * 		$this->joins[] = array(
	 * 			'table' => 'mobilenumbers',
	 * 			'join' => array('users.id', '=', 'mobilenumbers.user_id', 'INNER')
	 *		);
	 * </code>
	 * 
	 * @var array
	 */
	public $joins = array();

	/**
	 * The columns on joined tables that will be mapped
	 * 
	 * @var array
	 */
	public $mapped_columns = array();

	/**
	 * The fields that will be retrieved
	 * 
	 * @var array
	 */
	public $get_columns = array();

	public function __construct($model = null)
	{
		if( ! is_null($model))
		{
			$this->model = $model;

			$this->table = $model->table();

			// Initialize the array that will hold the columns we want to fetch
			$this->get_columns = array($this->table.'.*');
		}
	}

	/**
	 * Method for retrieving a DAL instance
	 * 
	 * @param $model the model to work with
	 */
	public static function model($model)
	{
		return new DAL($model);
	}

	/**
	 * Method for setting the language model
	 * 
	 * @param $language_model the language model
	 */
	public function language_model($language_model)
	{
		$this->language_model = $language_model;

		return $this;
	}

	/**
	 * Method for merging settings
	 * 
	 * @var array the settings
	 */
	public function settings($settings)
	{
		$this->settings = array_replace_recursive($this->settings, $settings);

		return $this;
	}

	/**
	 * Method for merging options
	 * 
	 * @var array the options
	 */
	public function options($options)
	{
		$this->options = array_replace_recursive($this->options, $options);

		return $this;
	}

	public function filter($filter)
	{
		$this->options['filter'] = array_replace_recursive($this->options['filter'], $filter);

		return $this;
	}

	/**
	 * Method for merging input
	 * 
	 * @var array the input
	 */
	public function input($input)
	{
		$this->input = array_replace_recursive($this->input, $input);

		return $this;
	}

	public function multilanguage($multilanguage = true)
	{
		$this->multilanguage = $multilanguage;

		if($multilanguage)
		{
			// Get the lowercase model name, without the namespace
			$basename = strtolower(class_basename($this->model));

			// Set the language table name
			$this->language_table = $basename.'_lang';

			// Set the language table's foreign key
			$this->language_table_foreign_key = $basename.'_id';

			$this->settings['filterable'][$this->language_table][] = 'language_id';
		}

		return $this;
	}

	public function versioned($versioned = true)
	{
		$this->versioned = $versioned;

		return $this;
	}

	public function slug($slug = '')
	{
		$this->slug = $slug;

		return $this;
	}

	public function parent($parent)
	{
		$this->parent = $parent;

		return $this;
	}

	public function sync($sync = null)
	{
		if(is_null($sync))
		{
			foreach($this->sync as $relationship)
			{
				$this->model->$relationship()->sync($this->input[$relationship] ? array_flip(array_filter(array_flip($this->input[$relationship]), 'strlen')) : array());	
			}

			return $this;
		}

		$this->sync = $sync;

		return $this;
	}

	/**
	 * Method for retrieving multiple records
	 */
	public function read_multiple()
	{
		if( ! $this->valid_options())
		{
			return $this;
		}

		// Multilanguage, lets check if we need to join the records (for search or sort)
		if ($this->multilanguage)
		{
			$filters = $this->get_filters($this->language_table);

			// Find out if we need a join
			if ($this->needs_join($this->language_table) || $this->versioned)
			{
				$table = $this->table;
				$language_table = $this->language_table;
				$language_table_foreign_key = $this->language_table_foreign_key;

				// Add a join for the language table
				$this->joins[] = array(
					'table' => $language_table,
					'join' => array(function($join) use ($table, $language_table, $language_table_foreign_key, $filters)
					{
						foreach($filters as $filter)
						{
							list($table, $column, $value) = $filter;

							$join->where($table.'.'.$column, '=', $value);
						}

						$join->on($table.'.id', '=', $language_table.'.'.$language_table_foreign_key);
						$join->on($language_table.'.language_id', '=', 1);
					})
				);

				if($this->versioned)
				{
					// Add a left outer join to be able to only get
					// the language rows at the latest version
					$this->joins[] = array(
						'table' => $language_table.' AS max',
						'join' => array(function($join) use ($table, $language_table, $language_table_foreign_key, $filters)
						{
							foreach($filters as $filter)
							{
								list($table, $column, $value) = $filter;

								$join->where($table.'.'.$column, '=', $value);
							}

							$join->on($table.'.id', '=', 'max.'.$language_table_foreign_key);
							$join->on('max.version', '>', $language_table.'.version');
							$join->on('max.language_id', '=', 1);
						}, '', '', 'LEFT OUTER')
					);

					$this->get_columns[] = $language_table.'.version';

					$this->model = $this->model->where('max.version', 'IS', DB::raw('NULL'));
				}
			}
			
			else
			{
				$this->model->includes[] = array('lang' => function($query) use ($filters)
				{
					foreach($filters as $filter)
					{
						list($table, $column, $value) = $filter;

						$query = $query->where($table.'.'.$column, '=', $value);
					}
				});
			}
		}

		elseif($this->versioned)
		{
			$this->model = $this->model->where('version', '=',
				DB::raw('('.
					DB::table($this->table.' AS max')
						->select(DB::raw('MAX(version) AS version'))
						->where($this->table.'.id', '=', DB::raw('max.id'))
						->sql().
				')')
			);
		}

		$this->apply_joins();

		foreach($this->get_filters($this->table) as $filter)
		{
			list($table, $column, $value) = $filter;

			$this->model = $this->model->where($table.'.'.$column, '=', $value);
		}

		// Add where's to our query
		if ($this->options['search']['string'] !== '')
		{
			$columns = $this->options['search']['columns'];
			$string = $this->options['search']['string'];
			
			$this->model = $this->model->where(function($query) use ($columns, $string)
			{
				foreach($columns as $column)
				{
					$query->or_where($column, ' LIKE ', '%'.$string.'%');
				}
			});
		}

		$total = (int) $this->model->count();

		// Add order_by, skip & take to our results query
		$results = $this->model
			->order_by($this->options['sort_by'], $this->options['order'])
			->skip($this->options['offset'])
			->take($this->options['limit'])
			->get($this->get_columns);

		$this->data = array(
			'results' => to_array($results),
			'total' => $total,
			'pages' => ceil($total / $this->options['limit'])
		);

		$this->apply_mappings();

		return $this;
	}

	/**
	 * Method for retrieving a single record
	 */
	public function read($id)
	{
		if( ! $this->valid_options())
		{
			return $this;
		}

		if( ! $this->find($id))
		{
			return $this;
		}

		$version = isset($this->options['filter']['version']) ? $this->options['filter']['version'] : null;

		if(is_null($version) && $this->versioned)
		{
			if($this->multilanguage)
			{
				$query = DB::table($this->language_table)
					->where($this->language_table_foreign_key, '=', $this->id);

				foreach($this->get_filters($this->language_table) as $filter)
				{
					list($table, $column, $value) = $filter;

					$query = $query->where($table.'.'.$column, '=', $value);
				}

				$version = $query->max('version');
			}
			else
			{
				$query = DB::table($this->table)
					->where_id($this->id);

				foreach($this->get_filters($this->table) as $filter)
				{
					list($table, $column, $value) = $filter;

					$query = $query->where($table.'.'.$column, '=', $value);
				}

				$version = $query->max('version');
			}
		}

		if($this->multilanguage)
		{
			if( ! isset($this->options['filter']['language_id']))
			{
				$filters = $this->get_filters($this->language_table);

				$this->model->model->with(array(
					'languages' => function($query) use ($version, $filters)
					{
						foreach($filters as $filter)
						{
							list($table, $column, $value) = $filter;

							$query->where($table.'.'.$column, '=', $value);
						}

						if( ! is_null($version))
						{
							$query->where_version($version);
						}
					}
				));
			}
			else
			{
				$language_id = $this->options['filter']['language_id'];

				$filters = $this->get_filters($this->language_table);

				$this->model->model->with(array(
					'lang' => function($query) use ($version, $language_id, $filters)
					{
						foreach($filters as $filter)
						{
							list($table, $column, $value) = $filter;

							$query->where($table.'.'.$column, '=', $value);
						}

						$query->where_language_id($language_id);

						if( ! is_null($version))
						{
							$query->where_version($version);
						}
					}
				));
			}
		}

		if( ! is_null($version))
		{
			$this->model = $this->model->where($this->table.'.version', '=', $version);
		}

		foreach($this->get_filters($this->table) as $filter)
		{
			list($table, $column, $value) = $filter;

			$this->model = $this->model->where($table.'.'.$column, '=', $value);
		}

		$this->data = $this->model->first($this->get_columns)->to_array();

		$this->apply_mappings();

		return $this;
	}

	public function create_multiple()
	{
		foreach($this->input as $input)
		{
			$dal = clone $this;

			$dal->input = $input;

			$dal->create();

			if($dal->code === 400)
			{
				$this->code = 400;
				$this->data[] = $dal->data;
			}
		}

		return $this;
	}

	public function create()
	{
		if( ! $this->multilanguage && ( ! is_null($this->slug) || (isset($this->parent) && ! is_null($this->parent))))
		{
			$key = isset($this->parent) && ! is_null($this->parent) ?
					$this->parent->slug
				:
					$this->slug;

			$this->input['slug'] = Str::slug($this->input[$key]); 
		}

		// Fill the model with data
		$this->model->fill($this->input);

		// Try to save
		if($this->model->save() === false)
		{
			$this->code = 400;
			$this->data = (array) $this->model->errors->messages;

			return $this;
		}

		$this->sync();

		if($this->multilanguage)
		{
			$lang_input = array();
			foreach($this->input['lang'] as $id => $input)
			{
				$input['language_id'] = $id;
				$input[$this->language_table_foreign_key] = $this->id;

				$lang_input[] = $input;
			}

			unset($this->input['lang']);
			
			$dal = DAL::model($this->language_model)
				->versioned($this->versioned)
				->input($lang_input)
				->create_multiple();

			if( ! $dal->code == 200)
			{
				$this->data = $dal->data;
				$this->code = $dal->code;

				return $this;
			}
		}
		
		// Set the model's id as data
		$this->data = $this->model->get_key();

		return $this;
	}

	public function update_multiple()
	{
		foreach($this->input as $input)
		{
			$dal = clone $this;

			$dal->input = $input;

			$id = is_null($this->parent) ? $input['id'] : $input[$this->parent->language_table_foreign_key];

			$dal->update($id);

			if($dal->code === 400)
			{
				$this->code = 400;
				$this->data[$dal->id] = $dal->data;
			}
		}

		return $this;
	}

	public function update($id)
	{
		if( ! $this->find($id))
		{
			return $this;
		}

		$this->model = $this->model->first();

		if($this->versioned && ! $this->multilanguage)
		{
			$latest_version = DB::table($this->table)
				->where_id($this->model->get_key())
				->max('version');

			$this->model->version = is_null($latest_version) ? 0 : $latest_version + 1;
			$this->model->exists = false;
		}

		if($this->multilanguage)
		{
			if($this->parent)
			{
				$this->input[$this->parent->language_table_foreign_key] = $this->parent->model->id;

				return $this->create();
			}

			$lang_input = array();
			foreach($this->input['lang'] as $id => $input)
			{
				$input['language_id'] = $id;
				$input[$this->language_table_foreign_key] = $this->id;

				$lang_input[] = $input;
			}

			unset($this->input['lang']);
			
			$dal = DAL::model($this->language_model)
				->parent($this)
				->versioned($this->versioned)
				->input($lang_input)
				->update_multiple();

			if( ! $dal->code == 200)
			{
				$this->data = $dal->data;
				$this->code = $dal->code;

				return $this;
			}
		}
		elseif( ! is_null($this->slug) || (isset($this->parent) && ! is_null($this->parent)))
		{
			$key = isset($this->parent) && ! is_null($this->parent) ?
					$this->parent->slug
				:
					$this->slug;

			$this->input['slug'] = Str::slug($this->input[$key]); 
		}

		// Fill the model
		$this->model->fill($this->input);

		// Try to save
		if($this->model->save() === false)
		{
			$this->code = 400;
			$this->data = (array) $this->model->errors->messages;
		}

		$this->sync();

		return $this;
	}

	public function delete_multiple($ids)
	{
		foreach($ids as $id)
		{
			$dal = clone $this;

			$dal->delete($id);
		}

		return $this;
	}

	public function delete($id)
	{
		if( ! $this->find($id))
		{
			return $this;
		}

		$this->model->delete();

		return $this;
	}

	public function get()
	{
		return $this->data;
	}

	public function response()
	{
		return Response::json(is_null($this->data) ? '' : $this->data, $this->code);
	}

	public function __clone()
	{
		$this->model = clone $this->model;

		if( ! is_null($this->language_model))
		{
			$this->language_model = clone $this->language_model;
		}
	}

	public function __call($method, $parameters)
	{
		$this->model = call_user_func_array(array($this->model, $method), $parameters);

		return $this;
	}

	public function __get($key = null)
	{
		if(is_null($key))
		{
			return $this->data;
		}

		return $this->data[$key];
	}

	public function find($id)
	{
		$column = 'id';

		if( ! is_numeric($id))
		{
			if(is_null($this->slug))
			{
				$this->code = 404;
				$this->data = 'You are trying to retrieve data with a slug from a resource that is not retrievable via a slug';

				return false;
			}

			if($this->multilanguage)
			{
				$language_row = DB::table($this->language_table)
					->where_slug($id)
					->first(array($this->language_table_foreign_key, 'language_id'));
				
				if(is_null($language_row))
				{
					$id = null;
				}
				else
				{
					$id = $language_row->{$this->language_table_foreign_key};

					$this->options(array(
						'filter' => array(
							'language_id' => $language_row->language_id
						)
					));
				}
			}
			else
			{
				$column = 'slug';
			}
		}
		elseif($this->parent && $this->parent->multilanguage)
		{
			if( ! $this->input['language_id'])
			{
				$this->code = 404;
				$this->data = 'Please provide the language_id';

				return false;
			}

			$this->language_id = $this->input['language_id'];

			$this->model = $this->model->where($this->table.'.language_id', '=', $this->input['language_id']);

			$column = $this->parent->language_table_foreign_key;
		}

		$this->id = $id;

		$this->model = $this->model->where($this->table.'.'.$column, '=', $id);

		if(is_null($this->model->first()))
		{
			$this->code = 404;
			$this->data = 'The data you are trying to retrieve could not be found';

			return false;
		}

		return true;
	}

	protected function apply_joins()
	{
		if(count($this->joins) > 0)
		{
			foreach($this->joins as $table => $join)
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

				if($alias_name !== 'max')
				{
					if ( ! isset($this->settings['relating'][$table_name]))
					{
						$this->settings['relating'][$table_name] = $this->get_columns($table_name);
					}

					foreach($this->settings['relating'][$table_name] as $column)
					{
						$this->mapped_columns[$alias_name][$column] = 'temp_'.$alias_name.'_'.$column;

						$this->get_columns[] = $alias_name.'.'.$column.' AS '. 'temp_'.$alias_name.'_'.$column;
					}
				}

				$defaults = array(
					'', '', '', 'INNER'
				);
				
				$join = $join + $defaults;

				list($column1, $operator, $column2, $type) = $join;

				$this->model = $this->model->join($table, $column1, $operator, $column2, $type);
			}
		}
	}

	protected function apply_mapping($result)
	{
		foreach($this->mapped_columns as $table => $columns)
		{
			foreach($columns as $column => $temp_name)
			{
				$result[ends_with($table, '_lang') ? 'lang' : $table][$column] = $result[$temp_name];
				unset($result[$temp_name]);
			}
		}

		return $result;
	}

	protected function apply_mappings()
	{
		if (count($this->joins) > 0)
		{
			if(array_key_exists('results', $this->data) && array_key_exists('total', $this->data) && array_key_exists('pages', $this->data))
			{
				foreach($this->data['results'] as &$row)
				{
					$row = $this->apply_mapping($row);
				}
			}
			else
			{
				$this->data = $this->apply_mapping($this->data);
			}
		}
	}

	protected function get_filters($table)
	{
		$filters = array();

		if(empty($this->settings['filterable'][$table]))
		{
			return $filters;
		}

		foreach($this->options['filter'] as $column => $value)
		{
			if(in_array($column, $this->settings['filterable'][$table]))
			{
				$filters[] = array($table, $column, $value);
			}
		}

		return $filters;
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

		foreach($this->options['search']['columns'] as $column)
		{
			if(in_array($column, $this->settings['searchable'][$table]))
			{
				$join = true;
			}
		}

		return $join;
	}

	protected function valid_options()
	{
		$found = false;
		foreach($this->settings['sortable'] as $table => $columns)
		{
			if(in_array($this->options['sort_by'], $columns))
			{
				$found = true;
				$this->options['sort_by'] = $table.'.'.$this->options['sort_by'];
			}
		}

		if( ! $found)
		{
			$this->data = "The specified sort_by option \"".$this->options['sort_by']."\" is not supported";
			$this->code = 400;

			return false;
		}

		// We are looking for something...
		if($this->options['search']['string'] !== '')
		{
			if( ! isset($this->options['search']['columns']))
			{
				$this->data = "Please specify the column that you want to search through";
				$this->code = 400;

				return false;
			}

			$unfound_columns = array();		
			foreach($this->options['search']['columns'] as $i => $column)
			{
				$found = false;
				foreach($this->settings['searchable'] as $table => $columns)
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
					$this->data = "The specified search column \"".$unfound_columns[0]."\" is not supported";
					$this->code = 400;

					return false;
				}
				else
				{
					$this->data = "The specified search columns \"".implode(', ', $unfound_columns)."\" are not supported";
					$this->code = 400;

					return false;
				}
			}
		}

		foreach($this->options['filter'] as $column => $value)
		{
			$found = false;
			foreach($this->settings['filterable'] as $table => $columns)
			{
				if(in_array($column, $this->settings['filterable'][$table]))
				{
					$found = true;
				}
			}

			if( ! $found)
			{
				$this->data = "The specified filter option \"".$column."\" is not supported";
				$this->code = 400;

				return false;
			}
		}
		
		return true;
	}
}