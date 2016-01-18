<?php namespace Lagdo\Lajax;

use Illuminate\Support\ServiceProvider;

class LajaxServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('lagdo/lajax');
	
		// Register the controllers namespace and dir for autoloading
		if(($namespace = trim(\Config::get('lajax::app.namespace'), '\\')))
		{
			$loader = require base_path() . '/vendor/autoload.php';
			$loader->setPsr4($namespace . '\\', \Config::get('lajax::app.controllers', app_path() . '/ajax/controllers'));
		}

		// Define the helpers
		require_once(__DIR__ . '/Facades/helpers.php');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Register the Lajax singleton
		$this->app['lajax'] = $this->app->share(function($app)
		{
			// Xajax application config
			$requestRoute = \Config::get('lajax::app.route', 'xajax');
			$controllerDir = \Config::get('lajax::app.controllers', app_path() . '/ajax/controllers');
			$extensionDir = \Config::get('lajax::app.extensions', app_path() . '/ajax/extensions');
			$excluded = \Config::get('lajax::app.excluded', array());
			$namespace = trim(\Config::get('lajax::app.namespace'), '\\');

			// Create the Xajax object
			$lajax = new Lajax($requestRoute, $namespace, $controllerDir, $extensionDir, $excluded);

			// Register Xajax plugins
			$lajax->registerPlugins();

			// Dir and URL of Javascript files
			$defaultJsDir = public_path('/packages/lagdo/xajax/js');
			$defaultJsUrl = asset('/packages/lagdo/xajax/js');
			// Xajax library config
			$lajax->configure('wrapperPrefix', \Config::get('lajax::lib.wrapperPrefix', 'Xajax'));
			$lajax->configure('characterEncoding', \Config::get('lajax::lib.characterEncoding', 'UTF-8'));
			$lajax->configure('deferScriptGeneration', \Config::get('lajax::lib.deferScriptGeneration', false));
			$lajax->configure('deferDirectory', \Config::get('lajax::lib.deferDirectory', 'deferred'));
			$lajax->configure('javascript URI', \Config::get('lajax::lib.javascript_URI', $defaultJsUrl));
			$lajax->configure('javascript Dir', \Config::get('lajax::lib.javascript_Dir', $defaultJsDir));
			$lajax->configure('errorHandler', \Config::get('lajax::lib.errorHandler', false));
			$lajax->configure('debug', \Config::get('lajax::lib.debug', false));

			return $lajax;
		});

		// Register the Lajax commands
		$this->app['lajax::commands.config'] = $this->app->share(function($app)
		{
			return new Console\PublishConfig;
		});
		$this->app['lajax::commands.assets'] = $this->app->share(function($app)
		{
			return new Console\PublishAssets;
		});
		$this->commands(
			'lajax::commands.config',
			'lajax::commands.assets'
		);

		// Register the Lajax Request singleton
		$this->app['lajax.request'] = $this->app->share(function($app)
		{
			// Create the Xajax Request object
			$request = new Request();
			return $request;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('lajax, lajax.request');
	}
}
