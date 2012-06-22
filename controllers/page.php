<?php

use Domain\Models\Page;

class Domain_Page_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->model = new Page;

		$this->multilanguage = true;

		$this->settings = array(
			'relating' => array(
				'page_lang' => array(
					'id',
					'language_id',
					'active',
					'url',
					'slug',
					'meta_title',
					'meta_keywords',
					'meta_description',
					'menu',
					'content',
					'created_at',
					'updated_at',
					'created_at'
				)
			)
		);
	}

	/**
	 * Get all pages
	 *
	 * @return Response
	 */
	public function get_read_multiple()
	{
		$this->options = array(
			'sort_by' => 'created_at',
		);
		
		$this->settings['sortable'] = array(
			'page_lang' => array(
				'meta_title',
				'menu',
				'content',
				'created_at',
				'updated_at'
			)
		);

		$this->settings['searchable'] = array(
			'page_lang' => array(
				'meta_title',
				'menu',
				'content'
			)
		);

		$this->includes = array('account');

		return $this->read_multiple(Input::all());
	}

	/**
	 * Get page by id
	 *
	 * @return Response
	 */
	public function get_read($id)
	{
		$this->includes = array('layout');

		return $this->read($id, Input::all());
	}

	/**
	 * Add page
	 *
	 * @return Response
	 */
	public function post_create()
	{
		return $this->create(Input::all());
	}

	/**
	 * Edit page
	 *
	 * @return Response
	 */
	public function put_update($id)
	{
		return $this->update($id, Input::all());
	}

	/**
	 * Delete page
	 *
	 * @return Response
	 */
	public function delete_delete($id)
	{
		return $this->delete($id, Input::all());
	}

}