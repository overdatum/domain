<?php

use Domain\Libraries\DAL;
use Domain\Models\Role;

class Domain_Role_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->dal = DAL::model(new Role)
			->settings(array(
				'sortable' => array(
					'role_lang' => array(
						'name'
					)
				)
			))
			->multilanguage();
	}

	/**
	 * Get all roles
	 *
	 * @return Response
	 */
	public function get_read_multiple()
	{
		return $this->dal
			->options(Input::all())
			->read_multiple()
			->response();
	}

	/**
	 * Get role by id
	 *
	 * @return Response
	 */
	public function get_read($id)
	{
		return $this->dal
			->options(Input::all())
			->read($id)
			->response();
	}

	/**
	 * Add role
	 *
	 * @return Response
	 */
	public function post_create()
	{
		return $this->dal
			->create(Input::all())
			->response();
	}

	/**
	 * Edit role
	 *
	 * @return Response
	 */
	public function put_update($id)
	{
		return $this->dal
			->update($id, Input::all())
			->response();
	}

	/**
	 * Delete role
	 *
	 * @return Response
	 */
	public function delete_delete($id)
	{
		$this->dal
			->delete($id)
			->response();
	}

}