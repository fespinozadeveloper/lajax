<?php namespace Lagdo\Lajax\Pagination;

class Presenter extends \Illuminate\Pagination\Presenter
{
	protected $xajaxCall;

	/**
	 * Create a new Presenter instance.
	 *
	 * @param  \Illuminate\Pagination\Paginator  $paginator
	 * @return void
	 */
	public function __construct(\Illuminate\Pagination\Paginator $paginator, $xajaxClass, $xajaxMethod, $xajaxParams = false)
	{
		parent::__construct($paginator);
		// Params other than the page number
		$paramsBefore = '';
		$paramsAfter = '';
		if(is_array($xajaxParams))
		{
			// Params before the page number
			if(array_key_exists('b', $xajaxParams) && is_array($xajaxParams['b']))
			{
				foreach($xajaxParams['b'] as $param)
				{
					if(is_string($param))
						$paramsBefore .= "'" . addslashes($param) . "',";
					elseif(is_numeric($param))
						$paramsBefore .= $param . ",";
				}
			}
			// Add params after the page number
			if(array_key_exists('a', $xajaxParams) && is_array($xajaxParams['a']))
			{
				foreach($xajaxParams['a'] as $param)
				{
					if(is_string($param))
						$paramsAfter .= ",'" . addslashes($param) . "'";
					elseif(is_numeric($param))
						$paramsAfter .= "," . $param;
				}
			}
		}
		// The Xajax call to a page, with the page number and text as placeholders
		$xajaxPrefix = \Config::get('lajax::lib.wrapperPrefix', 'Xajax');
		$this->xajaxCall = '<li><a href="javascript:void(0)" onclick="' . $xajaxPrefix . $xajaxClass . '.' .
			$xajaxMethod . '(' . $paramsBefore . '{number}' . $paramsAfter . ');return false;">{page}</a></li>';
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
		return str_replace(array('{number}', '{page}'), array($number, $page), $this->xajaxCall);
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
