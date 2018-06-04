<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Console;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Language\Text;
use Joomla\Console\AbstractCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Joomla\CMS\Factory;

/**
 * Console command for checking if there are pending extension updates
 *
 * @since  4.0.0
 */
class CheckJoomlaUpdatesCommand extends AbstractCommand
{
	/*
	 * Stores the Update Information
	 */
	private $updateInfo;

	/**
	 * Configures the IO
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	private function configureIO()
	{
		$language = Factory::getLanguage();
		$language->load('check_updates_cli', JPATH_SITE, null, false, false)
		// Fallback to the check_updates_cli file in the default language
		|| $language->load('check_updates_cli', JPATH_SITE, null, true);
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
		$symfonyStyle = new SymfonyStyle($this->getApplication()->getConsoleInput(), $this->getApplication()->getConsoleOutput());
		$data = $this->getUpdateInfo();
		$symfonyStyle->title(Text::_('CHECK_UPDATES_TITLE'));
		if ($data['hasUpdate'])
		{
			$symfonyStyle->success(Text::sprintf('UPDATES_NOT_AVAILABLE', $data['latest']));
		}
		else
		{
			$symfonyStyle->note(Text::sprintf('UPDATES_AVAILABLE', $data['latest']));
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
		$this->configureIO();
		$this->setName('check-updates');
		$this->setDescription(Text::_('CHECK_UPDATES_DESCRIPTION'));
		$this->setHelp(Text::_('CHECK_UPDATES_HELP'));
	}

	/**
	 * Retrieves Update Information
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	private function getUpdateInformationFromModel()
	{
		$app = Factory::getApplication();
		$updatemodel = $app->bootComponent('com_joomlaupdate')->createMVCFactory($app)->createModel('Update', 'Administrator');
		return $updatemodel->getUpdateInformation();
	}

	/**
	 * Gets the Update Information
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	public function getUpdateInfo()
	{
		if (!$this->updateInfo)
		{
			$this->setUpdateInfo();
			return $this->updateInfo;
		}
		else
		{
			return $this->updateInfo;
		}
	}

	/**
	 * Sets the Update Information
	 *
	 * @param   null  $info  stores update Information
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function setUpdateInfo($info = null)
	{
		if (!$info)
		{
			$this->updateInfo = $this->getUpdateInformationFromModel();
		}
		else
		{
			$this->updateInfo = $info;
		}
	}
}
