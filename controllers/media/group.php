<?php

use Domain\Libraries\DAL;
use Domain\Models\MediaGroup;

class Domain_Media_Group_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->dal = DAL::model(new MediaGroup)
			->options(array(
				'sort_by' => 'created_at',
			))
			->settings(array(
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
			));
	}

	/**
	 * Get all MediaGroups
	 *
	 * @return Response
	 */
	public function get_read_multiple($module_id = null)
	{
		return $this->dal
			->filter(array(
				'module_id' => $module_id
			))
			->options(Input::all())
			->read_multiple()
			->response();
	}

}