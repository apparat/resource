<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
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

namespace ApparatTest;

use Apparat\Resource\Framework\Io\InMemory\Writer as InMemoryWriter;
use Apparat\Resource\Framework\Io\File\Writer as FileWriter;
use Apparat\Resource\Framework\Api\InvalidArgumentException;
use Apparat\Resource\Framework\Model\Resource\TextResource;

/**
 * Resource factory tests
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 */
class FactoryTest extends TestBase
{
	/**
	 * Example text data
	 *
	 * @var string
	 */
	protected $_text = null;

	/**
	 * Example text file
	 *
	 * @var string
	 */
	const TXT_FILE = __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'cc0.txt';

	/**
	 * Sets up the fixture
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->_text = file_get_contents(self::TXT_FILE);
	}

	/**
	 * Test the text resource factory with a string literal input (in-memory reader)
	 */
	public function testTextFactoryStringReader()
	{
		$randString = md5(rand());
		$textResource = TextResource::from($randString);
		$this->assertInstanceOf(TextResource::class, $textResource);
		$this->assertEquals($randString, $textResource->get());
		$this->assertEquals($randString, strval($textResource));
	}

	/**
	 * Test the text resource factory with a file path input (file reader)
	 */
	public function testTextFactoryFileReader()
	{
		$textResource = TextResource::from('file://'.self::TXT_FILE);
		$this->assertInstanceOf(TextResource::class, $textResource);
		$this->assertEquals($this->_text, $textResource->get());
	}

	/**
	 * Test the text resource factory with an invalid reader stream-wrapper
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1448493550
	 */
	public function testTextFactoryInvalidReader()
	{
		TextResource::from('foo://'.self::TXT_FILE);
	}

	/**
	 * Test the text resource factory with a string output (in-memory writer)
	 */
	public function testTextFactoryStringWriter()
	{
		$textResource = TextResource::from($this->_text);
		$writer = $textResource->to('');
		$this->assertInstanceOf(InMemoryWriter::class, $writer);
		$this->assertEquals($this->_text, $writer->getData());
	}

	/**
	 * Test the text resource factory with a file path (file reader)
	 */
	public function testTextFactoryFileWriter()
	{
		$tempFileName = $this->_createTemporaryFile(true);
		$textResource = TextResource::from($this->_text);
		$writer = $textResource->to('file://'.$tempFileName, FileWriter::FILE_CREATE);
		$this->assertInstanceOf(FileWriter::class, $writer);
		$this->assertStringEqualsFile($tempFileName, $this->_text);
	}

	/**
	 * Test the text resource factory with an invalid writer stream-wrapper
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1448493564
	 */
	public function testTextFactoryInvalidWriter()
	{
		TextResource::from($this->_text)->to('foo://'.$this->_createTemporaryFile(true));
	}
}