<?php

use Domain\Models\Module;

class Domain_Module_Controller extends Domain_Base_Controller {
	
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
	public function get_read_multiple()
	{
		return $this->get_multiple(Input::all());
	}

	/**
	 * Get account by id
	 *
	 * @return Response
	 */
	public function get_read($id)
	{
		return $this->get_single($id);
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
	public function put_update($id)
	{
		$this->model($id);

		return $this->update_single(Input::all());
	}

	/**
	 * Delete module
	 *
	 * @return Response
	 */
	public function delete_delete($id)
	{
		$this->model($id);

		$this->delete_single();
	}

}