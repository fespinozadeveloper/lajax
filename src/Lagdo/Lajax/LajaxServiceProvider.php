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
		// Register the controllers namespace and dir for autoloading
		if(($namespace = trim(config('lajax.app.namespace'), '\\')))
		{
			$loader = require base_path() . '/vendor/autoload.php';
			$loader->setPsr4($namespace . '\\', config('lajax.app.controllers', app_path() . '/ajax/controllers'));
		}

		// Config source and destination files
		$configSrcFile = __DIR__ . '/../../config/config.php';
		$configDstFile = config_path('lajax.php');
		// The assets are actually those of the lagdo/xajax package, and they should be copied to the public directory.
		$xajaxJsSrcDir = __DIR__ . '/../../../../xajax/xajax_js';
		$xajaxJsDstDir = config('lajax.lib.javascript_Dir', public_path('/packages/lagdo/xajax/js'));
		// Publish assets and config
		$this->publishes([
			$configSrcFile => $configDstFile,
			$xajaxJsSrcDir => $xajaxJsDstDir,
		]);

		// Define the helpers
		require_once(__DIR__ . 'helpers.php');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Register the Lajax singleton
		$this->app->singleton('Lajax', function ($app)
		{
			// Xajax application config
			$requestRoute = config('lajax.app.route', 'xajax');
			$controllerDir = config('lajax.app.controllers', app_path() . '/ajax/controllers');
			$extensionDir = config('lajax.app.extensions', app_path() . '/ajax/extensions');
			$excluded = config('lajax.app.excluded', array());
			$namespace = trim(config('lajax.app.namespace'), '\\');

			// Create the Xajax object
			$lajax = new Lajax($requestRoute, $namespace, $controllerDir, $extensionDir, $excluded);

			// Register Xajax plugins
			$lajax->registerPlugins();

			// Dir and URL of Javascript files
			$defaultJsDir = public_path('/packages/lagdo/xajax/js');
			$defaultJsUrl = asset('/packages/lagdo/xajax/js');
			// Xajax library config
			$lajax->configure('wrapperPrefix', config('lajax.lib.wrapperPrefix', 'Xajax'));
			$lajax->configure('characterEncoding', config('lajax.lib.characterEncoding', 'UTF-8'));
			$lajax->configure('deferScriptGeneration', config('lajax.lib.deferScriptGeneration', false));
			$lajax->configure('deferDirectory', config('lajax.lib.deferDirectory', 'deferred'));
			$lajax->configure('javascript URI', config('lajax.lib.javascript_URI', $defaultJsUrl));
			$lajax->configure('javascript Dir', config('lajax.lib.javascript_Dir', $defaultJsDir));
			$lajax->configure('errorHandler', config('lajax.lib.errorHandler', false));
			$lajax->configure('debug', config('lajax.lib.debug', false));

			return $lajax;
		});

		// Register the Lajax Request singleton
		$this->app->bind('lajax.request', function()
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
