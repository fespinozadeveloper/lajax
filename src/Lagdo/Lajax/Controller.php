<?php namespace Lagdo\Lajax;

class Controller
{
	// Application data
	public $request = null;
	public $response = null;
	// Javascripts requests to this class
	public $requests = array();

	public function __construct()
	{}

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
	 * Get all the values in a form
	 * 
	 * @param string $sFormId the id of the HTML form
	 * @return array
	 */
	protected function lxForm($sFormId)
	{
		return $this->request->form($sFormId);
	}

	/**
	 * Get the value of an input field
	 * 
	 * @param string $sInputId the id of the HTML input element
	 * @return array
	 */
	protected function lxInput($sInputId)
	{
		return $this->request->input($sInputId);
	}

	/**
	 * Get the value of a checkbox field
	 * 
	 * @param string $sInputId the name of the HTML checkbox element
	 * @return array
	 */
	protected function lxCheckbox($sInputId)
	{
		return $this->request->checked($sInputId);
	}

	/**
	 * Get the value of a select field
	 * 
	 * @param string $sInputId the name of the HTML checkbox element
	 * @return array
	 */
	protected function lxSelect($sInputId)
	{
		return $this->request->checked($sInputId);
	}

	/**
	 * Get the value of a element in the DOM
	 * 
	 * @param string $sElementId the id of the HTML element
	 * @return array
	 */
	protected function lxHtml($sElementId)
	{
		return $this->request->html($sElementId);
	}

	/**
	 * Return a string value
	 * 
	 * @param string $sValue the value of the parameter
	 * @return array
	 */
	protected function lxString($sValue)
	{
		return $this->request->string($sValue);
	}

	/**
	 * Return a numeric value
	 * 
	 * @param numeric $nValue the value of the parameter
	 * @return array
	 */
	protected function lxNumeric($nValue)
	{
		return $this->request->numeric($nValue);
	}

	/**
	 * Return an integer value
	 * 
	 * @param numeric $nValue the value of the parameter
	 * @return array
	 */
	protected function lxInteger($nValue)
	{
		return $this->request->integer($nValue);
	}

	/**
	 * Return a javascript expression
	 * 
	 * @param string $sValue the Js code of the parameter
	 * @return array
	 */
	protected function lxJavascript($sValue)
	{
		return $this->request->javascript($sValue);
	}

	/**
	 * Return a page number
	 * 
	 * @return array
	 */
	protected function lxPageNumber()
	{
		return $this->request->pageNumber();
	}
}
