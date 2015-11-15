<?php namespace Lagdo\Lajax\Xajax;

class Response extends \xajaxResponse
{
	public function __construct()
	{
		parent::__construct();
	}

	public function http($code = '200')
	{
		$httpResponse = Response::make($this->getOutput(), $code);
		$httpResponse->header('Content-Type', $this->getContentType() . ';charset="' . $this->getCharacterEncoding() . '"');
		return $httpResponse;
	}
}
