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
		exec('php cli/joomla.php help cache:clean', $result);
		$sections = array_flip($result);
		$this->assertArrayHasKey('Usage:', $sections, 'Message should contain usage instructions');
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

		exec('php cli/joomla.php cache:clean', $result, $code);
		$this->assertTrue(in_array(' [OK] Cache cleaned', $result));
		$this->assertFileNotExists($filename, "Cache file should have been removed");
		$this->assertEquals(0, $code, 'The Command did not execute successfully.');
	}

	/**
	 * @depends testCleanCache
	 * @testdox 'cache:clean' cleans empty system cache successfully
	 */
	public function testCleanEmptyCache()
	{
		exec('php cli/joomla.php cache:clean', $result);
		$this->assertContains('[OK] Cache cleaned', implode("\n", $result));
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

		exec('php cli/joomla.php cache:clean', $result);

		$this->assertContains('[OK] Cache cleaned', implode("\n", $result));
		$this->assertFileExists($filename, "Special file '$file' should not have been removed");
		unlink($filename);
	}
}
