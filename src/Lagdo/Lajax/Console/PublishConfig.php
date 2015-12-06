<?php

namespace Lagdo\Lajax\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class PublishConfig extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'lajax:config';

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
		// Call the config:publish command for this package
		$this->call('config:publish', array('package' => 'lagdo/lajax'));
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
