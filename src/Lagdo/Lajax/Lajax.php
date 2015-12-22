<?php namespace Lagdo\Lajax;

class Lajax
{
	protected $xajax = null;
	protected $response = null;

	protected $cbEventBefore = null;
	protected $cbEventAfter = null;
	protected $cbEventInit = null;

	// Array of registered Xajax controllers, and their requests
	protected $controllers = array();
	protected $requests = array();
	// Directory where class files are found
	protected $controllerDir;
	// Extension of controllers files
	protected $extension = '.php';

	public function __construct($requestRoute, $controllerDir)
	{
		$this->xajax = new \xajax($requestRoute);
		// $this->response = \xajax::getGlobalResponse();
		$this->response = new Response();
		$this->controllerDir = $controllerDir;
	}

	public function hasRequest()
	{
		return $this->xajax->canProcessRequest();
	}

	public function response()
	{
		return $this->response;
	}

	public function javascript()
	{
		return $this->xajax->getJavascript();
	}

	public function js()
	{
		return $this->xajax->getJavascript();
	}

	public function configure($param, $value)
	{
		return $this->xajax->configure($param, $value);
	}

	public function setEventInit($closure)
	{
		$this->cbEventInit = $closure;
	}

	public function setEventBefore($closure)
	{
		$this->cbEventBefore = $closure;
	}

	public function setEventAfter($closure)
	{
		$this->cbEventAfter = $closure;
	}

	public function registerClass($classname)
	{
		$classname = str_replace(array('\\', '/'), array('.', '.'), $classname);
		// Remove trailing dots
		$classname = trim($classname, '.');
		$classpath = '';
		$classfile = $classname;
		if(($lastDotPos = strrpos($classname, '.')) !== false)
		{
			$classpath = substr($classname, 0, $lastDotPos);
			$classname = substr($classname, $lastDotPos + 1);
			$classfile = str_replace('.', '/', $classpath) . '/' . $classname;
		}
		// Set the namespace, if defined
		if(($namespace = trim(\Config::get('lajax::app.namespace'), '\\')))
		{
			$classname = '\\' . $namespace . '\\' . str_replace('/', '\\', $classfile);
		}

		// Return the controller if it already exists
		if(array_key_exists($classname, $this->controllers))
		{
			return $this->controllers[$classname];
		}
		// Create an instance of the controller
		require_once($this->controllerDir . '/' . $classfile . $this->extension);
		$controller = new $classname;
		// Add in the controllers array
		$this->controllers[$classname] = $controller;
		// Register as a callable object in the Xajax library
		$config = array();
		if(($classpath))
		{
			$config['*'] = array('classpath' => $classpath);
		}

		$controller->requests = $this->xajax->register(XAJAX_CALLABLE_OBJECT, $controller, $config);
		return $controller;
	}

	public function registerClasses(array $classnames)
	{
		array_unique($classnames);
		foreach($classnames as $classname)
		{
			$this->registerClass($classname);
		}
	}

	public function register()
	{
		$dir = $this->controllerDir;
		foreach (\File::allFiles($dir) as $file)
		{
			// Vérifier l'extension du fichier
			if($file->isFile() && $this->extension == '.' . $file->getExtension())
			{
				// Retrouver le nom de la classe
				$classname = $file->getBasename($this->extension);
				$filepath = $file->getPath();
				if($filepath != $this->controllerDir)
				{
					$classname = substr($filepath, strlen($this->controllerDir) + 1) . '/' . $classname;
				}
				$this->registerClass($classname);
			}
		}
	}

	protected function initController($controller)
	{
		// Si le controller a déjà été initialisé, ne rien faire
		if(($controller->response))
		{
			return;
		}
		// Placer les données dans le controleur
		$controller->response = $this->response;
		if(($this->cbEventInit))
		{
			$cb = $this->cbEventInit;
			$cb($controller);
		}
		$controller->__init();
	}

	public function controller($classname)
	{
		$controller = $this->registerClass($classname);
		$this->initController($controller);
		return $controller;
	}

	public function eventBefore(&$bEndRequest)
	{
		// Include called class
		$class = $_POST['xjxcls'];
		$method = $_POST['xjxmthd'];
		// Todo : check $class ans $method validity and exit in case of error
		$controller = $this->controller($class);

		// Set the actual controller class name in the Xajax request plugin,
		// so the Xajax library can invoke the right callable object. 
		$xajaxPluginManager = \xajaxPluginManager::getInstance();
		$xajaxCallableObjectPlugin = $xajaxPluginManager->getRequestPlugin('xajaxCallableObjectPlugin');
		$xajaxCallableObjectPlugin->setRequestedClass(get_class($controller));

		// Call the user defined callback
		if(($this->cbEventBefore))
		{
			$cb = $this->cbEventBefore;
			$cb($class, $method, $bEndRequest);
		}
		return $this->response;
	}

	public function eventAfter()
	{
		if(($this->cbEventAfter))
		{
			$cb = $this->cbEventAfter;
			$cb();
		}
		return $this->response;
	}

	public function processRequest()
	{
		// Process Xajax Request
		$this->xajax->register(XAJAX_PROCESSING_EVENT, XAJAX_PROCESSING_EVENT_BEFORE, array($this, 'eventBefore'));
		$this->xajax->register(XAJAX_PROCESSING_EVENT, XAJAX_PROCESSING_EVENT_AFTER, array($this, 'eventAfter'));
		if($this->xajax->canProcessRequest())
		{
			// Traiter la requete
			$this->xajax->processRequest();
		}
	}

	private function setRequestParameters(&$xajaxRequest, array $parameters)
	{
		$xajaxRequest->clearParameters();
		$xajaxRequest->useSingleQuote();
		foreach($parameters as $param)
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

	public function call($controller, $method, array $parameters = array())
	{
		if(!is_object($controller))
			$controller = $this->controller($controller);
		// The Xajax library turns the method names into lower case chars.
		$method = strtolower($method);
		// Check if the xajax method exists
		if(!array_key_exists($method, $controller->requests))
		{
			return '';
		}
		$request = $controller->requests[$method];
		$this->setRequestParameters($request, $parameters);
		return $request->getScript();
	}
	
	public function paginate($currentPage, $itemsPerPage, $itemsTotal, $controller, $method, array $parameters = array())
	{
		if(!is_object($controller))
			$controller = $this->controller($controller);
		// The Xajax library turns the method names into lower case chars.
		$method = strtolower($method);
		// Check if the xajax method exists
		if(!array_key_exists($method, $controller->requests))
		{
			return null;
		}
		// Since this request is to be stored in the Presenter class, it has to be cloned
		$request = clone $controller->requests[$method];
		$this->setRequestParameters($request, $parameters);
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

?>
