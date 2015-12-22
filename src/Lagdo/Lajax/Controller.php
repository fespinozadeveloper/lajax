<?php namespace Lagdo\Lajax;

class Controller
{
	protected $lajax = null;
	// Application data
	public $response = null;
	// Javascripts requests to this class
	public $requests = array();

	public function __construct()
	{
		$this->lajax = \App::make('lajax');
	}

	public function __init()
	{}

	protected function call($method, array $parameters = array())
	{
		return $this->lajax->call($this, $method, $parameters);
	}

	protected function paginate($currentPage, $itemsPerPage, $itemsTotal, $method, array $parameters = array())
	{
		return $this->lajax->paginate($currentPage, $itemsPerPage, $itemsTotal, $this, $method, $parameters);
	}

	/**
	 * Make a parameter of type XAJAX_FORM_VALUES
	 * 
	 * @param string $sFormId the id of the HTML form
	 * @return array
	 */
	protected function pForm($sFormId)
	{
		return $this->lajax->pForm($sFormId);
	}

	/**
	 * Make a parameter of type XAJAX_INPUT_VALUE
	 * 
	 * @param string $sInputId the id of the HTML input element
	 * @return array
	 */
	protected function pInput($sInputId)
	{
		return $this->lajax->pInput($sInputId);
	}

	/**
	 * Make a parameter of type XAJAX_CHECKED_VALUE
	 * 
	 * @param string $sCheckedId the name of the HTML form element
	 * @return array
	 */
	protected function pChecked($sCheckedId)
	{
		return $this->lajax->pChecked($sCheckedId);
	}

	/**
	 * Make a parameter of type XAJAX_ELEMENT_INNERHTML
	 * 
	 * @param string $sElementId the id of the HTML element
	 * @return array
	 */
	protected function pHtml($sElementId)
	{
		return $this->lajax->pHtml($sElementId);
	}

	/**
	 * Make a parameter of type XAJAX_QUOTED_VALUE
	 * 
	 * @param string $sValue the value of the parameter
	 * @return array
	 */
	protected function pQuoted($sValue)
	{
		return $this->lajax->pQuoted($sValue);
	}

	/**
	 * Make a parameter of type XAJAX_QUOTED_VALUE
	 * 
	 * @param string $sValue the value of the parameter
	 * @return array
	 */
	protected function pStr($sValue)
	{
		return $this->lajax->pStr($sValue);
	}

	/**
	 * Make a parameter of type XAJAX_NUMERIC_VALUE
	 * 
	 * @param numeric $nValue the value of the parameter
	 * @return array
	 */
	protected function pNumeric($nValue)
	{
		return $this->lajax->pNumeric($nValue);
	}

	/**
	 * Make a parameter of type XAJAX_NUMERIC_VALUE
	 * 
	 * @param numeric $nValue the value of the parameter
	 * @return array
	 */
	protected function pInt($nValue)
	{
		return $this->lajax->pInt($nValue);
	}

	/**
	 * Make a parameter of type XAJAX_JS_VALUE
	 * 
	 * @param string $sValue the Js code of the parameter
	 * @return array
	 */
	protected function pJs($sValue)
	{
		return $this->lajax->pJs($sValue);
	}

	/**
	 * Make a parameter of type XAJAX_PAGE_NUMBER
	 * 
	 * @return array
	 */
	protected function pPage()
	{
		// By default, the value of a parameter of type XAJAX_PAGE_NUMBER is 0.
		return $this->lajax->pPage();
	}
}
