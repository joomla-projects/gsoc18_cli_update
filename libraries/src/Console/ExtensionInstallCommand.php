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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Joomla\CMS\Factory;

/**
 * Console command for checking if there are pending extension updates
 *
 * @since  4.0.0
 */
class ExtensionInstallCommand extends AbstractCommand
{
	/**
	 * Stores the Input Object
	 * @var
	 * @since 4.0
	 */
	private $cliInput;

	/**
	 * SymfonyStyle Object
	 * @var
	 * @since 4.0
	 */
	private $ioStyle;

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

		$from = $this->cliInput->getArgument('from');

		if (!in_array($from, ['path', 'url']))
		{
			$this->ioStyle->error('You can either specify a path or url.');
			return 2;
		}

		if ($from == 'path')
		{
			$this->processPathInstallation($this->cliInput->getOption('path'));
		}
		else if ($from == 'url')
		{
			$this->processUrlInstallation($this->cliInput->getOption('url'));
		}
		else
		{
			$this->ioStyle->error('Invalid Argument for command.');
		}
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
		$this->setName('extension:install');
		$this->addArgument(
			'from',
			InputArgument::REQUIRED,
			'From where do you want to install? (path OR url)'
		);

		$this->addOption('path', null, InputOption::VALUE_REQUIRED, 'The path to the extension');
		$this->addOption('url', null, InputOption::VALUE_REQUIRED, 'The url to the extension');

		$this->setDescription('Installs an extension from a URL or from a Path.');
		$this->setHelp(
			<<<EOF
The <info>%command.name%</info> is used for installing extensions
--path=<path_to_extension> OR --url=<url_to_download_extension>

<info>php %command.full_name%</info>
EOF
		);
	}

	/**
	 * Used for installing extension from a path
	 *
	 * @param   $path  Path to the extension zip file
	 *
	 * @return bool|int
	 *
	 * @since 4.0
	 *
	 * @throws \Exception
	 */
	public function processPathInstallation($path)
	{
		if (!file_exists($path))
		{
			$this->ioStyle->error('The file path specified does not exist.');
			return 2;
		}
		$tmp_path = Factory::getApplication()->get('tmp_path');
		$tmp_path     = $tmp_path . '/' . basename($path);
		$package  = \JInstallerHelper::unpack($path, true);

		if ($package['type'] === false)
		{
			return false;
		}

		$jInstaller = \JInstaller::getInstance();
		$result     = $jInstaller->install($package['extractdir']);
		\JInstallerHelper::cleanupInstall($path, $package['extractdir']);
	}


	/**
	 * Used for installing extension from a URL
	 *
	 * @param   $url  URL to the extension zip file
	 *
	 * @return bool
	 *
	 * @since 4.0
	 *
	 * @throws \Exception
	 */
	public function processUrlInstallation($url)
	{
		$filename = \JInstallerHelper::downloadPackage($url);

		$tmp_path = Factory::getApplication()->get('tmp_path');
		$path     = $tmp_path . '/' . basename($filename);
		$package  = \JInstallerHelper::unpack($filename, true);
		if ($package['type'] === false)
		{
			return false;
		}

		$jInstaller = \JInstaller::getInstance();
		$result     = $jInstaller->install($package['extractdir']);
		\JInstallerHelper::cleanupInstall($path, $package['extractdir']);

		return $result;
	}
}
