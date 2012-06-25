<?php

use Domain\Libraries\DAL;
use Domain\Models\Module;

class Domain_Media_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->dal = DAL::model(new Module)
			->settings(array(
				'sortable' => array(
					'modules' => array(
						'name'
					)
				)
			));
	}

	/**
	 * Get all Media Modules
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