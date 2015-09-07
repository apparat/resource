<?php

/**
 * Bauwerk
 *
 * @category    Jkphl
 * @package     Jkphl_Bauwerk
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2015 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2015 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation files (the "Software"), to deal in
 *  the Software without restriction, including without limitation the rights to
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 ***********************************************************************************/

namespace BauwerkTest;

use Bauwerk\Resource\File\Generic;

/**
 * Basic tests for generic files
 *
 * @package BauwerkTest
 */
class FileTest extends TestBase {
	/**
	 * Example text data
	 *
	 * @var array
	 */
	protected $_text = null;

	/**
	 * Example text file
	 *
	 * @var string
	 */
	const TXT_FILE = __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'cc0.txt';

	/**
	 * Repeated example text file
	 *
	 * @var string
	 */
	const TXT_PLUSPLUS_FILE = __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'cc0++.txt';

	/**
	 * Sets up the fixture
	 */
	protected function setUp() {
		parent::setUp();

		$this->_text = file_get_contents(self::TXT_FILE);
	}

	/**
	 * Test constructor and content without argument
	 */
	public function testContentWithoutFilePath() {
		$file = new Generic();
		$this->assertInstanceOf('Bauwerk\Resource\File\Generic', $file);
		$this->assertEquals(1, count($file));
		$this->assertEquals(0, strlen($file));
	}

	/**
	 * Test constructor and content with invalid file path
	 *
	 * @expectedException \Bauwerk\Resource\File\Exception\InvalidArgument
	 * @expectedExceptionCode 1440346451
	 */
	public function testContentWithInvalidFilePath() {
		$file = new Generic(__DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'invalid-'.time());
	}

	/**
	 * Test constructor and content with directory path
	 *
	 * @expectedException \Bauwerk\Resource\File\Exception\InvalidArgument
	 * @expectedExceptionCode 1440347668
	 */
	public function testContentWithDirectoryPath() {
		$file       = new Generic(__DIR__.DIRECTORY_SEPARATOR.'files');
	}

	/**
	 * Test invalid part key read access
	 *

	 */
	public function testInvalidPartKeyReadAccess() {
		$file       = new Generic(self::TXT_FILE);
//		$file->getPart('invalid');
	}
}