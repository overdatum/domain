<?php

use Domain\Models\Module;

class Domain_V1_Module_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->model = new Module;
		
		$this->settings['sortable'] = array(
			'modules' => array(
				'name'
			)
		);
	}

	/**
	 * Get all accounts
	 *
	 * @return Response
	 */
	public function get_module_all()
	{
		return $this->get_multiple(Input::all());
	}

	/**
	 * Get account by id
	 *
	 * @return Response
	 */
	public function get_module($id)
	{
		return $this->get_single($id);
	}

	protected function insert_module()
	{
		$account = $this->model();

		$account::$rules['password'] = 'required';
		$account::$rules['email'] = 'required|email|unique:accounts,email';

		$account::$accessible[] = 'password';

		$sync = array(
			'roles' => Input::get('roles')
		);

		return $this->create_single(Input::all(), $sync);
	}

	/**
	 * Create new module
	 *
	 * @return Response
	 */
	public function post_module_create()
	{
		new Generators_Model(array('module_blog_domain::bam', 'has_many:assholes'));

		//$this->insert_module();
	}

	/**
	 * Install module
	 */
	public function post_module_install()
	{

	}

	/**
	 * Edit module
	 *
	 * @return Response
	 */
	public function put_module($id)
	{
		// Find the account we are updating
		$account = $this->model($id);

		// If the password is set, we allow it to be updated
		if(Input::get('password') !== '') $account::$accessible[] = 'password';

		$sync = array(
			'roles' => Input::get('roles')
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