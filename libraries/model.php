<?php namespace Domain;

use Laravel\Database\Eloquent\Model as Laravel_Model;
use Laravel\Validator;

class Model extends Laravel_Model {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules;

	/**
	 * Save the model instance to the database.
	 *
	 * @return bool
	 */
	public function save()
	{
		if ( ! $this->dirty()) return true;

		if (static::$timestamps)
		{
			$this->timestamp();
		}

		$this->fire_event('saving');

		if(static::$rules)
		{
			$validator = Validator::make($this->attributes, static::$rules);
			if( ! $validator->valid())
			{
				$this->errors = $validator->errors;

				return false;
			}
		}

		// If the model exists, we only need to update it in the database, and the update
		// will be considered successful if there is one affected row returned from the
		// fluent query instance. We'll set the where condition automatically.
		if ($this->exists)
		{
			$query = $this->query()->where(static::$key, '=', $this->get_key());

			$result = $query->update($this->get_dirty()) === 1;

			if ($result) $this->fire_event('updated');
		}

		// If the model does not exist, we will insert the record and retrieve the last
		// insert ID that is associated with the model. If the ID returned is numeric
		// then we can consider the insert successful.
		else
		{
			$id = $this->query()->insert_get_id($this->attributes, $this->sequence());

			$this->set_key($id);

			$this->exists = $result = is_numeric($this->get_key());

			if ($result) $this->fire_event('created');
		}

		// After the model has been "saved", we will set the original attributes to
		// match the current attributes so the model will not be viewed as being
		// dirty and subsequent calls won't hit the database.
		$this->original = $this->attributes;

		if ($result)
		{
			$this->fire_event('saved');
		}

		return $result;
	}

}