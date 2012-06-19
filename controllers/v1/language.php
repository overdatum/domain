<?php

use Domain\Models\Language;

class Domain_V1_Language_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->model = new Language;
	}

	/**
	 * Get all languages
	 *
	 * @return Response
	 */
	public function get_language_all()
	{
		$this->settings['sortable'] = array(
			'languages' => array(
				'name'
			)
		);

		return $this->get_multiple(Input::all());
	}

	/**
	 * Get language by id
	 *
	 * @return Response
	 */
	public function get_language($id)
	{
		return $this->get_single($id);
	}

	/**
	 * Add language
	 *
	 * @return Response
	 */
	public function post_language()
	{
		$language = $this->model();

		return $this->create_single(Input::all());
	}

	/**
	 * Edit language
	 *
	 * @return Response
	 */
	public function put_language($id)
	{
		// Find the language we are updating
		$language = $this->model($id);

		return $this->update_single(Input::all());
	}

	/**
	 * Delete language
	 *
	 * @return Response
	 */
	public function delete_language($id)
	{
		$this->model($id);

		$this->delete_single();
	}

}