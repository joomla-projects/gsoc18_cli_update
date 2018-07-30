<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Console;

defined('JPATH_PLATFORM') or die;

use Joomla\Console\AbstractCommand;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Console command for displaying configuration options
 *
 * @since  4.0.0
 */
class GetConfigurationCommand extends AbstractCommand
{
	/**
	 * Stores the Input Object
	 * @var Input
	 * @since 4.0
	 */
	private $cliInput;

	/**
	 * SymfonyStyle Object
	 * @var SymfonyStyle
	 * @since 4.0
	 */
	private $ioStyle;

	/**
	 * Constant defining the Database option group
	 * @var array
	 * @since 4.0
	 */
	const DB_GROUP = ['name' => 'db', 'options' => ['dbtype', 'host', 'user', 'password', 'dbprefix', 'db']];

	/**
	 * Constant defining the Session option group
	 * @var array
	 * @since 4.0
	 */
	const SESSION_GROUP = ['name' => 'session', 'options' => ['session_handler', 'shared_session', 'session_metadata']];

	/**
	 * Constant defining the Mail option group
	 * @var array
	 * @since 4.0
	 */
	const MAIL_GROUP = [
				'name' => 'mail',
				'options' => [
					'mailonline', 'mailer', 'mailfrom',
					'fromname', 'sendmail', 'smtpauth',
					'smtpuser', 'smtppass', 'smtphost',
					'smtpsecure', 'smtpport'
				]
	];

	/**
	 * Configures the IO
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	private function configureIO()
	{
		$this->cliInput = $this->getApplication()->getConsoleInput();
		$this->ioStyle = new SymfonyStyle($this->getApplication()->getConsoleInput(), $this->getApplication()->getConsoleOutput());
	}


	/**
	 * Execute the command.
	 *
	 * @return  integer  The exit code for the command.
	 *
	 * @since   4.0.0
	 */
	public function execute(): int
	{
		$this->configureIO();

		$configs = $this->formatConfig($this->getApplication()->getConfig()->toArray());

		$option = $this->cliInput->getArgument('option');
		$group = $this->cliInput->getOption('group');

		if ($group)
		{
			return $this->processGroupOptions($group);
		}

		if ($option)
		{
			return $this->processSingleOption($option);
		}

		if (!$option && !$group)
		{
			$options = [];

			array_walk(
				$configs,
				function ($value, $key) use (&$options) {
					$options[] = [$key, $value];
				}
			);

			$this->ioStyle->title("Current options in Configuration");
			$this->ioStyle->table(['Option', 'Value'], $options);

			return 0;
		}

		return 1;
	}

	/**
	 * Displays logically grouped options
	 *
	 * @param   string  $group  The group to be processed
	 *
	 * @return integer
	 *
	 * @since 4.0
	 */
	public function processGroupOptions($group)
	{
		$configs = $this->getApplication()->getConfig()->toArray();
		$configs = $this->formatConfig($configs);

		$groups = $this->getGroups();

		$foundGroup = false;

		foreach ($groups as $key => $value)
		{
			if ($value['name'] === $group)
			{
				$foundGroup = true;
				$options = [];

				foreach ($value['options'] as $key => $option)
				{
					$options[] = [$option, $configs[$option]];
				}

				$this->ioStyle->table(['Option', 'Value'], $options);
			}
		}

		if (!$foundGroup)
		{
			$this->ioStyle->error("Group *$group* not found");
			exit;
		}

		return 0;
	}

	/**
	 * Gets the defined option groups
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function getGroups()
	{
		return [
				self::DB_GROUP,
				self::MAIL_GROUP,
				self::SESSION_GROUP
		];
	}

	/**
	 * Formats the configuration array into desired format
	 *
	 * @param   array  $configs  Array of the configurations
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function formatConfig($configs)
	{
		foreach ($configs as $key => $config)
		{
			$config = $config === false ? "false" : $config;
			$config = $config === true ? "true" : $config;

			if (!in_array($key, ['cwd', 'execution']))
			{
				$newConfig[$key] = $config;
			}
		}

		return $newConfig;
	}

	/**
	 * Handles the command when an single option is requested
	 *
	 * @param   string  $option  The option we want to get its value
	 *
	 * @return integer
	 *
	 * @since 4.0
	 */
	public function processSingleOption($option)
	{
		$configs = $this->getApplication()->getConfig()->toArray();

		if (!array_key_exists($option, $configs))
		{
			$this->ioStyle->error("Can't find option *$option* in configuration list");

			return 1;
		}

		$value = $this->getApplication()->get($option) ?: 'Not set';

		$this->ioStyle->table(['Option', 'Value'], [[$option, $value]]);

		return 0;
	}

	/**
	 * Initialise the command.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	protected function initialise()
	{
		$groups = $this->getGroups();

		foreach ($groups as $key => $group)
		{
			$groupNames[] = $group['name'];
		}

		$groupNames = implode(', ', $groupNames);

		$this->setName('config:get');
		$this->setDescription('Displays the current value of a configuration option');

		$this->addArgument('option', null, 'Name of the option');
		$this->addOption('group', 'g', InputOption::VALUE_REQUIRED, 'Name of the option');

		$help = "The <info>%command.name%</info> Displays the current value of a configuration option
				\nUsage: <info>php %command.full_name%</info> <option>
				\nGroup usage: <info>php %command.full_name%</info> --group=<groupname>
				\nAvailable group names: $groupNames";

		$this->setHelp($help);
	}
}
