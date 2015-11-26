<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat_<Package>
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

namespace Apparat\Resource\Framework\Io {

	/**
	 * Mocked version of the native copy() function
	 *
	 * @param string $source Source file
	 * @param string $dest Destination file
	 * @param resource $context Context resource
	 * @return bool
	 */
	function copy($source, $dest, $context = null)
	{
		$arguments = func_get_args();
		return empty($GLOBALS['mockCopy']) ? \copy(...$arguments) : false;
	}

	/**
	 * Mocked version of the native rename() function
	 *
	 * @param string $source Source file
	 * @param string $dest Destination file
	 * @param resource $context Context resource
	 * @return bool
	 */
	function rename($source, $dest, $context = null)
	{
		$arguments = func_get_args();
		return empty($GLOBALS['mockMove']) ? \rename(...$arguments) : false;
	}
}

namespace ApparatTest {

	use Apparat\Resource\Framework\Io\InMemory\Writer;
	use Apparat\Resource\Framework\Io\InvalidArgumentException;
	use Apparat\Resource\Framework\Io\Io;
	use Apparat\Resource\Framework\Io\RuntimeException;

	/**
	 * I/O handler test
	 *
	 * @package ApparatTest
	 */
	class IoTest extends TestBase
	{
		/**
		 * Example text file
		 *
		 * @var string
		 */
		const TXT_FILE = __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'cc0.txt';

		/**
		 * Test invalid reader stream wrapper while copying
		 *
		 * @expectedException InvalidArgumentException
		 * @expectedExceptionCode 1448493550
		 */
		public function testCopyInvalidReaderStreamWrapper()
		{
			Io::copy('foo://bar');
		}

		/**
		 * Test invalid writer stream wrapper while copying
		 *
		 * @expectedException InvalidArgumentException
		 * @expectedExceptionCode 1448493564
		 */
		public function testCopyInvalidWriterStreamWrapper()
		{
			Io::copy('file://'.self::TXT_FILE)->to('foo://bar');
		}

		/**
		 * Test copying a string to a file
		 */
		public function testCopyStringToFile()
		{
			$tempFile = $this->_createTemporaryFile(true);
			$randomString = md5(rand());
			Io::copy($randomString)->to('file://'.$tempFile);
			$this->assertStringEqualsFile($tempFile, $randomString);
		}

		/**
		 * Test copying a file to a file
		 */
		public function testCopyFileToFile()
		{
			$tempFile = $this->_createTemporaryFile(true);
			Io::copy('file://'.self::TXT_FILE)->to('file://'.$tempFile);
			$this->assertFileEquals($tempFile, self::TXT_FILE);
		}

		/**
		 * Test error while copying a file to a file
		 *
		 * @expectedException RuntimeException
		 * @expectedExceptionCode 1448569381
		 */
		public function testCopyFileToFileError()
		{
			$GLOBALS['mockCopy'] = true;
			$tempFile = $this->_createTemporaryFile(true);
			Io::copy('file://'.self::TXT_FILE)->to('file://'.$tempFile);
			$this->assertFileEquals($tempFile, self::TXT_FILE);
			unset($GLOBALS['mockCopy']);
		}

		/**
		 * Test copying a string to a string
		 */
		public function testCopyStringToString()
		{
			$randomString = md5(rand());
			/** @var Writer $writer */
			$writer = Io::copy($randomString)->to('');
			$this->assertInstanceOf(Writer::class, $writer);
			$this->assertEquals($randomString, $writer->getData());
		}

		/**
		 * Test copying a file to a string
		 */
		public function testCopyFileToString()
		{
			/** @var Writer $writer */
			$writer = Io::copy('file://'.self::TXT_FILE)->to('');
			$this->assertInstanceOf(Writer::class, $writer);
			$this->assertStringEqualsFile(self::TXT_FILE, $writer->getData());
		}

		/**
		 * Test invalid reader stream wrapper while moving
		 *
		 * @expectedException InvalidArgumentException
		 * @expectedExceptionCode 1448493550
		 */
		public function testMoveInvalidReaderStreamWrapper()
		{
			Io::move('foo://bar');
		}

		/**
		 * Test invalid writer stream wrapper while moving
		 *
		 * @expectedException InvalidArgumentException
		 * @expectedExceptionCode 1448493564
		 */
		public function testMoveInvalidWriterStreamWrapper()
		{
			Io::move('file://'.self::TXT_FILE)->to('foo://bar');
		}

		/**
		 * Test moving a string to a file
		 */
		public function testMoveStringToFile()
		{
			$tempFile = $this->_createTemporaryFile(true);
			$randomString = md5(rand());
			Io::move($randomString)->to('file://'.$tempFile);
			$this->assertStringEqualsFile($tempFile, $randomString);
		}

		/**
		 * Test moving a file to a file
		 */
		public function testMoveFileToFile()
		{
			$srcFile = $this->_createTemporaryFile(true);
			copy(self::TXT_FILE, $srcFile);
			$tempFile = $this->_createTemporaryFile(true);
			Io::move('file://'.$srcFile)->to('file://'.$tempFile);
			$this->assertFileEquals(self::TXT_FILE, $tempFile);
		}

		/**
		 * Test error while moving a file to a file
		 *
		 * @expectedException RuntimeException
		 * @expectedExceptionCode 1448571473
		 */
		public function testMoveFileToFileError()
		{
			$GLOBALS['mockMove'] = true;
			$tempFile = $this->_createTemporaryFile(true);
			Io::move('file://'.self::TXT_FILE)->to('file://'.$tempFile);
			$this->assertFileEquals($tempFile, self::TXT_FILE);
			unset($GLOBALS['mockMove']);
		}

		/**
		 * Test moving a string to a string
		 */
		public function testMoveStringToString()
		{
			$randomString = md5(rand());
			/** @var Writer $writer */
			$writer = Io::move($randomString)->to('');
			$this->assertInstanceOf(Writer::class, $writer);
			$this->assertEquals($randomString, $writer->getData());
		}

		/**
		 * Test moving a file to a string
		 */
		public function testMoveFileToString()
		{
			$srcFile = $this->_createTemporaryFile(true);
			copy(self::TXT_FILE, $srcFile);
			/** @var Writer $writer */
			$writer = Io::move('file://'.$srcFile)->to('');
			$this->assertInstanceOf(Writer::class, $writer);
			$this->assertStringEqualsFile(self::TXT_FILE, $writer->getData());
		}
	}
}