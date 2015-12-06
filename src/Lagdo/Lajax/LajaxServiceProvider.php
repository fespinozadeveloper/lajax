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
			$requestRoute = \Config::get('lajax::app.route', '/xajax');
			$controllerDir = \Config::get('lajax::app.controllers', app_path() . '/ajax/controllers');
			$extensionDir = \Config::get('lajax::app.extensions', app_path() . '/ajax/extensions');

			// Create the Xajax object
			$xajax = new Lajax($requestRoute, $controllerDir);

			// Ces inclusions doivent se placer après la création de l'objet xajax
			// mais avant les appels à la méthode configure
			foreach (\File::files($extensionDir) as $file)
			{
				if(\File::extension($file) == "php")
		        {
		        	require_once($file);
		        }
			}

			// Dir and URL of Javascript files
			$defaultJsDir = public_path('/packages/lagdo/xajax/js');
			$defaultJsUrl = asset('/packages/lagdo/xajax/js');
			// Xajax library config
			$xajax->configure('wrapperPrefix', \Config::get('lajax::lib.wrapperPrefix', 'Xajax'));
			$xajax->configure('characterEncoding', \Config::get('lajax::lib.characterEncoding', 'UTF-8'));
			$xajax->configure('deferScriptGeneration', \Config::get('lajax::lib.deferScriptGeneration', false));
			$xajax->configure('deferDirectory', \Config::get('lajax::lib.deferDirectory', 'deferred'));
			$xajax->configure('javascript URI', \Config::get('lajax::lib.javascript_URI', $defaultJsUrl));
			$xajax->configure('javascript Dir', \Config::get('lajax::lib.javascript_Dir', $defaultJsDir));
			$xajax->configure('errorHandler', \Config::get('lajax::lib.errorHandler', false));
			$xajax->configure('debug', \Config::get('lajax::lib.debug', false));

			return $xajax;
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
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('lajax');
	}
}
