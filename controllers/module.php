<?php

use Domain\Models\Module;

class Domain_Module_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->dal = DAL::model(new Module)
			->settings(array(
				'sortable' => array(
					'modules' => array(
						'name'
					)
				)
			))
			->slug('name');
	}

	/**
	 * Get all modules
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
	 * Get module by id
	 *
	 * @return Response
	 */
	public function get_read($id)
	{
		return $this->dal
			->read($id)
			->response();
	}

	/**
	 * Create new module
	 *
	 * @return Response
	 */
	public function post_module_create()
	{
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
	public function put_update($id)
	{
		return $this->dal
			->input(Input::all())
			->update($id)
			->response();
	}

	/**
	 * Delete module
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