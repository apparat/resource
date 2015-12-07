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

use Apparat\Resource\Framework\Io\InMemory\Reader;
use Apparat\Resource\Framework\Io\InMemory\ReaderWriter;
use Apparat\Resource\Framework\Io\InMemory\Writer;
use Apparat\Resource\Framework\Model\Part\TextPart;
use Apparat\Resource\Framework\Model\Resource\TextResource;
use Apparat\Resource\Domain\Model\Part\InvalidArgumentException;
use Apparat\Resource\Domain\Model\Resource\RuntimeException;

/**
 * Text file tests
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 */
class TextTest extends TestBase
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
	 * Test the text file constructor
	 */
	public function testTextResource()
	{
		$textReource = new TextResource();
		$this->assertInstanceOf(TextResource::class, $textReource);
		$this->assertEquals(TextPart::MIME_TYPE, $textReource->getMimeTypePart());
	}

	/**
	 * Test the text file constructor with reader
	 */
	public function testTextResourceReader()
	{
		$textReource = new TextResource(new Reader($this->_text));
		$this->assertEquals($this->_text, $textReource->getPart());
	}

	/**
	 * Test the text file constructor with explicit loading
	 */
	public function testTextResourceLoad()
	{
		$textReource = new TextResource();
		$textReource->load(new Reader($this->_text));
		$this->assertEquals($this->_text, $textReource->getPart());
	}

	/**
	 * Test invalid file method
	 *
	 * @expectedException RuntimeException
	 * @expectedExceptionCode 1447450449
	 */
	public function testTextResourceInvalidMethod()
	{
		$textReource = new TextResource();
		$textReource->undefinedMethod();
	}

	/**
	 * Test setting the content part of a text file
	 */
	public function testTextResourceSetPart()
	{
		$randomSet = md5(rand());
		$textReource = new TextResource();
		$textReource->setPart($randomSet);
		$this->assertEquals($randomSet, $textReource->getPart());
	}

	/**
	 * Test setting the content of a text file
	 */
	public function testTextResourceSet()
	{
		$randomSet = md5(rand());
		$textReource = new TextResource();
		$textReource->set($randomSet);
		$this->assertEquals($randomSet, $textReource->getPart());
	}

	/**
	 * Test getting the content of a text file
	 */
	public function testTextResourceGet()
	{
		$textReource = new TextResource(new Reader($this->_text));
		$this->assertEquals($this->_text, $textReource->get());
	}

	/**
	 * Test setting and appending content to a text file part
	 */
	public function testTextResourceAppendPart()
	{
		$randomSet = md5(rand());
		$randomAppend = md5(rand());
		$textReource = new TextResource();
		$textReource->setPart($randomSet)->appendPart($randomAppend);
		$this->assertEquals($randomSet.$randomAppend, $textReource->getPart());
	}

	/**
	 * Test appending content to a text file
	 */
	public function testTextResourceAppend()
	{
		$randomSet = md5(rand());
		$randomAppend = md5(rand());
		$textReource = new TextResource();
		$textReource->set($randomSet)->append($randomAppend);
		$this->assertEquals($randomSet.$randomAppend, $textReource->get());
	}

	/**
	 * Test prepending content to a text file part
	 */
	public function testTextResourcePrependPart()
	{
		$randomSet = md5(rand());
		$randomPrepend = md5(rand());
		$textReource = new TextResource();
		$textReource->setPart($randomSet)->prependPart($randomPrepend);
		$this->assertEquals($randomPrepend.$randomSet, $textReource->getPart());
	}


	/**
	 * Test appending content to a text file
	 */
	public function testTextResourcePrepend()
	{
		$randomSet = md5(rand());
		$randomPrepend = md5(rand());
		$textReource = new TextResource();
		$textReource->set($randomSet)->prepend($randomPrepend);
		$this->assertEquals($randomPrepend.$randomSet, $textReource->get());
	}

	/**
	 * Test an invalid path identifier
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447364401
	 */
	public function testTextResourceInvalidPathIdentifier()
	{
		$textReource = new TextResource();
		$textReource->getPart('-');
	}

	/**
	 * Test appending to the content of a text file with unallowed subparts
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447365624
	 */
	public function testTextResourceAppendSubparts()
	{
		$textReource = new TextResource();
		$textReource->appendPart(md5(rand()), 'a/b/c');
	}

	/**
	 * Test prepending to the content of a text file with unallowed subparts
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447365624
	 */
	public function testTextResourcePrependSubparts()
	{
		$textReource = new TextResource();
		$textReource->prependPart(md5(rand()), 'a/b/c');
	}

	/**
	 * Test getting the MIME type of a text file with unallowed subparts
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447365624
	 */
	public function testTextResourceMimeTypeSubparts()
	{
		$textReource = new TextResource();
		$textReource->getMimeTypePart('a/b/c');
	}

	/**
	 * Test dumping the contents of a text file
	 */
	public function testTextResourceDump()
	{
		$randomSet = md5(rand());
		$writer = new Writer();
		$textReource = new TextResource();
		$textReource->setPart($randomSet)->dump($writer);
		$this->assertEquals($randomSet, $writer->getData());
	}

	/**
	 * Test the in-memory universal reader / writer
	 */
	public function testTextResourceReaderWriter()
	{
		$randomAppend = md5(rand());
		$readerWriter = new ReaderWriter($this->_text);
		$textReource = new TextResource($readerWriter);
		$textReource->appendPart($randomAppend)->dump($readerWriter);
		$this->assertEquals($this->_text.$randomAppend, $readerWriter->getData());
	}
}