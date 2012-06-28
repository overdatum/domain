<?php

use Domain\Libraries\DAL;
use Domain\Models\MediaGroup;
use Domain\Models\Module;

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
				),
				'filterable' => array(
					'mediagroups' => array(
						'module_id'
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
		$dal = DAL::model(new Module)
			->slug();

		if( ! $dal->find($module_id))
		{
			return $dal->response();
		}

		$module_id = $dal->model->first()->id;

		return $this->dal
			->options(Input::all())
			->filter(array(
				'module_id' => $module_id
			))
			->read_multiple()
			->response();
	}

}