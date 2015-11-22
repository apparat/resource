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

namespace ApparatTest;

use Apparat\Resource\Framework\File\FrontMarkFile;
use Apparat\Resource\Framework\Hydrator\CommonMarkHydrator;
use Apparat\Resource\Framework\Hydrator\FrontMarkHydrator;
use Apparat\Resource\Framework\Hydrator\FrontMatterHydrator;
use Apparat\Resource\Framework\Hydrator\JsonHydrator;
use Apparat\Resource\Framework\Hydrator\YamlHydrator;
use Apparat\Resource\Framework\Io\InMemory\Reader;
use Apparat\Resource\Model\File\File;
use Apparat\Resource\Model\Hydrator\Hydrator;
use Apparat\Resource\Model\Part\InvalidArgumentException;
use Apparat\Resource\Model\Part\OutOfBoundsException;
use Apparat\Resource\Model\Part\PartSequence;
use Symfony\Component\Yaml\Yaml;


/**
 * Mocked part sequence
 *
 * @package ApparatTest
 */
class PartSequenceMock extends PartSequence
{
	/**
	 * Invalidate the CommonMark part
	 */
	public function invalidateCommonMarkPart()
	{
		$this->_occurrences[0][Hydrator::STANDARD] = null;
	}
}

/**
 * Mocked FrontMark hydrator
 *
 * @package ApparatTest
 */
class FrontMarkHydratorMock extends FrontMarkHydrator
{
	/**
	 * Part aggregate class name
	 *
	 * @var string
	 */
	protected $_aggregateClass = PartSequenceMock::class;
}

/**
 * Mocked FrontMark file
 *
 * @package ApparatTest
 */
class FrontMarkFileMock extends File
{
	/**
	 * FrontMark file constructor
	 *
	 * @param Reader $reader Reader instance
	 */
	public function __construct(Reader $reader = null)
	{
		parent::__construct($reader, array(
			[
				FrontMatterHydrator::FRONTMATTER => [
					[
						JsonHydrator::JSON => JsonHydrator::class,
						YamlHydrator::YAML => YamlHydrator::class
					],
					FrontMatterHydrator::class
				],
				Hydrator::STANDARD => CommonMarkHydrator::class,
			],
			FrontMarkHydratorMock::class
		));
	}

	/**
	 * Invalidate the CommonMark part
	 */
	public function invalidateCommonMarkPart()
	{
		/** @var PartSequenceMock $sequence */
		$sequence = $this->_part();
		$sequence->invalidateCommonMarkPart();
	}
}

/**
 * FrontMark file tests
 *
 * @package ApparatTest
 */
class FrontMarkTest extends TestBase
{
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
	 * Example YAML file
	 *
	 * @var string
	 */
	const YAML_FILE = __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'invoice.yaml';
	/**
	 * Example CommonMark file
	 *
	 * @var string
	 */
	const COMMONMARK_FILE = __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'commonmark.md';
	/**
	 * Example FrontMark file with YAML front matter
	 *
	 * @var string
	 */
	const YAML_FRONTMARK_FILE = __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'yaml-frontmark.md';
	/**
	 * Example FrontMark file with JSON front matter
	 *
	 * @var string
	 */
	const JSON_FRONTMARK_FILE = __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'json-frontmark.md';
	/**
	 * Example front matter YAML file
	 *
	 * @var string
	 */
	const YAML_FRONTMATTER_FILE = __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'frontmatter.yaml';
	/**
	 * Example front matter JSON file
	 *
	 * @var string
	 */
	const JSON_FRONTMATTER_FILE = __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'frontmatter.json';

	/**
	 * Sets up the fixture
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->_commonMark = file_get_contents(self::COMMONMARK_FILE);
		$this->_yamlFrontMark = file_get_contents(self::YAML_FRONTMARK_FILE);
		$this->_jsonFrontMark = file_get_contents(self::JSON_FRONTMARK_FILE);
	}

	/**
	 * Test a FrontMark file
	 */
	public function testFrontMarkFile()
	{
		$frontMarkFile = new FrontMarkFile();
		$this->assertInstanceOf(FrontMarkFile::class, $frontMarkFile);
		$this->assertEquals(null, $frontMarkFile->getMimeTypePart());
	}

	/**
	 * Test YAML part with too few part identifiers
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1448051332
	 */
	public function testYamlTooFewPartIdentifiers()
	{
		$frontMarkFile = new FrontMarkFile(new Reader($this->_yamlFrontMark));
		$frontMarkFile->getPart('/0');
	}

