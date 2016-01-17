<?php namespace Lagdo\Lajax;

class Lajax
{
	protected $xajax = null;
	protected $response = null;

	protected $preCallback = null;
	protected $postCallback = null;
	protected $initCallback = null;

	// Array of registered Xajax controllers, and their requests
	protected $controllers = array();
	protected $requests = array();
	protected $excluded = array();
	// Requested controller
	protected $controller = null;
	// Directory where class files are found
	protected $controllerDir;
	// Directory where plugin files are found
	protected $extensionDir;
	// Extension of controllers files
	protected $extension = '.php';
	// Namespace of Xajax controllers
	protected $namespace = '';

	public function __construct($requestRoute, $namespace, $controllerDir, $extensionDir, $excluded)
	{
		$this->xajax = new \xajax($requestRoute);
		// $this->response = \xajax::getGlobalResponse();
		$this->response = new Response();
		$this->namespace = $namespace;
		$this->controllerDir = $controllerDir;
		$this->extensionDir = $extensionDir;

		if(is_array($excluded))
		{
			$this->excluded = array_values($excluded);
		}
		// The public methods of the Controller base class must not be exported to javascript
		$controllerClass = new \ReflectionClass('Lagdo\Lajax\Controller');
		foreach ($controllerClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $xMethod)
		{
			$this->excluded[] = $xMethod->getShortName();
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

	public function setInitCallback($callable)
	{
		$this->initCallback = $callable;
	}

	public function setPreCallback($callable)
	{
		$this->preCallback = $callable;
	}

	public function setPostCallback($callable)
	{
		$this->postCallback = $callable;
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

	public function registerClass($name)
	{
		$alias = str_replace(array('\\', '/'), array('.', '.'), $name);
		// Remove trailing dots
		$alias = trim($alias, '.');
		$classfile = str_replace('.', '/', $alias) . $this->extension;
		// Check if controller file exists
		if(!\File::exists($this->controllerDir . '/' . $classfile))
		{
			// Todo : throw an exception
			return null;
		}
		// Return the controller if it already exists
		if(array_key_exists($alias, $this->controllers))
		{
			return $this->controllers[$alias];
		}
		$classpath = '';
		$classname = $alias;
		if(($lastDotPos = strrpos($classname, '.')) !== false)
		{
			$classpath = substr($classname, 0, $lastDotPos);
			$classname = substr($classname, $lastDotPos + 1);
		}
		// Set the namespace, if defined
		if(($this->namespace))
		{
			$classname = '\\' . $this->namespace . '\\' . str_replace('.', '\\', $alias);
		}

		// Create an instance of the controller
		require_once($this->controllerDir . '/' . $classfile);
		$controller = new $classname;
		// Add in the controllers array
		$controller->alias = $alias;
		$this->controllers[$alias] = $controller;
		// Register as a callable object in the Xajax library
		$config = array(
			'*' => array('excluded' => $controller->excluded($this->excluded))
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
		if(!($controller) || ($controller->response))
		{
			return;
		}
		// Placer les données dans le controleur
		$controller->request = \App::make('lajax.request');
		$controller->response = $this->response;
		if(($this->initCallback))
		{
			$cb = $this->initCallback;
			$cb($controller);
		}
		$controller->init();
	}

	public function controller($classname)
	{
		$controller = $this->registerClass($classname);
		$this->initController($controller);
		return $controller;
	}

	public function preProcess(&$bEndRequest)
	{
		// Include called class
		$class = $_POST['xjxcls'];
		$method = $_POST['xjxmthd'];
		// Todo : Sanitize $class ans $method inputs
		$this->controller = $this->controller($class);

		// Set the actual controller class name in the Xajax request plugin,
		// so the Xajax library can invoke the right callable object. 
		$xajaxPluginManager = \xajaxPluginManager::getInstance();
		$xajaxCallableObjectPlugin = $xajaxPluginManager->getRequestPlugin('xajaxCallableObjectPlugin');
		$xajaxCallableObjectPlugin->setRequestedClass(get_class($this->controller));

		// Call the user defined callback
		if(($this->preCallback))
		{
			$cb = $this->preCallback;
			$cb($this->controller, $method, $bEndRequest);
		}
		return $this->response;
	}

	public function postProcess()
	{
		if(($this->postCallback))
		{
			$cb = $this->postCallback;
			$cb($this->controller);
		}
		return $this->response;
	}

	public function processRequest()
	{
		// Process Xajax Request
		$this->xajax->register(XAJAX_PROCESSING_EVENT, XAJAX_PROCESSING_EVENT_BEFORE, array($this, 'preProcess'));
		$this->xajax->register(XAJAX_PROCESSING_EVENT, XAJAX_PROCESSING_EVENT_AFTER, array($this, 'postProcess'));
		if($this->xajax->canProcessRequest())
		{
			// Traiter la requete
			$this->xajax->processRequest();
		}
	}
}

?>
