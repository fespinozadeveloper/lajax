<?php

/**
 * Return the javascript call to an Xajax controller method
 *
 * @param string|object $controller the controller
 * @param string $method the name of the method
 * @param array $parameters the parameters of the method
 * @return string
 */
function lxCall($controller, $method, array $parameters = array())
{
	return \App::make('lajax.request')->call($controller, $method, $parameters);
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
function lxPaginate($currentPage, $itemsPerPage, $itemsTotal, $controller, $method, array $parameters = array())
{
	return \App::make('lajax.request')->paginate($currentPage, $itemsPerPage, $itemsTotal, $controller, $method, $parameters);
}

/**
 * Make a parameter of type XAJAX_FORM_VALUES
 *
 * @param string $sFormId the id of the HTML form
 * @return array
 */
function lxForm($sFormId)
{
	return \App::make('lajax.request')->form($sFormId);
}

/**
 * Make a parameter of type XAJAX_INPUT_VALUE
 *
 * @param string $sInputId the id of the HTML input element
 * @return array
 */
function lxInput($sInputId)
{
	return \App::make('lajax.request')->input($sInputId);
}

/**
 * Make a parameter of type XAJAX_CHECKED_VALUE
 *
 * @param string $sCheckedId the name of the HTML form element
 * @return array
 */
function lxChecked($sCheckedId)
{
	return \App::make('lajax.request')->checked($sCheckedId);
}

/**
 * Make a parameter of type XAJAX_ELEMENT_INNERHTML
 *
 * @param string $sElementId the id of the HTML element
 * @return array
 */
function lxHtml($sElementId)
{
	return \App::make('lajax.request')->html($sElementId);
}

/**
 * Make a parameter of type XAJAX_QUOTED_VALUE
 *
 * @param string $sValue the value of the parameter
 * @return array
 */
function lxQuoted($sValue)
{
	return \App::make('lajax.request')->quoted($sValue);
}

/**
 * Make a parameter of type XAJAX_QUOTED_VALUE
 *
 * @param string $sValue the value of the parameter
 * @return array
 */
function lxStr($sValue)
{
	return \App::make('lajax.request')->str($sValue);
}

/**
 * Make a parameter of type XAJAX_NUMERIC_VALUE
 *
 * @param numeric $nValue the value of the parameter
 * @return array
 */
function lxNumeric($nValue)
{
	return \App::make('lajax.request')->numeric($nValue);
}

/**
 * Make a parameter of type XAJAX_NUMERIC_VALUE
 *
 * @param numeric $nValue the value of the parameter
 * @return array
 */
function lxInt($nValue)
{
	return \App::make('lajax.request')->int($nValue);
}

/**
 * Make a parameter of type XAJAX_JS_VALUE
 *
 * @param string $sValue the Js code of the parameter
 * @return array
 */
function lxJs($sValue)
{
	return \App::make('lajax.request')->js($sValue);
}

/**
 * Make a parameter of type XAJAX_PAGE_NUMBER
 *
 * @return array
 */
function lxPage()
{
	return \App::make('lajax.request')->page();
}
