<?php

use Domain\Libraries\DAL;
use Domain\Models\Page;
use Domain\Models\PageLang;

class Domain_Page_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->dal = DAL::model(new Page)
			->language_model(new PageLang)
			->options(array(
				'sort_by' => 'created_at',
			))
			->settings(array(
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
				),
				'sortable' => array(
					'page_lang' => array(
						'meta_title',
						'menu',
						'content',
						'created_at',
						'updated_at'
					)
				),
				'searchable' => array(
					'page_lang' => array(
						'meta_title',
						'menu',
						'content'
					)
				)
			))
			->multilanguage()
			->slug('url');
	}

	/**
	 * Get all pages
	 *
	 * @return Response
	 */
	public function get_read_multiple()
	{
		return $this->dal
			->with(array('account'))
			->options(Input::all())
			->read_multiple()
			->response();
	}

	/**
	 * Get page by id
	 *
	 * @return Response
	 */
	public function get_read($id)
	{
		return $this->dal
			->with(array('account', 'layout'))
			->options(Input::all())
			->read($id)
			->response();
	}

	/**
	 * Add page
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
	 * Edit page
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
	 * Delete page
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