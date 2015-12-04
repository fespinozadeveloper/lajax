<?php namespace Lagdo\Lajax;

class Controller
{
	// Controller classpath
	protected $classpath = '';
	// Application data
	protected $response = null;
	// Javascripts requests to this class
	protected $requests = array();

	public function __construct()
	{}

	public function __init()
	{}

	public function classpath()
	{
		return $this->classpath;
	}

	public function setClasspath($classpath)
	{
		$this->classpath = $classpath;
	}

	public function response()
	{
		return $this->response;
	}

	public function setResponse($response)
	{
		$this->response = $response;
	}

	public function setRequests($requests)
	{
		$this->requests = $requests;
	}

	private function setRequestParameters(&$xajaxRequest, array $xajaxParameters)
	{
		$xajaxRequest->clearParameters();
		$xajaxRequest->useSingleQuote();
		foreach($xajaxParameters as $param)
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

	public function getScript($xajaxMethod, array $xajaxParameters = array())
	{
		// Check if the xajax method exists
		if(!array_key_exists($xajaxMethod, $this->requests))
		{
			return '';
		}
		$request = $this->requests[$xajaxMethod];
		$this->setRequestParameters($request, $xajaxParameters);
		return $request->getScript();
	}

	public function paginate($currentPage, $itemsPerPage, $itemsTotal, $xajaxMethod, array $xajaxParameters = array())
	{
		// Check if the xajax method exists
		if(!array_key_exists($xajaxMethod, $this->requests))
		{
			return null;
		}
		// Since this request is to be stored in the Presenter class, it has to be cloned 
		$request = clone $this->requests[$xajaxMethod];
		$this->setRequestParameters($request, $xajaxParameters);
		// Append the page number to the parameter list, if not yet given.
		if(!$request->hasPageNumber())
		{
			$request->addParameter(XAJAX_PAGE_NUMBER, 0);
		}

		$paginator = \Paginator::make(array(), $itemsTotal, $itemsPerPage);
		$presenter = new Pagination\Presenter($paginator, $request);
		$presenter->setCurrentPage($currentPage);
		\View::share('presenter', $presenter);
		\View::share('paginator', $paginator);
		return $paginator;
	}

	/**
	 * Make a parameter of type XAJAX_FORM_VALUES
	 * 
	 * @param string $sFormId the id of the HTML form
	 * @return array
	 */
	public function pForm($sFormId)
	{
		return array(XAJAX_FORM_VALUES, $sFormId);
	}

	/**
	 * Make a parameter of type XAJAX_INPUT_VALUE
	 * 
	 * @param string $sInputId the id of the HTML input element
	 * @return array
	 */
	public function pInput($sInputId)
	{
		return array(XAJAX_INPUT_VALUE, $sInputId);
	}

	/**
	 * Make a parameter of type XAJAX_CHECKED_VALUE
	 * 
	 * @param string $sCheckedId the name of the HTML form element
	 * @return array
	 */
	public function pChecked($sCheckedId)
	{
		return array(XAJAX_CHECKED_VALUE, $sCheckedId);
	}

	/**
	 * Make a parameter of type XAJAX_ELEMENT_INNERHTML
	 * 
	 * @param string $sElementId the id of the HTML element
	 * @return array
	 */
	public function pHtml($sElementId)
	{
		return array(XAJAX_ELEMENT_INNERHTML, $sElementId);
	}

	/**
	 * Make a parameter of type XAJAX_QUOTED_VALUE
	 * 
	 * @param string $sValue the value of the parameter
	 * @return array
	 */
	public function pQuoted($sValue)
	{
		return array(XAJAX_QUOTED_VALUE, $sValue);
	}

	/**
	 * Make a parameter of type XAJAX_QUOTED_VALUE
	 * 
	 * @param string $sValue the value of the parameter
	 * @return array
	 */
	public function pStr($sValue)
	{
		return $this->pQuoted($sValue);
	}

	/**
	 * Make a parameter of type XAJAX_NUMERIC_VALUE
	 * 
	 * @param numeric $nValue the value of the parameter
	 * @return array
	 */
	public function pNumeric($nValue)
	{
		return array(XAJAX_NUMERIC_VALUE, $nValue);
	}

	/**
	 * Make a parameter of type XAJAX_NUMERIC_VALUE
	 * 
	 * @param numeric $nValue the value of the parameter
	 * @return array
	 */
	public function pInt($nValue)
	{
		return $this->pNumeric(intval($nValue));
	}

	/**
	 * Make a parameter of type XAJAX_JS_VALUE
	 * 
	 * @param string $sValue the Js code of the parameter
	 * @return array
	 */
	public function pJs($sValue)
	{
		return array(XAJAX_JS_VALUE, $sValue);
	}

	/**
	 * Make a parameter of type XAJAX_PAGE_NUMBER
	 * 
	 * @return array
	 */
	public function pPage()
	{
		// By default, the value of a parameter of type XAJAX_PAGE_NUMBER is 0.
		return array(XAJAX_PAGE_NUMBER, 0);
	}
}
