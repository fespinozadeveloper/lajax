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

	public function jsCall($xajaxMethod, array $xajaxParams)
	{
		// Check if the xajax method exists
		if(!array_key_exists($xajaxMethod, $this->requests))
		{
			return '';
		}
		$request = $this->requests[$xajaxMethod];
		$request->clearParameters();
		// By default, there is always at least one argument, the page number.
		if(count($xajaxParams) == 0)
		{
			$xajaxParams[] = '{number}';
		}
		foreach($xajaxParams as $param)
		{
			if($param == '{number}' || is_numeric($param))
			{
				$request->addParameter(XAJAX_JS_VALUE, $param);
			}
			else if(is_string($param))
			{
				$request->addParameter(XAJAX_QUOTED_VALUE, $param);
			}
			else if(is_array($param))
			{
				$request->addParameter($param[0], $param[1]);
			}
		}
		return $request->getScript();
	}

	public function paginate($currentPage, $itemsPerPage, $itemsTotal, $xajaxMethod, array $xajaxParams = array())
	{
		$paginator = \Paginator::make(array(), $itemsTotal, $itemsPerPage);
		$presenter = new Pagination\Presenter($paginator, $this->jsCall($xajaxMethod, $xajaxParams), '{page}');
		$presenter->setCurrentPage($currentPage);
		\View::share('presenter', $presenter);
		\View::share('paginator', $paginator);
		return $paginator;
	}
}
