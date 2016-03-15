<?php namespace Lagdo\Lajax;

class Controller
{
	// Application data
	public $request = null;
	public $response = null;
	// Javascripts requests to this class
	public $requests = array();

	/**
	 * Create a new Controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{}

	/**
	 * Initialise the controller.
	 *
	 * @return void
	 */
	public function init()
	{}

	/**
	 * Return the javascript call to an Xajax controller method
	 *
	 * @param string $method the name of the method
	 * @param array $parameters the parameters of the method
	 * @return string
	 */
	final public function call($method, array $parameters = array())
	{
		return $this->request->call($this, $method, $parameters);
	}

	/**
	 * Set an Xajax presenter on a Laravel paginator
	 *
	 * @param object $paginator the Laravel paginator
	 * @param string $method the name of the method
	 * @param array $parameters the parameters of the method
	 * @return object the Laravel paginator instance
	 */
	final public function setPresenter($paginator, $method, array $parameters = array())
	{
		return $this->request->setPresenter($paginator, $this, $method, $parameters);
	}

	/**
	 * Make the pagination for an Xajax controller method
	 *
	 * @param integer $itemsTotal the total number of items
	 * @param integer $itemsPerPage the number of items per page page
	 * @param integer $page the current page
	 * @param string $method the name of the method
	 * @param array $parameters the parameters of the method
	 * @return object the Laravel paginator instance
	 */
	final public function paginator($itemsTotal, $itemsPerPage, $page, $method, array $parameters = array())
	{
		return $this->request->paginator($itemsTotal, $itemsPerPage, $page, $this, $method, $parameters);
	}
}
