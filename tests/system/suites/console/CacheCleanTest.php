<?php
/**
 * @package    Joomla.SystemTest
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class CacheCleanTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * @testdox Output from  `joomla help cache:clean` contains usage instructions
	 */
	public function testOutputContainsUsage()
	{
		$result   = `php cli/joomla.php help cache:clean`;
		$parts    = preg_split("~(?:^|\n)(.*?):\n~", $result, -1, PREG_SPLIT_DELIM_CAPTURE);
		$halo     = array_shift($parts);
		$sections = [];

		while (!empty($parts))
		{
			$sections[array_shift($parts)] = array_shift($parts);
		}

		$this->assertArrayHasKey('Usage', $sections, 'Message should contain usage instructions');
	}

	/**
	 * @testdox 'cache:clean' cleans the system cache
	 */
	public function testCleanCache()
	{
		$filename = 'administrator/cache/test.js';
		$template = 'administrator/cache/index.html';
		copy($template, $filename);
		touch($filename, strtotime("-1 year"));
		chown($filename, fileowner($template));
		chgrp($filename, filegroup($template));

		$result = `php cli/joomla.php cache:clean`;

		$this->assertContains('[OK] Cache cleaned', $result);
		$this->assertFileNotExists($filename, "Cache file should have been removed");
	}

	/**
	 * @depends testCleanCache
	 * @testdox 'cache:clean' cleans empty system cache successfully
	 */
	public function testCleanEmptyCache()
	{
		$result = `php cli/joomla.php cache:clean`;

		$this->assertContains('[OK] Cache cleaned', $result);
	}

	public function filesToKeep()
	{
		return [
			['.svn'],
			['CVS'],
			['.DS_Store'],
			['__MACOSX'],
		];
	}

	/**
	 * @dataProvider filesToKeep
	 * @testdox 'cache:clean' keeps special files
	 */
	public function testSpecialFilesAreRetained($file)
	{
		$filename = 'administrator/cache/' . $file;
		$template = 'administrator/cache/index.html';
		copy($template, $filename);
		touch($filename, strtotime("-1 year"));
		chown($filename, fileowner($template));
		chgrp($filename, filegroup($template));

		$result = `php cli/joomla.php cache:clean`;

		$this->assertContains('[OK] Cache cleaned', $result);
		$this->assertFileExists($filename, "Special file '$file' should not have been removed");
		unlink($filename);
	}
}
