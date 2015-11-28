<?php namespace Lagdo\Lajax;

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

	public function paginate($currentPage, $itemsPerPage, $itemsTotal, $xajaxMethod, $xajaxParams = false)
	{
		$xajaxClass = get_class($this);
		$paginator = \Paginator::make(array(), $itemsTotal, $itemsPerPage);
		$presenter = new Pagination\Presenter($paginator, $xajaxClass, $xajaxMethod, $xajaxParams);
		$presenter->setCurrentPage($currentPage);
		\View::share('presenter', $presenter);
		\View::share('paginator', $paginator);
		return $paginator;
	}
}
