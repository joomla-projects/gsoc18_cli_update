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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Joomla\CMS\Factory;

/**
 * Console command for listing available languages
 *
 * @since  4.0.0
 */
class LanguagesListCommand extends AbstractCommand
{
	/*
	 * Stores the available Languages
	 */
	private $languages;

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
		$languages = $this->getLanguages();

		if (empty($languages))
		{
			$this->ioStyle->error("Cannot find available languages.");
			return 0;
		}

		$languages = $this->getLanguagesInfo($languages);

		$this->ioStyle->title('Available languages.');
		$this->ioStyle->table(['Language', 'Tag', 'Version', 'Url'], $languages);
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
		$this->setName('language:list');
		$this->setDescription('List available languages');

		$help = "The <info>%command.name%</info> List all currently available languages
				\nUsage: <info>php %command.full_name%</info>";

		$this->setHelp($help);
	}

	/**
	 * Transforms language arrays into required form
	 *
	 * @param   array  $languages  Array of languages
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	private function getLanguagesInfo($languages)
	{
		$langInfo = [];
		foreach ($languages as $key => $language)
		{
			$langInfo[] = [
				$language->name,
				substr($language->element, 4),
				$language->version,
				$language->detailsurl,
			];
		}

		return $langInfo;
	}

	/**
	 * Get the Update Site
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @return  string  The URL of the Accredited Languagepack Updatesite XML
	 */
	private function getUpdateSite()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('us.location'))
			->from($db->qn('#__extensions', 'e'))
			->where($db->qn('e.type') . ' = ' . $db->q('package'))
			->where($db->qn('e.element') . ' = ' . $db->q('pkg_en-GB'))
			->where($db->qn('e.client_id') . ' = 0')
			->join('LEFT', $db->qn('#__update_sites_extensions', 'use') . ' ON ' . $db->qn('use.extension_id') . ' = ' . $db->qn('e.extension_id'))
			->join('LEFT', $db->qn('#__update_sites', 'us') . ' ON ' . $db->qn('us.update_site_id') . ' = ' . $db->qn('use.update_site_id'));

		return $db->setQuery($query)->loadResult();
	}

	/**
	 * Gets an array of objects from the updatesite.
	 *
	 * @return  object[]  An array of results.
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \RuntimeException
	 */
	protected function getLanguages()
	{
		$updateSite = $this->getUpdateSite();

		try
		{
			$response = \JHttpFactory::getHttp()->get($updateSite);
		}
		catch (\RuntimeException $e)
		{
			$response = null;
		}

		if ($response === null || $response->code !== 200)
		{
			$this->ioStyle->error("Cannot find extensions of the type '$type' specified.");
			return 0;
		}

		$updateSiteXML = simplexml_load_string($response->body);
		$languages     = array();

		foreach ($updateSiteXML->extension as $extension)
		{
			$language = new \Joomla\CMS\Object\CMSObject;

			foreach ($extension->attributes() as $key => $value)
			{
				$language->$key = (string) $value;
			}

			$languages[$language->name] = $language;
		}

		ksort($languages);

		return $languages;
	}
}
