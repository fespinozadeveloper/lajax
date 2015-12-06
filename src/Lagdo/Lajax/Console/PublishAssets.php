<?php

namespace Lagdo\Lajax\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class PublishAssets extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'lajax:assets';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Publish the Xajax javascript files.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		// The assets are actually those of the lagdo/xajax package.
		$xajaxJsSrcDir = __DIR__ . '/../../../../../xajax/xajax_js';
		// They should be copied to the public directory
		$defaultJsDir = public_path('/packages/lagdo/xajax/js');
		$xajaxJsDstDir = \Config::get('lajax::lib.javascript_Dir', $defaultJsDir);
		if(!\File::isDirectory($xajaxJsSrcDir))
		{
			// Unable to find the Xajax javascript director
			$this->error('[lagdo/lajax] Error. Unable to find the Xajax javascript directory');
			return;
		}
		if(!\File::isDirectory($xajaxJsDstDir) && !\File::makeDirectory($xajaxJsDstDir, 0755, true))
		{
			// Unable to create the assets directory
			$this->error('[lagdo/lajax] Error. Unable to create the assets directory');
			return;
		}
		// Create the deferred dir
		if(\Config::get('lajax::lib.deferScriptGeneration', false))
		{
			$deferDirectory = $xajaxJsDstDir . '/' . \Config::get('lajax::lib.deferDirectory', 'deferred');
			if(!\File::isDirectory($deferDirectory) && !\File::makeDirectory($deferDirectory, 0755, true))
			{
				// Unable to create the deferred directory
				$this->error('[lagdo/lajax] Error. Unable to create the deferred directory');
				return;
			}
		}
		if(!\File::copyDirectory($xajaxJsSrcDir, $xajaxJsDstDir))
		{
			// Unable to copy the files
			$this->error('[lagdo/lajax] Error. Unable to copy the assets to the public directory');
			return;
		}
		$this->info('[lagdo/lajax] Success. The assets were copied to the public directory');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			// array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			// array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}
}
