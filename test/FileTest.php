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
		echo \Bauwerk\Resource\File\Part\Body\Generic::class;
	}

	/**
	 * Test constructor and content without argument
	 */
	public function testContentWithoutFilePath() {
		$file       = new \Bauwerk\Resource\File();
		$this->assertInstanceOf('Bauwerk\Resource\File', $file);
		$this->assertEquals(1, count($file));
		$this->assertEquals(0, strlen($file->getContent()));
	}

	/**
	 * Test constructor and content with invalid file path
	 *
	 * @expectedException \Bauwerk\Resource\File\InvalidArgumentException
	 * @expectedExceptionCode 1440346451
	 */
	public function testContentWithInvalidFilePath() {
		$file       = new \Bauwerk\Resource\File(__DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'invalid-'.time());
		$this->assertInstanceOf('Bauwerk\Resource\File', $file);
		$this->assertEquals(0, count($file));
		$file->getContent();
	}

	/**
	 * Test constructor and content with directory path
	 *
	 * @expectedException \Bauwerk\Resource\File\InvalidArgumentException
	 * @expectedExceptionCode 1440347668
	 */
	public function testContentWithDirectoryPath() {
		$file       = new \Bauwerk\Resource\File(__DIR__.DIRECTORY_SEPARATOR.'files');
		$this->assertInstanceOf('Bauwerk\Resource\File', $file);
		$this->assertEquals(0, count($file));
		$file->getContent();
	}

	/**
	 * Test invalid part key read access
	 *
	 * @expectedException \Bauwerk\Resource\Container\OutOfRangeException
	 * @expectedExceptionCode 1440524242
	 */
	public function testInvalidPartKeyReadAccess() {
		$file       = new \Bauwerk\Resource\File(self::TXT_FILE);
		$file->getPart('invalid');
	}

	/**
	 * Test invalid part key write access
	 *
	 * @expectedException \Bauwerk\Resource\Container\OutOfRangeException
	 * @expectedExceptionCode 1440524242
	 */
	public function testInvalidPartKeyWriteAccess() {
		$file       = new \Bauwerk\Resource\File(self::TXT_FILE);
		$file->setPart('invalid', new \Bauwerk\Resource\Part('invalid'));
	}

	/**
	 * Test constructor and content with valid path
	 */
	public function testContentWithTextFile() {
		$file       = new \Bauwerk\Resource\File(self::TXT_FILE);
		$this->assertInstanceOf('Bauwerk\Resource\File', $file);
		$this->assertEquals(1, count($file));
		$this->assertInstanceOf('Bauwerk\Resource\Part', $file->getPart(\Bauwerk\Resource\Part::DEFAULT_NAME));

		// Iterate through all parts
		$count		= 0;
		foreach ($file as $part) {
			$this->assertInstanceOf('Bauwerk\Resource\Part', $part);
			++$count;
		}
		$this->assertEquals(1, $count);

		$this->assertStringEqualsFile(self::TXT_FILE, $file->getContent());
	}

	/**
	 * Test content manipulation via getContent() / setContent()
	 */
	public function testContentManipulationViaContent() {
		$file       = new \Bauwerk\Resource\File(self::TXT_FILE);
		$content	= $file->getContent();
		$file->setContent($content.$content);
		$this->assertStringEqualsFile(self::TXT_PLUSPLUS_FILE, $file->getContent());
	}

	/**
	 * Test content manipulation via getPart() / setPart()
	 */
	public function testContentManipulationViaPart() {
		$file       = new \Bauwerk\Resource\File(self::TXT_FILE);
		$content	= strval($file->getPart(\Bauwerk\Resource\Part::DEFAULT_NAME));
		$file->setPart(\Bauwerk\Resource\Part::DEFAULT_NAME, new \Bauwerk\Resource\Part($content.$content));
		$this->assertStringEqualsFile(self::TXT_PLUSPLUS_FILE, $file->getContent());
	}

	/**
	 * Save with no target file
	 *
	 * @expectedException \Bauwerk\Resource\File\InvalidArgumentException
	 * @expectedExceptionCode 1440361529
	 */
	public function testSaveWithNoTarget() {
		$file       = new \Bauwerk\Resource\File();
		$file->save();
	}

	/**
	 * Save with non-existing directory
	 *
	 * @expectedException \Bauwerk\Resource\File\RuntimeException
	 * @expectedExceptionCode 1440361552
	 */
	public function testSaveWithInvalidDirectory() {
		$file       = new \Bauwerk\Resource\File();
		$tmpFile	= $this->_createTemporaryFile();
		@unlink($tmpFile);
		$file->save($tmpFile.DIRECTORY_SEPARATOR.'invalid');
	}

	/**
	 * Save with already existing target file
	 *
	 * @expectedException \Bauwerk\Resource\File\RuntimeException
	 * @expectedExceptionCode 1440526568
	 */
	public function testSaveWithExistingTarget() {
		$file       = new \Bauwerk\Resource\File();
		$tmpFile	= $this->_createTemporaryFile();
		@unlink($tmpFile);
		@mkdir($tmpFile, 0777);
		$tmpFile	.= DIRECTORY_SEPARATOR.'existing';
		@touch($tmpFile);
		array_unshift($this->_tmpFiles, $tmpFile);
		$file->save($tmpFile);
	}

	/**
	 * Save with already existing target file
	 *
	 * @expectedException \Bauwerk\Resource\File\RuntimeException
	 * @expectedExceptionCode 1440526568
	 */
	public function testSaveWithExistingTargetNoOverwrite() {
		$file       = new \Bauwerk\Resource\File();
		$tmpFile	= $this->_createTemporaryFile();
		@unlink($tmpFile);
		@mkdir($tmpFile, 0777);
		$tmpFile	.= DIRECTORY_SEPARATOR.'existing';
		@touch($tmpFile);
		array_unshift($this->_tmpFiles, $tmpFile);
		$file->save($tmpFile);
	}

	/**
	 * Save with already existing target file
	 */
	public function testSaveWithEmptyBody() {
		$file       = new \Bauwerk\Resource\File();
		$tempFile	= $this->_createTemporaryFile();
		$this->assertEquals(0, $file->save($tempFile, true, true));
		$file->setSource(self::TXT_FILE);
		$this->assertEquals(filesize(self::TXT_FILE), $file->save($tempFile, true, true));
	}
}