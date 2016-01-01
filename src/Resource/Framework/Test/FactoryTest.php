<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation Fixture (the "Software"), to deal in
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

use Apparat\Resource\Framework\Api\Resource;
use Apparat\Resource\Framework\Io\InMemory\Writer as InMemoryWriter;
use Apparat\Resource\Framework\Io\File\Writer as FileWriter;
use Apparat\Resource\Framework\Api\InvalidArgumentException;
use Apparat\Resource\Framework\Model\Resource\CommonMarkResource;
use Apparat\Resource\Framework\Model\Resource\FrontMarkResource;
use Apparat\Resource\Framework\Model\Resource\JsonResource;
use Apparat\Resource\Framework\Model\Resource\TextResource;
use Apparat\Resource\Framework\Model\Resource\YamlResource;

/**
 * Resource factory tests
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 */
class FactoryTest extends AbstractTest
{
	/**
	 * Example text data
	 *
	 * @var string
	 */
	protected $_text = null;

	/**
	 * Example JSON data
	 *
	 * @var string
	 */
	protected $_json = null;

	/**
	 * Example YAML data
	 *
	 * @var string
	 */
	protected $_yaml = null;

	/**
	 * Example CommonMark data
	 *
	 * @var string
	 */
	protected $_commonMark = null;

	/**
	 * Example FrontMark data with YAML front matter
	 *
	 * @var string
	 */
	protected $_yamlFrontMark = null;

	/**
	 * Example FrontMark file with JSON front matter
	 *
	 * @var string
	 */
	protected $_jsonFrontMark = null;

	/**
	 * Example text file
	 *
	 * @var string
	 */
	const TXT_FILE = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'cc0.txt';

	/**
	 * Example JSON file
	 *
	 * @var string
	 */
	const JSON_FILE = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'invoice.json';

	/**
	 * Example YAML file
	 *
	 * @var string
	 */
	const YAML_FILE = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'invoice.yaml';

	/**
	 * Example CommonMark file
	 *
	 * @var string
	 */
	const COMMONMARK_FILE = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'commonmark.md';

	/**
	 * Example FrontMark file with YAML front matter
	 *
	 * @var string
	 */
	const YAML_FRONTMARK_FILE = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'yaml-frontmark.md';
	/**
	 * Example FrontMark file with JSON front matter
	 *
	 * @var string
	 */
	const JSON_FRONTMARK_FILE = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'json-frontmark.md';

	/**
	 * Sets up the fixture
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->_text = file_get_contents(self::TXT_FILE);
		$this->_json = file_get_contents(self::JSON_FILE);
		$this->_yaml = file_get_contents(self::YAML_FILE);
		$this->_commonMark = file_get_contents(self::COMMONMARK_FILE);
		$this->_yamlFrontMark = file_get_contents(self::YAML_FRONTMARK_FILE);
		$this->_jsonFrontMark = file_get_contents(self::JSON_FRONTMARK_FILE);
	}

	/**
	 * Test the text resource factory with a string literal input (in-memory reader)
	 */
	public function testTextFactoryStringReader()
	{
		$randString = md5(rand());
		$textResource = Resource::text($randString);
		$this->assertInstanceOf(TextResource::class, $textResource);
		$this->assertEquals($randString, $textResource->get());
		$this->assertEquals($randString, strval($textResource));
	}

	/**
	 * Test the text resource factory with a file path input (file reader)
	 */
	public function testTextFactoryFileReader()
	{
		$textResource = Resource::text('file://'.self::TXT_FILE);
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
		Resource::text('foo://'.self::TXT_FILE);
	}

	/**
	 * Test the text resource factory with a string output (in-memory writer)
	 */
	public function testTextFactoryStringWriter()
	{
		$textResource = Resource::text($this->_text);
		/** @var InMemoryWriter $writer */
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
		$textResource = Resource::text($this->_text);
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
		Resource::text($this->_text)->to('foo://'.$this->_createTemporaryFile(true));
	}

	/**
	 * Test the JSON resource factory with a file path input (file reader)
	 */
	public function testJsonFactoryFileReader()
	{
		$jsonResource = Resource::json('file://'.self::JSON_FILE);
		$this->assertInstanceOf(JsonResource::class, $jsonResource);
		$this->assertEquals($this->_json, $jsonResource->get());
	}

	/**
	 * Test the YAML resource factory with a file path input (file reader)
	 */
	public function testYamlFactoryFileReader()
	{
		$yamlResource = Resource::yaml('file://'.self::YAML_FILE);
		$this->assertInstanceOf(YamlResource::class, $yamlResource);
		$this->assertEquals($this->_yaml, $yamlResource->get());
	}

	/**
	 * Test the CommonMark resource factory with a file path input (file reader)
	 */
	public function testCommonMarkFactoryFileReader()
	{
		$commonMarkResource = Resource::commonMark('file://'.self::COMMONMARK_FILE);
		$this->assertInstanceOf(CommonMarkResource::class, $commonMarkResource);
		$this->assertEquals($this->_commonMark, $commonMarkResource->get());
	}

	/**
	 * Test the YAML FrontMark resource factory with a file path input (file reader)
	 */
	public function testYamlFrontMarkFactoryFileReader()
	{
		$yamlFrontMarkResource = Resource::frontMark('file://'.self::YAML_FRONTMARK_FILE);
		$this->assertInstanceOf(FrontMarkResource::class, $yamlFrontMarkResource);
		$this->assertEquals($this->_yamlFrontMark, strval($yamlFrontMarkResource));
	}

	/**
	 * Test the JSON FrontMark resource factory with a file path input (file reader)
	 */
	public function testJsonFrontMarkFactoryFileReader()
	{
		$jsonFrontMarkResource = Resource::frontMark('file://'.self::JSON_FRONTMARK_FILE);
		$this->assertInstanceOf(FrontMarkResource::class, $jsonFrontMarkResource);
		$this->assertEquals($this->_jsonFrontMark, strval($jsonFrontMarkResource));
	}
}