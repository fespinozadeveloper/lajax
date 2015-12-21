<?php namespace Lagdo\Lajax;

class Controller
{
	// Application data
	public $response = null;
	// Javascripts requests to this class
	public $requests = array();

	public function __construct()
	{}

	public function __init()
	{}

	protected function getScript($method, array $parameters = array())
	{
		return \App::make('lajax')->getScript($this, $method, $parameters);
	}

	protected function paginate($currentPage, $itemsPerPage, $itemsTotal, $method, array $parameters = array())
	{
		return \App::make('lajax')->paginate($currentPage, $itemsPerPage, $itemsTotal, $this, $method, $parameters);
	}
}
