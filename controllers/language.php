<?php

use Domain\Libraries\DAL;
use Domain\Models\Language;

class Domain_Language_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->dal = DAL::model(new Language)
			->settings(array(
				'sortable' => array(
					'languages' => array(
						'name'
					)
				)
			));
	}

	/**
	 * Get all languages
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
	 * Get language by id
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
	 * Add language
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
	 * Edit language
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
	 * Delete language
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