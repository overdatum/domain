<?php

use Domain\Models\Page;

class Domain_Page_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->model = new Page;

		$this->multilanguage = true;

		$this->versioned = true;

		$this->settings = array(
			'relating' => array(
				'page_lang' => array(
					'id',
					'language_id',
					'active',
					'url',
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
	public function get_list()
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

		return $this->get_multiple(Input::all());
	}

	/**
	 * Get page by id
	 *
	 * @return Response
	 */
	public function get_read($id)
	{
		$this->includes = array('layout', 'versions');

		return $this->get_single($id);
	}

	/**
	 * Add page
	 *
	 * @return Response
	 */
	public function post_create()
	{
		$page = $this->model();

		return $this->create_single(Input::all());
	}

	/**
	 * Edit page
	 *
	 * @return Response
	 */
	public function put_update($id)
	{
		// Find the page we are updating
		$page = $this->model($id);

		return $this->update_single(Input::all());
	}

	/**
	 * Delete page
	 *
	 * @return Response
	 */
	public function delete_delete($id)
	{
		$this->model($id);

		$this->delete_single();
	}

}