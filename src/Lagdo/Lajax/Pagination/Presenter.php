<?php namespace Lagdo\Lajax\Pagination;

use Illuminate\Pagination\Presenter as LaravelPresenter;

class Presenter extends LaravelPresenter
{
	protected $handler = 'page';
	protected $params;

	/**
	 * Create a new Presenter instance.
	 *
	 * @param  \Illuminate\Pagination\Paginator  $paginator
	 * @return void
	 */
	public function __construct(\Illuminate\Pagination\Paginator $paginator, $handler, array $params = array())
	{
		parent::__construct($paginator);
		$this->handler = $handler;
		$this->params = $params;
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
			$pageNum = $this->currentPage - 1;
		else if($page == '&raquo;') // Next page
			$pageNum = $this->currentPage + 1;
		else
			$pageNum = $page;
		$params = array_merge($this->params, array($pageNum));
		return '<li><a href="javascript:void(0)" onclick="' . $this->handler . '(' .
			implode(',', $params) . ');return false;">' . $page . '</a></li>';
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
