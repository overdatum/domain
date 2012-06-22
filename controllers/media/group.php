<?php

use Domain\Models\MediaGroup;

class Domain_Media_Group_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->model = new MediaGroup;
	}

	/**
	 * Get all accounts
	 *
	 * @return Response
	 */
	public function get_read_multiple($module_id = null)
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

		return $this->read_multiple(Input::all());
	}

}