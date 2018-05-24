<?php
/**
 * @package    Joomla.SystemTest
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class MainTest extends \PHPUnit\Framework\TestCase
{
	/** @var string */
	private $result;

	/** @var string */
	private $halo;

	/** @var string[] */
	private $sections;

	public function setUp()
	{
		$this->result   = `php cli/joomla.php`;
		$parts          = preg_split("~\n(.*?):\n~", $this->result, -1, PREG_SPLIT_DELIM_CAPTURE);
		$this->halo     = array_shift($parts);
		$this->sections = [];

		while (!empty($parts))
		{
			$this->sections[array_shift($parts)] = array_shift($parts);
		}
	}

	/**
	 * @testdox Output from  `joomla` without sub-command starts with "Joomla!"
	 */
	public function testOutputContainsJoomla()
	{
		$this->assertStringStartsWith('Joomla!', $this->halo, 'Message should start with "Joomla!"');
	}

	/**
	 * @testdox Output from  `joomla` without sub-command contains usage instructions
	 */
	public function testOutputContainsUsage()
	{
		$this->assertArrayHasKey('Usage', $this->sections, 'Message should contain usage instructions');
	}

	/**
	 * @testdox Output from  `joomla` without sub-command contains a list of available commands
	 */
	public function testOutputContainsAvailableCommands()
	{
		$this->assertArrayHasKey('Available commands', $this->sections,'Message should contain a list of available commands');
	}

	public function expectedCommands(): array
	{
		return [
			['help'],
			['list'],
			['cache:clean'],
			['session:gc'],
			['update:extensions:check'],
			['update:joomla:remove-old-files'],
		];
	}

	/**
	 * @dataProvider expectedCommands
	 * @testdox      The list of available commands contains expected commands

	 */
	public function testExpectedCommandsAreAvailable($expected)
	{
		$available = $this->sections['Available commands'];
		$this->assertRegExp('~(^|\n)  ' . preg_quote($expected, '~') . '  ~', $available,"'$expected' command should be available");
	}
}
