<?php namespace Lagdo\Lajax\Xajax;

class Controller
{
	// Application data
	protected $response = null;

	public function __construct()
	{}

	public function __init()
	{}

	public function response()
	{
		return $this->response;
	}

	public function setResponse($response)
	{
		$this->response = $response;
	}
}
