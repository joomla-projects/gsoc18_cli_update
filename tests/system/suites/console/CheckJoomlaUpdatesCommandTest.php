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
	 * @testdox Output from  `joomla help check-update` contains usage instructions
	 *
	 * @since 4.0
	 */
	public function testOutputContainsUsage()
	{
		$result   = `php cli/joomla.php help check-update`;
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
	 * @testdox 'check-update' cleans the system cache
	 *
	 * @since 4.0
	 */
	public function testUpdateCheck()
	{
		$result = `php cli/joomla.php check-update`;
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
