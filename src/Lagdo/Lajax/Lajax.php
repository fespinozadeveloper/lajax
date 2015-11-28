<?php namespace Lagdo\Lajax;

class Lajax
{
	protected $xajax = null;
	protected $response = null;

	protected $cbEventBefore = null;
	protected $cbEventAfter = null;
	protected $cbEventInit = null;

	// Array of registered Xajax controllers
	protected $controllers = array();
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

	public function registerClass($className)
	{
		require_once($this->controllerDir . '/' . $className . $this->extension);
		// Create en instance of controller
		$controller = new $className;
		// Add in controllers array
		$this->controllers[$className] = $controller;
		// Enregistrer le controleur dans la librairie Xajax
		$this->xajax->register(XAJAX_CALLABLE_OBJECT, $controller);
	}

	public function registerClasses(array $classNames)
	{
		foreach($classNames as $className)
		{
			$this->registerClass($className);
		}
	}

	public function register()
	{
		$dir = $this->controllerDir;
		foreach (\File::files($dir) as $file)
		{
			// Vérifier l'extension du fichier
			if(substr($file, -strlen($this->extension)) == $this->extension)
			{
				// Retrouver le nom de la classe
				$className = basename($file, $this->extension);
				if(($className))
		        {
		        	$this->registerClass($className);
		        }
			}
		}
	}

	public function eventBefore(&$bEndRequest)
	{
		// Include called class
		$class = $_POST['xjxcls'];
		$method = $_POST['xjxmthd'];
		// Todo : check $class ans $method validity and return in case of error
		$controller = $this->getController($class);
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

	public function getController($className)
	{
		if(!array_key_exists($className, $this->controllers))
		{
			$this->registerClass($className);
		}
		$controller = $this->controllers[$className];
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
