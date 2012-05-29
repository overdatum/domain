<?php

class Domain_V1_Page_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->model = new Page;
	}

	/**
	 * Get all pages
	 *
	 * @return Response
	 */
	public function get_page_all()
	{
		$this->includes = array('account', 'lang');

		$this->join = array(
			'page_lang' => array(
				'join' => array('pages.id', '=', 'page_lang.page_id'),
				'columns' => array('url', 'meta_title', 'meta_keywords', 'meta_description', 'menu', 'content')
			)
		);

		return $this->get_multiple();
	}

	/**
	 * Get page by id
	 *
	 * @return Response
	 */
	public function get_page($id)
	{
		$this->includes = array('layout', 'lang');

		return $this->get_single($id);
	}

	/**
	 * Add page
	 *
	 * @return Response
	 */
	public function post_page()
	{
		$page = $this->model();

		return $this->create_single(Input::all());
	}

	/**
	 * Edit page
	 *
	 * @return Response
	 */
	public function put_page($id)
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
	public function delete_page($id)
	{
		$this->model($id);

		$this->delete_single();
	}

}