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
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Console command for checking if there are pending extension updates
 *
 * @since  4.0.0
 */
class CheckJoomlaUpdatesCommand extends AbstractCommand
{
	/**
	 * Execute the command.
	 *
	 * @return  integer  The exit code for the command.
	 *
	 * @since   4.0.0
	 */
	public function execute(): int
	{
		$data = $this->getUpdateInformation();
		$symfonyStyle = new SymfonyStyle($this->getApplication()->getConsoleInput(), $this->getApplication()->getConsoleOutput());
		$symfonyStyle->title('Joomla! Updates');
		if ($data['hasUpdate'])
		{
			$symfonyStyle->success('You already have the latest Joomla version ' . $data['latest']);
		}
		else
		{
			$symfonyStyle->note('New Joomla Version ' . $data['latest'] . ' is available.');
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
		$this->setName('check-update');
		$this->setDescription('Checks for Joomla updates');
		$this->setHelp(
			<<<EOF
The <info>%command.name%</info> Checks for Joomla updates

<info>php %command.full_name%</info>
EOF
		);
	}

	/**
	 * Retrieves Update Information
	 * @return mixed
	 *
	 * @since version
	 * @throws \Exception
	 */
	public function getUpdateInformation() {
		$app = \JFactory::getApplication();
		$updatemodel = $app->bootComponent('com_joomlaupdate')->createMVCFactory($app)->createModel('Update', 'Administrator');
		return $updatemodel->getUpdateInformation();
	}
}
