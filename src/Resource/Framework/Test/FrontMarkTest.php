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

use Apparat\Resource\Domain\Model\Hydrator\HydratorInterface;
use Apparat\Resource\Domain\Model\Part\InvalidArgumentException;
use Apparat\Resource\Domain\Model\Part\OutOfBoundsException;
use Apparat\Resource\Domain\Model\Part\PartChoice;
use Apparat\Resource\Domain\Model\Part\PartSequence;
use Apparat\Resource\Domain\Model\Resource\AbstractResource;
use Apparat\Resource\Framework\Io\InMemory\Reader;
use Apparat\Resource\Framework\Model\Hydrator\CommonMarkHydrator;
use Apparat\Resource\Framework\Model\Hydrator\FrontMarkHydrator;
use Apparat\Resource\Framework\Model\Hydrator\FrontMatterHydrator;
use Apparat\Resource\Framework\Model\Hydrator\JsonHydrator;
use Apparat\Resource\Framework\Model\Hydrator\YamlHydrator;
use Apparat\Resource\Framework\Model\Resource\FrontMarkResource;
use Symfony\Component\Yaml\Yaml;


/**
 * Mocked part sequence
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 */
class PartSequenceMock extends PartSequence
{
    /**
     * Invalidate the CommonMark part
     */
    public function invalidateCommonMarkPart()
    {
        $this->_occurrences[0][HydratorInterface::STANDARD] = null;
    }
}

