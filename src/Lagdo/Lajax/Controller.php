<?php namespace Lagdo\Lajax;

class Controller
{
	protected $request = null;
	// Application data
	public $response = null;
	// Javascripts requests to this class
	public $requests = array();

	public function __construct()
	{
		$this->request = \App::make('lajax.request');
	}

	public function __init()
	{}

	/**
	 * Return the javascript call to an Xajax controller method
	 *
	 * @param string|object $controller the controller
	 * @param string $method the name of the method
	 * @param array $parameters the parameters of the method
	 * @return string
	 */
	public function call($method, array $parameters = array())
	{
		return $this->request->call($this, $method, $parameters);
	}

	/**
	 * Make the pagination for an Xajax controller method
	 *
	 * @param integer $currentPage the current page
	 * @param integer $itemsPerPage the number of items per page page
	 * @param integer $itemsTotal the total number of items
	 * @param string|object $controller the controller
	 * @param string $method the name of the method
	 * @param array $parameters the parameters of the method
	 * @return object the Laravel paginator instance
	 */
	public function paginate($currentPage, $itemsPerPage, $itemsTotal, $method, array $parameters = array())
	{
		return $this->request->paginate($currentPage, $itemsPerPage, $itemsTotal, $this, $method, $parameters);
	}

	/**
	 * Make a parameter of type XAJAX_FORM_VALUES
	 * 
	 * @param string $sFormId the id of the HTML form
	 * @return array
	 */
	protected function form($sFormId)
	{
		return $this->request->form($sFormId);
	}

	/**
	 * Make a parameter of type XAJAX_INPUT_VALUE
	 * 
	 * @param string $sInputId the id of the HTML input element
	 * @return array
	 */
	protected function input($sInputId)
	{
		return $this->request->input($sInputId);
	}

	/**
	 * Make a parameter of type XAJAX_CHECKED_VALUE
	 * 
	 * @param string $sCheckedId the name of the HTML form element
	 * @return array
	 */
	protected function checked($sCheckedId)
	{
		return $this->request->checked($sCheckedId);
	}

	/**
	 * Make a parameter of type XAJAX_ELEMENT_INNERHTML
	 * 
	 * @param string $sElementId the id of the HTML element
	 * @return array
	 */
	protected function html($sElementId)
	{
		return $this->request->html($sElementId);
	}

	/**
	 * Make a parameter of type XAJAX_QUOTED_VALUE
	 * 
	 * @param string $sValue the value of the parameter
	 * @return array
	 */
	protected function quoted($sValue)
	{
		return $this->request->quoted($sValue);
	}

	/**
	 * Make a parameter of type XAJAX_QUOTED_VALUE
	 * 
	 * @param string $sValue the value of the parameter
	 * @return array
	 */
	protected function str($sValue)
	{
		return $this->request->str($sValue);
	}

	/**
	 * Make a parameter of type XAJAX_NUMERIC_VALUE
	 * 
	 * @param numeric $nValue the value of the parameter
	 * @return array
	 */
	protected function numeric($nValue)
	{
		return $this->request->numeric($nValue);
	}

	/**
	 * Make a parameter of type XAJAX_NUMERIC_VALUE
	 * 
	 * @param numeric $nValue the value of the parameter
	 * @return array
	 */
	protected function int($nValue)
	{
		return $this->request->int($nValue);
	}

	/**
	 * Make a parameter of type XAJAX_JS_VALUE
	 * 
	 * @param string $sValue the Js code of the parameter
	 * @return array
	 */
	protected function js($sValue)
	{
		return $this->request->js($sValue);
	}

	/**
	 * Make a parameter of type XAJAX_PAGE_NUMBER
	 * 
	 * @return array
	 */
	protected function page()
	{
		return $this->request->page();
	}
}
