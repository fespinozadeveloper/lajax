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
		// The Xajax library turns the method names into lower case chars.
		$xajaxMethod = strtolower($xajaxMethod);
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
		// The Xajax library turns the method names into lower case chars.
		$xajaxMethod = strtolower($xajaxMethod);
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
}
