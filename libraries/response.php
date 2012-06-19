<?php namespace Domain\Libraries;

use Laravel\Response as Laravel_Response;

class Response extends Laravel_Response {

	/**
	 * Create a new JSON response.
	 *
	 * <code>
	 *		// Create a response instance with JSON
	 *		return Response::json($data, 200, array('header' => 'value'));
	 * </code>
	 *
	 * @param  mixed     $data
	 * @param  int       $status
	 * @param  array     $headers
	 * @return Response
	 */
	public static function json($data, $status = 200, $headers = array())
	{
		$headers['Content-Type'] = 'application/json';
		return new static(indent(json_encode($data)), $status, $headers);
	}

	/**
	 * Create a new response of JSON'd Eloquent models.
	 *
	 * <code>
	 *		// Create a new response instance with Eloquent models
	 *		return Response::eloquent($data, 200, array('header' => 'value'));
	 * </code>
	 *
	 * @param  Eloquenet|array  $data
	 * @param  int              $status
	 * @param  array            $headers
	 * @return Response
	 */
	public static function eloquent($data, $status = 200, $headers = array())
	{
		$headers['Content-Type'] = 'application/json';

		return new static(indent(eloquent_to_json($data)), $status, $headers);
	}

}