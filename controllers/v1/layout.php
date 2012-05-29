<?php

class Domain_V1_Layout_Controller extends Domain_Base_Controller {
	
	public function __construct()
	{
		$this->model = new Layout;
	}

	/**
	 * Get all layouts
	 *
	 * @return Response
	 */
	public function get_layout_all()
	{
		return $this->get_multiple();
	}

	/**
	 * Get layout by id
	 *
	 * @return Response
	 */
	public function get_layout($id)
	{
		return $this->get_single($id);
	}

	/**
	 * Add layout
	 *
	 * @return Response
	 */
	public function post_layout()
	{
		$layout = $this->model();

		return $this->create_single(Input::all());
	}

	/**
	 * Edit layout
	 *
	 * @return Response
	 */
	public function put_layout($id)
	{
		// Find the layout we are updating
		$layout = $this->model($id);

		return $this->update_single(Input::all());
	}

	/**
	 * Delete layout
	 *
	 * @return Response
	 */
	public function delete_layout($id)
	{
		$this->model($id);

		$this->delete_single();
	}

}