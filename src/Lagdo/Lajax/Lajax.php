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
	protected $excludedMethods = array();
	// Directory where class files are found
	protected $controllerDir;
	// Directory where plugin files are found
	protected $extensionDir;
	// Extension of controllers files
	protected $extension = '.php';

	public function __construct($requestRoute, $controllerDir, $extensionDir)
	{
		$this->xajax = new \xajax($requestRoute);
		// $this->response = \xajax::getGlobalResponse();
		$this->response = new Response();
		$this->controllerDir = $controllerDir;
		$this->extensionDir = $extensionDir;

		// The public methods of the Controller bas class must not be exported to javascript
		$controllerClass = new \ReflectionClass('Lagdo\Lajax\Controller');
		foreach ($controllerClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $xMethod)
		{
			$this->excludedMethods[] = $xMethod->getShortName();
		}
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

	public function registerPlugins()
	{
		// Register Xajax plugins
		foreach (\File::files($this->extensionDir) as $file)
		{
			if(\File::extension($file) == "php")
	        {
	        	require_once($file);
	        }
		}
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
		$config = array(
			'*' => array('exclude' => $this->excludedMethods)
		);
		if(($classpath))
		{
			$config['*']['classpath'] = $classpath;
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
}

?>