	/**
	 * Test YAML part with invalid occurrence index
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1448051596
	 */
	public function testYamlInvalidOccurrenceIndex()
	{
		$frontMarkFile = new FrontMarkFile(new Reader($this->_yamlFrontMark));
		$frontMarkFile->getPart('/abc/123');
	}

	/**
	 * Test YAML part with occurrence index out of bounds
	 *
	 * @expectedException OutOfBoundsException
	 * @expectedExceptionCode 1448052094
	 */
	public function testYamlOccurrenceIndexOutOfBounds()
	{
		$frontMarkFile = new FrontMarkFile(new Reader($this->_yamlFrontMark));
		$frontMarkFile->getPart('/123/abc');
	}

	/**
	 * Test YAML part with occurrence index out of bounds
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447364401
	 */
	public function testYamlInvalidSubpartIdentifier()
	{
		$frontMarkFile = new FrontMarkFile(new Reader($this->_yamlFrontMark));
		$frontMarkFile->getPart('/123/~');
	}

	/**
	 * Test YAML part with unknown identifier
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447876475
	 */
	public function testYamlUnknownSubpartIdentifier()
	{
		$frontMarkFile = new FrontMarkFile(new Reader($this->_yamlFrontMark));
		$frontMarkFile->getPart('/0/abc');
	}

	/**
	 * Test YAML with empty / invalid part
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1448053518
	 */
	public function testYamlInvalidPartInstance()
	{
		$frontMarkFile = new FrontMarkFileMock(new Reader($this->_yamlFrontMark));
		$frontMarkFile->invalidateCommonMarkPart();
		$frontMarkFile->getPart('/0/'.Hydrator::STANDARD);
	}

	/**
	 * Test YAML getting the front matter part
	 */
	public function testYamlGetFrontmatterPart()
	{
		$expectedData = Yaml::parse(file_get_contents(self::YAML_FRONTMATTER_FILE));
		$frontMarkFile = new FrontMarkFile(new Reader($this->_yamlFrontMark));
		$actualData = Yaml::parse($frontMarkFile->getPart('/0/'.FrontMatterHydrator::FRONTMATTER));
		$this->assertArrayEquals($expectedData, $actualData);
	}

	/**
	 * Test YAML setting the front matter part
	 *
	 */
	public function testYamlSetFrontmatterPart()
	{
		$yaml = file_get_contents(self::YAML_FILE);
		$expectedData = Yaml::parse($yaml);
		$frontMarkFile = new FrontMarkFile(new Reader($this->_yamlFrontMark));
		$frontMarkFile->setPart($yaml, '/0/'.FrontMatterHydrator::FRONTMATTER);
		$actualData = Yaml::parse($frontMarkFile->getPart('/0/'.FrontMatterHydrator::FRONTMATTER.'/0/'.YamlHydrator::YAML));
		$this->assertArrayEquals($expectedData, $actualData);
	}

	/**
	 * Test JSON FrontMark file
	 *
	 */
	public function testJsonFrontMarkFile()
	{
		$frontMarkFile = new FrontMarkFile(new Reader($this->_jsonFrontMark));
		$this->assertStringEqualsFile(self::JSON_FRONTMATTER_FILE,
			$frontMarkFile->getPart('/0/'.FrontMatterHydrator::FRONTMATTER.'/0/'.JsonHydrator::JSON));
	}

	/**
	 * Test JSON file
	 *
	 */
	public function testJsonFile()
	{
		$frontMarkFile = new FrontMarkFile(new Reader(file_get_contents(self::JSON_FRONTMATTER_FILE)));
		$this->assertStringEqualsFile(self::JSON_FRONTMATTER_FILE,
			$frontMarkFile->getPart('/0/'.FrontMatterHydrator::FRONTMATTER.'/0/'.JsonHydrator::JSON));
	}

	/**
	 * Test JSON file
	 *
	 */
	public function testJsonFileData()
	{
		// TODO: Route methods like FrontMarkFile->getDataPart() through aggregates
		// TODO: Test convenience methods like FrontMarkFile->getData()
//		$frontMarkFile = new FrontMarkFile(new Reader($this->_jsonFrontMark));
//		$this->assertStringEqualsFile(self::JSON_FRONTMATTER_FILE,
//			$frontMarkFile->getPart('/0/'.FrontMatterHydrator::FRONTMATTER.'/0/'.JsonHydrator::JSON));
//		print_r($frontMarkFile->getDataPart('/0/'.FrontMatterHydrator::FRONTMATTER.'/0/'.JsonHydrator::JSON));
	}
}