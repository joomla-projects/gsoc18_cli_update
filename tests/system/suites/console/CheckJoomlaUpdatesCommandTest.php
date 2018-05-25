<?php
/**
 * @package    Joomla.SystemTest
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class CheckJoomlaUpdatesCommandTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @testdox Output from  `joomla help check-updates` contains usage instructions
	 *
	 * @since 4.0
	 */
	public function testIfCommandOutputContainsUsageInformation()
	{
		$result   = `php cli/joomla.php help check-updates`;
		$parts    = $this->splitResult($result);
		array_shift($parts);
		$sections = [];

		while (!empty($parts))
		{
			$sections[array_shift($parts)] = array_shift($parts);
		}

		$this->assertArrayHasKey('Usage', $sections, 'Message should contain usage instructions');
	}

	/**
	 * @testdox 'check-updates' tells whether there is update or not
	 *
	 * @since 4.0
	 */
	public function testIfThereIsJoomlaUpdate()
	{
		$result = `php cli/joomla.php check-updates`;
		$possible_results = ['[NOTE] New Joomla Version', '[OK] You already have the latest Joomla version'];
		$bool = (strpos($result, $possible_results[0]) || strpos($result, $possible_results[1]));
		$this->assertEquals(true, $bool, 'Checking of Update not successful');
	}

	/**
	 * Splits Command results
	 *
	 * @param $result
	 *
	 * @return array[]|false|string[]
	 *
	 * @since 4.0
	 */
	private function splitResult($result)
	{
		$parts    = preg_split("~(?:^|\n)(.*?):\n~", $result, -1, PREG_SPLIT_DELIM_CAPTURE);
		return $parts;
	}
}
