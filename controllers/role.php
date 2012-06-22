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
	public function get_read_multiple()
	{
		$this->settings['sortable'] = array(
			'roles' => array(
				'name'
			)
		);

		return $this->read_multiple(Input::all());
	}

	/**
	 * Get role by id
	 *
	 * @return Response
	 */
	public function get_read($id)
	{
		return $this->read($id);
	}

	/**
	 * Add role
	 *
	 * @return Response
	 */
	public function post_create()
	{
		return $this->create(Input::all());
	}

	/**
	 * Edit role
	 *
	 * @return Response
	 */
	public function put_update($id)
	{
		return $this->update($id, Input::all());
	}

	/**
	 * Delete role
	 *
	 * @return Response
	 */
	public function delete_delete($id)
	{
		$this->delete($id);
	}

}