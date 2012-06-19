<?php

use Domain\Models\Asset;

class Domain_Media_Group_Asset_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->model = new Asset;

		$this->multilanguage = true;
	}

	/**
	 * Get all accounts
	 *
	 * @return Response
	 */
	public function get_list()
	{
		$this->options = array(
			'sort_by' => 'created_at',
		);

		$this->settings = array(
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
		);

		return $this->get_multiple(Input::all());
	}

}