<?php

use Domain\Models\Language;

class Domain_Language_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->model = new Language;
	}

	/**
	 * Get all languages
	 *
	 * @return Response
	 */
	public function get_read_multiple()
	{
		$this->settings['sortable'] = array(
			'languages' => array(
				'name'
			)
		);

		return $this->read_multiple(Input::all());
	}

	/**
	 * Get language by id
	 *
	 * @return Response
	 */
	public function get_read($id)
	{
		return $this->read($id, Input::all());
	}

	/**
	 * Add language
	 *
	 * @return Response
	 */
	public function post_create()
	{
		return $this->create(Input::all());
	}

	/**
	 * Edit language
	 *
	 * @return Response
	 */
	public function put_update($id)
	{
		return $this->update($id, Input::all());
	}

	/**
	 * Delete language
	 *
	 * @return Response
	 */
	public function delete_delete($id)
	{
		$this->delete($id, Input::all());
	}

}