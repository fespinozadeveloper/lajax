<?php namespace Lagdo\Lajax\Pagination;

class Presenter extends \Illuminate\Pagination\Presenter
{
	protected $xajaxRequest = '';

	/**
	 * Create a new Presenter instance.
	 *
	 * @param  \Illuminate\Pagination\Paginator  $paginator
	 * @return void
	 */
	public function __construct(\Illuminate\Pagination\Paginator $paginator, $xajaxRequest)
	{
		parent::__construct($paginator);
		$this->xajaxRequest = $xajaxRequest;
	}

	/**
	 * Get HTML wrapper for a page link.
	 *
	 * @param  string  $url
	 * @param  int  $page
	 * @return string
	 */
	public function getPageLinkWrapper($url, $page, $rel = null)
	{
		if($page == '&laquo;') // Prev page
			$number = $this->currentPage - 1;
		else if($page == '&raquo;') // Next page
			$number = $this->currentPage + 1;
		else
			$number = $page;
		return '<li><a href="javascript:;" onclick="' . $this->xajaxRequest->getScript($number) .
			';return false;">' . $page . '</a></li>';
	}

	/**
	 * Get HTML wrapper for disabled text.
	 *
	 * @param  string  $text
	 * @return string
	 */
	public function getDisabledTextWrapper($text)
	{
		return '<li class="disabled"><span>' . $text . '</span></li>';
	}

	/**
	 * Get HTML wrapper for active text.
	 *
	 * @param  string  $text
	 * @return string
	 */
	public function getActivePageWrapper($text)
	{
		return '<li class="active"><span>' . $text . '</span></li>';
	}
}
