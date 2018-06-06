<?php
/**
 * @package    Joomla.SystemTest
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class ExtensionListCommandTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @testdox Output from  `joomla help extension:install` contains usage instructions
	 *
	 * @since 4.0
	 */
	public function testIfCommandOutputContainsUsageInformation()
	{
		exec('php cli/joomla.php help extension:list', $parts);
		$parts = array_flip($parts);
		$this->assertArrayHasKey("Usage:", $parts, 'Message should contain usage instructions');
	}

	/**
	 * @testdox 'extension:install' install an extension form url or path
	 *
	 * @since 4.0
	 */
	public function testIfExtensionsAreInstalled()
	{
		exec('php cli/joomla.php extension:install', $result, $code);
		$result = implode("\n", $result);
		$this->assertContains('Installed Extensions', $result, 'Command does not list installed extensions');
		$this->assertEquals(0, $code, 'Command did not return appropriate code.');
	}
}