/**
 * Mocked FrontMark hydrator
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
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
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 */
class FrontMarkResourceMock extends AbstractResource
{
    /**
     * FrontMark file constructor
     *
     * @param Reader $reader Reader instance
     */
    public function __construct(Reader $reader = null)
    {
        parent::__construct(
            $reader, array(
            [
                FrontMatterHydrator::FRONTMATTER => [
                    [
                        JsonHydrator::JSON => JsonHydrator::class,
                        YamlHydrator::YAML => YamlHydrator::class
                    ],
                    FrontMatterHydrator::class
                ],
                HydratorInterface::STANDARD => CommonMarkHydrator::class,
            ],
            FrontMarkHydratorMock::class
        )
        );
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
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 */
class FrontMarkTest extends AbstractTest
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
    const YAML_FILE = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'invoice.yaml';
    /**
     * Example JSON file
     *
     * @var string
     */
    const JSON_FILE = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'invoice.json';
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
     * Example front matter YAML file
     *
     * @var string
     */
    const YAML_FRONTMATTER_FILE = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'frontmatter.yaml';
    /**
     * Example front matter JSON file
     *
     * @var string
     */
    const JSON_FRONTMATTER_FILE = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'frontmatter.json';

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
    public function testFrontMarkResource()
    {
        $frontMarkResource = new FrontMarkResource();
        $this->assertInstanceOf(FrontMarkResource::class, $frontMarkResource);
        $this->assertEquals(null, $frontMarkResource->getMimeTypePart());
    }

    /**
     * Test YAML part with too few part identifiers
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1448051332
     */
    public function testYamlTooFewPartIdentifiers()
    {
        $frontMarkResource = new FrontMarkResource(new Reader($this->_yamlFrontMark));
        $frontMarkResource->getPart('/0');
    }

    /**
     * Test YAML part with invalid occurrence index
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1448051596
     */
    public function testYamlInvalidOccurrenceIndex()
    {
        $frontMarkResource = new FrontMarkResource(new Reader($this->_yamlFrontMark));
        $frontMarkResource->getPart('/abc/123');
    }

    /**
     * Test YAML part with occurrence index out of bounds
     *
     * @expectedException OutOfBoundsException
     * @expectedExceptionCode 1448052094
     */
    public function testYamlOccurrenceIndexOutOfBounds()
    {
        $frontMarkResource = new FrontMarkResource(new Reader($this->_yamlFrontMark));
        $frontMarkResource->getPart('/123/abc');
    }

    /**
     * Test YAML part with occurrence index out of bounds
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1447364401
     */
    public function testYamlInvalidSubpartIdentifier()
    {
        $frontMarkResource = new FrontMarkResource(new Reader($this->_yamlFrontMark));
        $frontMarkResource->getPart('/123/~');
    }

    /**
     * Test YAML part with unknown identifier
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1447876475
     */
    public function testYamlUnknownSubpartIdentifier()
    {
        $frontMarkResource = new FrontMarkResource(new Reader($this->_yamlFrontMark));
        $frontMarkResource->getPart('/0/abc');
    }

    /**
     * Test YAML with empty / invalid part
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1448053518
     */
    public function testYamlInvalidPartInstance()
    {
        $frontMarkResource = new FrontMarkResourceMock(new Reader($this->_yamlFrontMark));
        $frontMarkResource->invalidateCommonMarkPart();
        $frontMarkResource->getPart('/0/'.HydratorInterface::STANDARD);
    }

    /**
     * Test YAML getting the front matter part
     */
    public function testYamlGetFrontmatterPart()
    {
        $expectedData = Yaml::parse(file_get_contents(self::YAML_FRONTMATTER_FILE));
        $frontMarkResource = new FrontMarkResource(new Reader($this->_yamlFrontMark));
        $actualData = Yaml::parse($frontMarkResource->getPart('/0/'.FrontMatterHydrator::FRONTMATTER));
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
        $frontMarkResource = new FrontMarkResource(new Reader($this->_yamlFrontMark));
        $frontMarkResource->setPart($yaml, '/0/'.FrontMatterHydrator::FRONTMATTER);
        $actualData = Yaml::parse(
            $frontMarkResource->getPart('/0/'.FrontMatterHydrator::FRONTMATTER.'/0/'.YamlHydrator::YAML)
        );
        $this->assertArrayEquals($expectedData, $actualData);
    }

    /**
     * Test JSON FrontMark file
     *
     */
    public function testJsonFrontMarkResource()
    {
        $frontMarkResource = new FrontMarkResource(new Reader($this->_jsonFrontMark));
        $this->assertStringEqualsFile(
            self::JSON_FRONTMATTER_FILE,
            $frontMarkResource->getPart('/0/'.FrontMatterHydrator::FRONTMATTER.'/0/'.JsonHydrator::JSON)
        );
    }

    /**
     * Test JSON file
     *
     */
    public function testJsonResource()
    {
        $frontMarkResource = new FrontMarkResource(new Reader(file_get_contents(self::JSON_FRONTMATTER_FILE)));
        $this->assertStringEqualsFile(
            self::JSON_FRONTMATTER_FILE,
            $frontMarkResource->getPart('/0/'.FrontMatterHydrator::FRONTMATTER.'/0/'.JsonHydrator::JSON)
        );
    }

    /**
     * Test JSON FrontMark frontmatter wildcard
     *
     */
    public function testJsonFrontMarkFrontmatterWildcard()
    {
        $frontMarkResource = new FrontMarkResource(new Reader($this->_jsonFrontMark));
        $this->assertStringEqualsFile(
            self::JSON_FRONTMATTER_FILE,
            $frontMarkResource->getPart('/0/'.FrontMatterHydrator::FRONTMATTER.'/0/'.PartChoice::WILDCARD)
        );
    }

    /**
     * Test JSON FrontMark get frontmatter data
     *
     */
    public function testJsonFrontMarkFrontmatterGetData()
    {
        $frontMarkResource = new FrontMarkResource(new Reader($this->_jsonFrontMark));
        $this->assertArrayEquals(
            json_decode(file_get_contents(self::JSON_FRONTMATTER_FILE), true),
            $frontMarkResource->getData()
        );
    }

    /**
     * Test JSON FrontMark set frontmatter data
     *
     */
    public function testJsonFrontMarkFrontmatterSetData()
    {
        $expectedJson = json_decode(file_get_contents(self::JSON_FILE), true);
        $frontMarkResource = new FrontMarkResource(new Reader($this->_jsonFrontMark));
        $frontMarkResource->setData($expectedJson);
        $this->assertArrayEquals(
            $expectedJson,
            $frontMarkResource->getDataPart('/0/'.FrontMatterHydrator::FRONTMATTER.'/0/'.PartChoice::WILDCARD)
        );
    }

    /**
     * Test YAML set / get CommonMark
     */
    public function testYamlSetGetCommonMark()
    {
        $randomSet = md5(rand());
        $frontMarkResource = new FrontMarkResource(new Reader($this->_yamlFrontMark));
        $frontMarkResource->set($randomSet);
        $this->assertEquals($randomSet, $frontMarkResource->getPart('/0/'.HydratorInterface::STANDARD));
        $this->assertEquals($randomSet, $frontMarkResource->get());
    }

    /**
     * Test YAML append CommonMark
     */
    public function testYamlAppendCommonMark()
    {
        $randomAppend = md5(rand());
        $frontMarkResource = new FrontMarkResource(new Reader($this->_yamlFrontMark));
        $frontMarkResource->append($randomAppend);
        $this->assertEquals($this->_commonMark.$randomAppend, $frontMarkResource->get());
    }

    /**
     * Test YAML prepend CommonMark
     */
    public function testYamlPrependCommonMark()
    {
        $randomPrepend = md5(rand());
        $frontMarkResource = new FrontMarkResource(new Reader($this->_yamlFrontMark));
        $frontMarkResource->prepend($randomPrepend);
        $this->assertEquals($randomPrepend.$this->_commonMark, $frontMarkResource->get());
    }

    /**
     * Test YAML get CommonMark HTL
     */
    public function testYamlGetCommonMarkHtml()
    {
        $expectedHtml = $this->_normalizeHtml(
            file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'commonmark.html')
        );
        $frontMarkResource = new FrontMarkResource(new Reader($this->_yamlFrontMark));
        $this->assertEquals($expectedHtml, $this->_normalizeHtml($frontMarkResource->getHtml()));
    }
}
