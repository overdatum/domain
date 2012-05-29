<?php

class Domain_V1_Account_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->model = new Account;
	}

	/**
	 * Get all accounts
	 *
	 * @return Response
	 */
	public function get_account_all()
	{
		$this->includes = array('roles', 'roles.lang', 'language');

		return $this->get_multiple();
	}

	/**
	 * Get account by id
	 *
	 * @return Response
	 */
	public function get_account($id)
	{
		$this->includes = array('roles', 'language');

		return $this->get_single($id);
	}

	/**
	 * Add account
	 *
	 * @return Response
	 */
	public function post_account()
	{
		$account = $this->model();

		$account::$rules['password'] = 'required';
		$account::$rules['email'] = 'email|unique:accounts,email';

		$account::$accessible[] = 'password';

		$sync = array(
			'roles' => Input::get('role_ids')
		);

		return $this->create_single(Input::all(), $sync);
	}

	/**
	 * Edit account
	 *
	 * @return Response
	 */
	public function put_account($id)
	{
		// Find the account we are updating
		$account = $this->model($id);

		// If the password is set, we allow it to be updated
		if(Input::get('password') !== '') $account::$accessible[] = 'password';

		$sync = array(
			'roles' => Input::get('role_ids')
		);
		
		return $this->update_single(Input::all(), $sync);
	}

	/**
	 * Delete account
	 *
	 * @return Response
	 */
	public function delete_account($id)
	{
		$this->model($id);

		$this->delete_single();
	}

}