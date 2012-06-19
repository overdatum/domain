<?php namespace Domain\Models;

use Domain\Libraries\Model as Eloquent;

class Account extends Eloquent {
	
	public static $timestamps = true;

	public static $sequence = 'accounts_id_seq';

	public static $table = 'accounts';

	public static $accessible = array('name', 'email', 'language_id', 'id');

	public static $versioned = true;

	public static $rules = array(
		'email' => 'required|email',
		'name' => 'required',
		'language_id' => 'required'
	);

	public static $hidden = array('password', 'language_id');

	public function language()
	{
		return $this->belongs_to('Domain\\Models\\Language', 'language_id');
	}

	public function roles()
	{
		return $this->has_many_and_belongs_to('Domain\\Models\\Role');
	}

	public function versions()
	{
		return $this->has_many('Domain\\Models\\Account', 'id')
			->order_by('version', 'DESC');
	}

	/**
	 * Check if the account has a relation with the given role
	 *
	 * @param	string	$key	the role key
	 * @return	boolean
	 */
    public function has_role($key)
    {
        return is_null($this->roles()->where_name($key)->first());
    }

	/**
	 * Check if the account has a relation with any of the given roles
	 *
	 * @param	array	$keys	the role keys
	 * @return	boolean
	 */
    public function has_any_role($keys)
    {
        if( ! is_array($keys))
        {
            $keys = func_get_args();
        }

        return is_null($this->roles()->where('name', 'IN', $keys)->first());
    }

}