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

	/**
	 * Create a new Lajax instance.
	 *
	 * @return void
	 */
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

	/**
	 * Check if the current request is an Xajax request.
	 *
	 * @return boolean  True if the request is Xajax, false otherwise.
	 */
	public function hasRequest()
	{
		return $this->xajax->canProcessRequest();
	}

	/**
	 * Get the Xajax response.
	 *
	 * @return object  the Xajax response
	 */
	public function response()
	{
		return $this->response;
	}

	/**
	 * Get the javascript code generated for all registered classes.
	 *
	 * @return string  the javascript code
	 */
	public function javascript()
	{
		return $this->xajax->getJavascript();
	}

	/**
	 * Get the javascript code generated for all registered classes.
	 *
	 * @return string  the javascript code
	 */
	public function js()
	{
		return $this->xajax->getJavascript();
	}

	/**
	 * Configure a parameter in the Xajax library.
	 *
	 * @param  string  $param the parameter name
	 * @param  string  $value the parameter value
	 * @return voif
	 */
	public function configure($param, $value)
	{
		$this->xajax->configure($param, $value);
	}

	/**
	 * Set the init callback, used to initialise controllers.
	 *
	 * @param  callable  $callable the callback function
	 * @return void
	 */
	public function setInitCallback($callable)
	{
		$this->initCallback = $callable;
	}

	/**
	 * Set the pre-request processing callback.
	 *
	 * @param  callable  $callable the callback function
	 * @return void
	 */
	public function setPreCallback($callable)
	{
		$this->preCallback = $callable;
	}

	/**
	 * Set the post-request processing callback.
	 *
	 * @param  callable  $callable the callback function
	 * @return void
	 */
	public function setPostCallback($callable)
	{
		$this->postCallback = $callable;
	}

	/**
	 * Register all the plugins defined in the extension directory.
	 *
	 * @return void
	 */
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

	/**
	 * Register a controller class.
	 *
	 * @param  string  $name the class name
	 * @return object  an instance of the controller
	 */
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
			'*' => array('excluded' => $this->excluded($controller))
		);
		if(($classpath))
		{
			$config['*']['classpath'] = $classpath;
		}

		$controller->requests = $this->xajax->register(XAJAX_CALLABLE_OBJECT, $controller, $config);
		return $controller;
	}

	/**
	 * Register an array of controller classes.
	 *
	 * @param  array  $names the class names
	 * @return void
	 */
	public function registerClasses(array $names)
	{
		array_unique($names);
		foreach($names as $name)
		{
			$this->registerClass($name);
		}
	}

	/**
	 * Register all the classes defined in the controller directory.
	 *
	 * @return void
	 */
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

	/**
	 * Initialise a controller.
	 *
	 * @return void
	 */
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

	/**
	 * Get a controller instance.
	 *
	 * @param  string  $classname the controller class name
	 * @return object  an instance of the controller
	 */
	public function controller($classname)
	{
		$controller = $this->registerClass($classname);
		$this->initController($controller);
		return $controller;
	}

	/**
	 * Return an array of methods that should not be exported to javascript
	 *
	 * @param object $controller an instance of an Xajax controller
	 * @return array The list of excluded methods
	 */
	protected function excluded($controller)
	{
		// Methods that should not be exported
		if(property_exists($controller, 'excluded') && is_array($controller->excluded))
		{
			return array_merge($this->excluded, array_values($controller->excluded));
		}
		return $this->excluded;
	}

	/**
	 * This is the pre-request processing callback passed to the Xajax library.
	 *
	 * @param  boolean  &$bEndRequest if set to true, the request processing is interrupted.
	 * @return object  the Xajax response
	 */
	public function preProcess(&$bEndRequest)
	{
		// Instanciate the called class
		$class = $_POST['xjxcls'];
		$method = $_POST['xjxmthd'];
		// Todo : check and sanitize $class and $method inputs
		// Instanciate the controller. This will include the required file.
		$this->controller = $this->controller($class);
		if(!$this->controller)
		{
			// End the request processing if a controller cannot be found.
			$bEndRequest = true;
			return $this->response;
		}

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

	/**
	 * This is the post-request processing callback passed to the Xajax library.
	 *
	 * @return object  the Xajax response
	 */
	public function postProcess()
	{
		if(($this->postCallback))
		{
			$cb = $this->postCallback;
			$cb($this->controller);
		}
		return $this->response;
	}

	/**
	 * Process the current Xajax request.
	 *
	 * @return void
	 */
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
