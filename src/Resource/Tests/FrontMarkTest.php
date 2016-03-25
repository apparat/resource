<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Tests
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
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

namespace Apparat\Resource\Tests;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Kernel\Tests\AbstractTest;
use Apparat\Resource\Domain\Model\Hydrator\HydratorInterface;
use Apparat\Resource\Domain\Model\Part\InvalidArgumentException;
use Apparat\Resource\Domain\Model\Part\OutOfBoundsException;
use Apparat\Resource\Domain\Model\Part\PartChoice;
use Apparat\Resource\Domain\Model\Part\PartSequence;
use Apparat\Resource\Infrastructure\Io\InMemory\Reader;
use Apparat\Resource\Infrastructure\Model\Hydrator\FrontMatterHydrator;
use Apparat\Resource\Infrastructure\Model\Hydrator\JsonHydrator;
use Apparat\Resource\Infrastructure\Model\Hydrator\YamlHydrator;
use Apparat\Resource\Infrastructure\Model\Resource\FrontMarkResource;
use Symfony\Component\Yaml\Yaml;

/**
 * FrontMark file tests
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Tests
 */
class FrontMarkTest extends AbstractTest
{
    /**
     * Example CommonMark data
     *
     * @var string
     */
    protected $commonMark = null;
    /**
     * Example FrontMark data with YAML front matter
     *
     * @var string
     */
    protected $yamlFrontMark = null;
    /**
     * Example FrontMark file with JSON front matter
     *
     * @var string
     */
    protected $jsonFrontMark = null;
    /**
     * Example YAML file
     *
     * @var string
     */
    const YAML_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'invoice.yaml';
    /**
     * Example JSON file
     *
     * @var string
     */
    const JSON_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'invoice.json';
    /**
     * Example CommonMark file
     *
     * @var string
     */
    const COMMONMARK_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'commonmark.md';
    /**
     * Example FrontMark file with YAML front matter
     *
     * @var string
     */
    const YAML_FRONTMARK_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'yaml-frontmark.md';
    /**
     * Example FrontMark file with JSON front matter
     *
     * @var string
     */
    const JSON_FRONTMARK_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'json-frontmark.md';
    /**
     * Example front matter YAML file
     *
     * @var string
     */
    const YAML_FRONTMATTER_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'frontmatter.yaml';
    /**
     * Example front matter JSON file
     *
     * @var string
     */
    const JSON_FRONTMATTER_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'frontmatter.json';

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        parent::setUp();
        $this->commonMark = file_get_contents(self::COMMONMARK_FILE);
        $this->yamlFrontMark = file_get_contents(self::YAML_FRONTMARK_FILE);
        $this->jsonFrontMark = file_get_contents(self::JSON_FRONTMARK_FILE);
    }

    /**
     * Test a FrontMark file
     */
    public function testFrontMarkResource()
    {
        $frontMarkResource = Kernel::create(FrontMarkResource::class, [null]);
        $this->assertInstanceOf(FrontMarkResource::class, $frontMarkResource);
        $this->assertEquals(null, $frontMarkResource->getMediaTypePart());

    }

    /**
     * Test YAML part with too few part identifiers
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1448051332
     */
    public function testYamlTooFewPartIdentifiers()
    {
        $frontMarkResource = Kernel::create(
            FrontMarkResource::class,
            [Kernel::create(Reader::class, [$this->yamlFrontMark])]
        );
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
        $frontMarkResource = Kernel::create(
            FrontMarkResource::class,
            [Kernel::create(Reader::class, [$this->yamlFrontMark])]
        );
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
        $frontMarkResource = Kernel::create(
            FrontMarkResource::class,
            [Kernel::create(Reader::class, [$this->yamlFrontMark])]
        );
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
        $frontMarkResource = Kernel::create(
            FrontMarkResource::class,
            [Kernel::create(Reader::class, [$this->yamlFrontMark])]
        );
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
        $frontMarkResource = Kernel::create(
            FrontMarkResource::class,
            [Kernel::create(Reader::class, [$this->yamlFrontMark])]
        );
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
        $frontMarkResource = Kernel::create(
            FrontMarkResourceMock::class,
            [Kernel::create(Reader::class, [$this->yamlFrontMark])]
        );
        $frontMarkResource->invalidateCommonMarkPart();
        $frontMarkResource->getPart('/0/' . HydratorInterface::STANDARD);
    }

    /**
     * Test sequence aggregate
     */
    public function testSequenceAggregate()
    {
        $frontMarkResource = Kernel::create(
            FrontMarkResourceMock::class,
            [Kernel::create(Reader::class, [$this->yamlFrontMark])]
        );

        /** @var PartSequence $sequence */
        $sequence = $frontMarkResource->getSequence();
        $this->assertInstanceOf(PartSequence::class, $sequence);
        $this->assertEquals(1, count($sequence));
        foreach ($sequence as $occurrenceIndex => $occurrence) {
            $this->assertEquals(0, $occurrenceIndex);
            $this->assertTrue(is_array($occurrence));
        }
    }

    /**
     * Test YAML getting the front matter part
     */
    public function testYamlGetFrontmatterPart()
    {
        $expectedData = Yaml::parse(file_get_contents(self::YAML_FRONTMATTER_FILE));
        $frontMarkResource = Kernel::create(
            FrontMarkResource::class,
            [Kernel::create(Reader::class, [$this->yamlFrontMark])]
        );
        $actualData = Yaml::parse($frontMarkResource->getPart('/0/' . FrontMatterHydrator::FRONTMATTER));
        $this->assertArrayEquals($expectedData, $actualData);
    }

    /**
     * Test YAML setting the front matter part
     */
    public function testYamlSetFrontmatterPart()
    {
        $yaml = file_get_contents(self::YAML_FILE);
        $expectedData = Yaml::parse($yaml);
        $frontMarkResource = Kernel::create(
            FrontMarkResource::class,
            [Kernel::create(Reader::class, [$this->yamlFrontMark])]
        );
        $frontMarkResource->setPart($yaml, '/0/' . FrontMatterHydrator::FRONTMATTER);
        $actualData = Yaml::parse(
            $frontMarkResource->getPart('/0/' . FrontMatterHydrator::FRONTMATTER . '/0/' . YamlHydrator::YAML)
        );
        $this->assertArrayEquals($expectedData, $actualData);
    }

    /**
     * Test JSON FrontMark file
     */
    public function testJsonFrontMarkResource()
    {
        $frontMarkResource = Kernel::create(
            FrontMarkResource::class,
            [Kernel::create(Reader::class, [$this->jsonFrontMark])]
        );
        $this->assertStringEqualsFile(
            self::JSON_FRONTMATTER_FILE,
            $frontMarkResource->getPart('/0/' . FrontMatterHydrator::FRONTMATTER . '/0/' . JsonHydrator::JSON)
        );
    }

    /**
     * Test JSON file
     */
    public function testJsonResource()
    {
        $frontMarkResource = Kernel::create(
            FrontMarkResource::class,
            [
                Kernel::create(
                    Reader::class,
                    [file_get_contents(self::JSON_FRONTMATTER_FILE)]
                )
            ]
        );
        $this->assertStringEqualsFile(
            self::JSON_FRONTMATTER_FILE,
            $frontMarkResource->getPart('/0/' . FrontMatterHydrator::FRONTMATTER . '/0/' . JsonHydrator::JSON)
        );
    }

    /**
     * Test JSON FrontMark frontmatter wildcard
     */
    public function testJsonFrontMarkFrontmatterWildcard()
    {
        $frontMarkResource = Kernel::create(
            FrontMarkResource::class,
            [Kernel::create(Reader::class, [$this->jsonFrontMark])]
        );
        $this->assertStringEqualsFile(
            self::JSON_FRONTMATTER_FILE,
            $frontMarkResource->getPart('/0/' . FrontMatterHydrator::FRONTMATTER . '/0/' . PartChoice::WILDCARD)
        );
    }

    /**
     * Test JSON FrontMark get frontmatter data
     */
    public function testJsonFrontMarkFrontmatterGetData()
    {
        $frontMarkResource = Kernel::create(
            FrontMarkResource::class,
            [Kernel::create(Reader::class, [$this->jsonFrontMark])]
        );
        $this->assertArrayEquals(
            json_decode(file_get_contents(self::JSON_FRONTMATTER_FILE), true),
            $frontMarkResource->getData()
        );
    }

    /**
     * Test JSON FrontMark set frontmatter data
     */
    public function testJsonFrontMarkFrontmatterSetData()
    {
        $expectedJson = json_decode(file_get_contents(self::JSON_FILE), true);
        $frontMarkResource = Kernel::create(
            FrontMarkResource::class,
            [Kernel::create(Reader::class, [$this->jsonFrontMark])]
        );
        $frontMarkResource->setData($expectedJson);
        $this->assertArrayEquals(
            $expectedJson,
            $frontMarkResource->getDataPart('/0/' . FrontMatterHydrator::FRONTMATTER . '/0/' . PartChoice::WILDCARD)
        );
    }

    /**
     * Test YAML set / get CommonMark
     */
    public function testYamlSetGetCommonMark()
    {
        $randomSet = md5(rand());
        $frontMarkResource = Kernel::create(
            FrontMarkResource::class,
            [Kernel::create(Reader::class, [$this->yamlFrontMark])]
        );
        $frontMarkResource->set($randomSet);
        $this->assertEquals($randomSet, $frontMarkResource->getPart('/0/' . HydratorInterface::STANDARD));
        $this->assertEquals($randomSet, $frontMarkResource->get());
    }

    /**
     * Test YAML append CommonMark
     */
    public function testYamlAppendCommonMark()
    {
        $randomAppend = md5(rand());
        $frontMarkResource = Kernel::create(
            FrontMarkResource::class,
            [Kernel::create(Reader::class, [$this->yamlFrontMark])]
        );
        $frontMarkResource->append($randomAppend);
        $this->assertEquals($this->commonMark . $randomAppend, $frontMarkResource->get());
    }

    /**
     * Test YAML prepend CommonMark
     */
    public function testYamlPrependCommonMark()
    {
        $randomPrepend = md5(rand());
        $frontMarkResource = Kernel::create(
            FrontMarkResource::class,
            [Kernel::create(Reader::class, [$this->yamlFrontMark])]
        );
        $frontMarkResource->prepend($randomPrepend);
        $this->assertEquals($randomPrepend . $this->commonMark, $frontMarkResource->get());
    }

    /**
     * Test YAML get CommonMark HTL
     */
    public function testYamlGetCommonMarkHtml()
    {
        $expectedHtml = $this->normalizeHtml(
            file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'commonmark.html')
        );
        $frontMarkResource = Kernel::create(
            FrontMarkResource::class,
            [Kernel::create(Reader::class, [$this->yamlFrontMark])]
        );
        $this->assertEquals($expectedHtml, $this->normalizeHtml($frontMarkResource->getHtml()));
    }
}
