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

	// Singleton
	protected static $instance = null;

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

	public function initController($controller)
	{
		// Si le controller a déjà été initialisé, ne rien faire
		if(($controller->response()))
		{
			return;
		}
		// Placer les données dans le controleur
		$controller->setResponse($this->response);
		if(($this->cbEventInit))
		{
			$cb = $this->cbEventInit;
			$cb($controller);
		}
		$controller->__init();
	}

	public function registerClass($classname)
	{
		$classname = str_replace(array('\\', '/'), array('.', '.'), $classname);
		// Remove trailing dots
		$classname = trim($classname, '.');
		$classpath = '';
		$classfile = '/' . $classname . $this->extension;
		if(($lastDotPos = strrpos($classname, '.')) !== false)
		{
			$classpath = substr($classname, 0, $lastDotPos);
			$classname = substr($classname, $lastDotPos + 1);
			$classfile = '/' . str_replace('.', '/', $classpath) . '/' . $classname . $this->extension;
		}
		require_once($this->controllerDir . $classfile);
		// Create an instance of the controller
		$controller = new $classname;
		// Add in controllers array
		$this->controllers[$classname] = $controller;
		// Enregistrer le controleur dans la librairie Xajax
		$config = array();
		if(($classpath))
		{
			$config['*'] = array('classpath' => $classpath);
		}
		$requests = $this->xajax->register(XAJAX_CALLABLE_OBJECT, $controller, $config);
		$controller->setRequests($requests);
		return $controller;
	}

	public function registerClasses(array $classnames)
	{
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

	public function eventBefore(&$bEndRequest)
	{
		// Include called class
		$class = $_POST['xjxcls'];
		$method = $_POST['xjxmthd'];
		$xajaxPluginManager = \xajaxPluginManager::getInstance();
		$xajaxCallableObjectPlugin = $xajaxPluginManager->getRequestPlugin('xajaxCallableObjectPlugin');

		// Todo : check $class ans $method validity and return in case of error
		$controller = $this->getController($class);
		$xajaxCallableObjectPlugin->setRequestedClass(get_class($controller));

		if(($this->cbEventBefore))
		{
			$cb = $this->cbEventBefore;
			$cb($class, $method, $bEndRequest);
		}
		// Call __init method
		if(!$bEndRequest)
		{
			$this->initController($controller);
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

	public function getController($classname)
	{
		$controller = $this->registerClass($classname);
		$this->initController($controller);
		return $controller;
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
}

?>
