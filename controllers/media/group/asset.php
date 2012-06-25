<?php

use Domain\Libraries\DAL;
use Domain\Models\Asset;

class Domain_Media_Group_Asset_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->dal = DAL::model(new Asset)
			->options(array(
				'sort_by' => 'created_at',
			))
			->settings(array(
				'sortable' => array(
					'assets' => array(
						'created_at'
					),
					'asset_lang' => array(
						'name',
						'description',
					)
				),
				'searchable' => array(
					'asset_lang' => array(
						'name',
						'description'
					)
				)
			))
			->multilanguage();
	}

	/**
	 * Get all accounts
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

}