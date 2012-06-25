<?php

use Domain\Libraries\DAL;
use Domain\Models\Account;

class Domain_Account_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->model = new Account;

		$this->dal = DAL::model(new Account)
			->options(array(
				'sort_by' => 'created_at',
			))
			->settings(array(
				'sortable' => array(
					'accounts' => array(
						'name',
						'email',
						'created_at',
						'updated_at'
					)
				),
				'searchable' => array(
					'accounts' => array(
						'name',
						'email'
					)
				)
			));
	}

	/**
	 * Get all accounts
	 *
	 * @return Response
	 */
	public function get_read_multiple()
	{
		return $this->dal
			->with(array('roles', 'roles.lang', 'language'))
			->options(Input::all())
			->read_multiple()
			->response();
	}

	/**
	 * Get account by id
	 *
	 * @return Response
	 */
	public function get_read($id)
	{
		$this->dal->with(array('roles', 'language'));

		return $this->dal->read($id)->response();
	}

	/**
	 * Add account
	 *
	 * @return Response
	 */
	public function post_create()
	{
		Account::$rules['password'] = 'required';
		Account::$rules['email'] = 'required|email|unique:accounts,email';

		Account::$accessible[] = 'password';

		$this->dal->sync(array(
			'roles'
		));

		return $this->dal->create(Input::all());
	}

	/**
	 * Edit account
	 *
	 * @return Response
	 */
	public function put_update($id)
	{
		// If the password is set, we allow it to be updated
		if(Input::get('password') !== '') Account::$accessible[] = 'password';

		$this->dal->sync(array(
			'roles'
		));
			
		return $this->dal->update($id, Input::all())->response();
	}

	/**
	 * Delete account
	 *
	 * @return Response
	 */
	public function delete_delete($id)
	{
		return $this->dal->delete($id)->response();
	}

}