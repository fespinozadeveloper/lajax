<?php namespace Lagdo\Lajax\Facades;

use Illuminate\Support\Facades\Facade;

class Request extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'lajax.request';
	}
}
