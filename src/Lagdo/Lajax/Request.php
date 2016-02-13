<?php

namespace Lagdo\Lajax;

use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class Request
{
	protected $lajax = null;

	/**
	 * Create a new Request instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->lajax = \App::make('lajax');
	}

	/**
	 * Save the parameters in the Xajax request object
	 *
	 * @param object $xajaxRequest the Xajax request
	 * @param array $parameters the parameters of the request
	 * @return string
	 */
	private function setParameters(&$xajaxRequest, array $parameters)
	{
		$xajaxRequest->clearParameters();
		$xajaxRequest->useSingleQuote();
		foreach($parameters as $param)
		{
			if(is_numeric($param))
			{
				$xajaxRequest->addParameter(XAJAX_NUMERIC_VALUE, $param);
			}
			else if(is_string($param))
			{
				$xajaxRequest->addParameter(XAJAX_QUOTED_VALUE, $param);
			}
			else if(is_array($param))
			{
				$xajaxRequest->addParameter($param[0], $param[1]);
			}
		}
	}

	/**
	 * Return javascript the call to an Xajax controller method
	 *
	 * @param string|object $controller the controller
	 * @param string $method the name of the method
	 * @param array $parameters the parameters of the method
	 * @return string
	 */
	public function call($controller, $method, array $parameters = array())
	{
		if(is_string($controller))
			$controller = $this->lajax->controller($controller);
		if(!is_object($controller))
			return '';
		// The Xajax library turns the method names into lower case chars.
		$method = strtolower($method);
		// Check if the xajax method exists
		if(!array_key_exists($method, $controller->requests))
		{
			return '';
		}
		$request = $controller->requests[$method];
		$this->setParameters($request, $parameters);
		return $request->getScript();
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
	public function paginate($currentPage, $itemsPerPage, $itemsTotal, $controller, $method, array $parameters = array())
	{
		if(is_string($controller))
			$controller = $this->lajax->controller($controller);
		if(!is_object($controller))
			return '';
		// The Xajax library turns the method names into lower case chars.
		$method = strtolower($method);
		// Check if the xajax method exists
		if(!array_key_exists($method, $controller->requests))
		{
			return null;
		}
		// Since multiple requests can be created with different sets of parameters, they have to be cloned.
		$request = clone $controller->requests[$method];
		$this->setParameters($request, $parameters);
		// Append the page number to the parameter list, if not yet given.
		if(!$request->hasPageNumber())
		{
			$request->addParameter(XAJAX_PAGE_NUMBER, 0);
		}

		$paginator = new Paginator(array(), $itemsTotal, $itemsPerPage, $currentPage);
		// Set the Laravel paginator to use our presenter 
		Paginator::presenter(function($paginator) use ($request)
		{
			return new Pagination\Presenter($paginator, $request);
		});
		view()->share('paginator', $paginator);
		return $paginator;
	}

	/**
	 * Make a parameter of type XAJAX_FORM_VALUES
	 * 
	 * @param string $sFormId the id of the HTML form
	 * @return array
	 */
	public function form($sFormId)
	{
		return array(XAJAX_FORM_VALUES, $sFormId);
	}

	/**
	 * Make a parameter of type XAJAX_INPUT_VALUE
	 * 
	 * @param string $sInputId the id of the HTML input element
	 * @return array
	 */
	public function input($sInputId)
	{
		return array(XAJAX_INPUT_VALUE, $sInputId);
	}

	/**
	 * Make a parameter of type XAJAX_CHECKED_VALUE
	 * 
	 * @param string $sInputId the name of the HTML form element
	 * @return array
	 */
	public function checked($sInputId)
	{
		return array(XAJAX_CHECKED_VALUE, $sInputId);
	}

	/**
	 * Make a parameter of type XAJAX_ELEMENT_INNERHTML
	 * 
	 * @param string $sElementId the id of the HTML element
	 * @return array
	 */
	public function html($sElementId)
	{
		return array(XAJAX_ELEMENT_INNERHTML, $sElementId);
	}

	/**
	 * Make a parameter of type XAJAX_QUOTED_VALUE
	 * 
	 * @param string $sValue the value of the parameter
	 * @return array
	 */
	public function string($sValue)
	{
		return array(XAJAX_QUOTED_VALUE, $sValue);
	}

	/**
	 * Make a parameter of type XAJAX_NUMERIC_VALUE
	 * 
	 * @param numeric $nValue the value of the parameter
	 * @return array
	 */
	public function numeric($nValue)
	{
		return array(XAJAX_NUMERIC_VALUE, $nValue);
	}

	/**
	 * Make a parameter of type XAJAX_NUMERIC_VALUE
	 * 
	 * @param numeric $nValue the value of the parameter
	 * @return array
	 */
	public function integer($nValue)
	{
		return $this->numeric(intval($nValue));
	}

	/**
	 * Make a parameter of type XAJAX_JS_VALUE
	 * 
	 * @param string $sValue the javascript code of the parameter
	 * @return array
	 */
	public function javascript($sValue)
	{
		return array(XAJAX_JS_VALUE, $sValue);
	}

	/**
	 * Make a parameter of type XAJAX_PAGE_NUMBER
	 * 
	 * @return array
	 */
	public function pageNumber()
	{
		// By default, the value of a parameter of type XAJAX_PAGE_NUMBER is 0.
		return array(XAJAX_PAGE_NUMBER, 0);
	}
}
