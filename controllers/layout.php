<?php

use Domain\Libraries\DAL;
use Domain\Models\Layout;

class Domain_Layout_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->dal = DAL::model(new Layout)
			->versioned()
			->slug('name');
	}

	/**
	 * Get all layouts
	 *s
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
	 * Get layout by id
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
	 * Add layout
	 *
	 * @return Response
	 */
	public function post_create()
	{
		return $this->dal
			->input(Input::all())
			->create()
			->response();
	}

	/**
	 * Edit layout
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
	 * Delete layout
	 *
	 * @return Response
	 */
	public function delete_delete($id)
	{
		return $this->dal
			->delete($id)
			->response();
	}

}