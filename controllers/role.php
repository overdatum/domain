<?php

use Domain\Models\Role;

class Domain_Role_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->model = new Role;

		$this->multilanguage = true;
	}

	/**
	 * Get all roles
	 *
	 * @return Response
	 */
	public function get_list()
	{
		$this->settings['sortable'] = array(
			'roles' => array(
				'name'
			)
		);

		return $this->get_multiple(Input::all());
	}

	/**
	 * Get role by id
	 *
	 * @return Response
	 */
	public function get_read($id)
	{
		return $this->get_single($id);
	}

	/**
	 * Add role
	 *
	 * @return Response
	 */
	public function post_create()
	{
		$role = $this->model();

		return $this->create_single(Input::all());
	}

	/**
	 * Edit role
	 *
	 * @return Response
	 */
	public function put_update($id)
	{
		// Find the role we are updating
		$role = $this->model($id);

		return $this->update_single(Input::all());
	}

	/**
	 * Delete role
	 *
	 * @return Response
	 */
	public function delete_delete($id)
	{
		$this->model($id);

		$this->delete_single();
	}

}