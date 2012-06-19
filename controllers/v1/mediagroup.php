<?php

use Domain\Models\MediaGroup;

class Domain_V1_Mediagroup_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->model = new MediaGroup;
	}

	/**
	 * Get all accounts
	 *
	 * @return Response
	 */
	public function get_mediagroup_all()
	{
		$this->options = array(
			'sort_by' => 'created_at',
		);

		$this->settings = array(
			'sortable' => array(
				'mediagroups' => array(
					'name',
					'created_at'
				)
			),
			'searchable' => array(
				'mediagroups' => array(
					'name'
				)
			)
		);

		return $this->get_multiple(Input::all());
	}

}