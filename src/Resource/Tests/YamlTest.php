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
use Apparat\Resource\Infrastructure\Io\InMemory\Reader;
use Apparat\Resource\Infrastructure\Model\Part\YamlPart;
use Apparat\Resource\Infrastructure\Model\Resource\YamlResource;

/**
 * YAML file tests
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Tests
 */
class YamlTest extends AbstractDataTest
{
    /**
     * Example YAML data
     *
     * @var string
     */
    protected $yaml = null;

    /**
     * Example YAML file
     *
     * @var string
     */
    const YAML_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'invoice.yaml';

    /**
     * Preparations before the first test is run
     */
    public static function setUpBeforeClass()
    {
        \date_default_timezone_set('UTC');
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        parent::setUp();
        $this->yaml = file_get_contents(self::YAML_FILE);
    }

    /**
     * Test the YAML file constructor
     */
    public function testYamlResource()
    {
        $yamlResource = Kernel::create(YamlResource::class, [null]);
        $this->assertInstanceOf(YamlResource::class, $yamlResource);
        $this->assertEquals(YamlPart::MIME_TYPE, $yamlResource->getMimeTypePart());
    }

    /**
     * Test the YAML file constructor with reader
     */
    public function testYamlResourceReader()
    {
        $yamlResource = Kernel::create(YamlResource::class, [Kernel::create(Reader::class, [$this->yaml])]);
        $this->assertEquals($this->yaml, $yamlResource->getPart());
    }

    /**
     * Test getting the data of a YAML file with unallowed subparts
     *
     * @expectedException \Apparat\Resource\Domain\Model\Part\InvalidArgumentException
     * @expectedExceptionCode 1447365624
     */
    public function testYamlResourceHtmlSubparts()
    {
        $yamlResource = Kernel::create(YamlResource::class, [Kernel::create(Reader::class, [$this->yaml])]);
        $yamlResource->getDataPart('a/b/c');
    }

    /**
     * Test getting the data of a Yaml file
     */
    public function testYamlResourceGetData()
    {
        $expectedData = include __DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'invoice.php';
        $yamlResource = Kernel::create(YamlResource::class, [Kernel::create(Reader::class, [$this->yaml])]);
        $this->assertArrayEquals($expectedData, $yamlResource->getDataPart());
        $this->assertArrayEquals($expectedData, $yamlResource->getData());
    }

    /**
     * Test getting the data part of a Yaml file
     */
    public function testYamlResourceSetDataPart()
    {
        $expectedData = $this->getExpectedInvoiceData();
        $yamlResource = Kernel::create(YamlResource::class, [Kernel::create(Reader::class, [$this->yaml])]);
        $yamlResource->setDataPart($expectedData);
        $this->assertArrayEquals($expectedData, $yamlResource->getDataPart());
    }

    /**
     * Test getting the data of a Yaml file
     */
    public function testYamlResourceSetData()
    {
        $expectedData = $this->getExpectedInvoiceData();
        $yamlResource = Kernel::create(YamlResource::class, [Kernel::create(Reader::class, [$this->yaml])]);
        $yamlResource->setData($expectedData);
        $this->assertArrayEquals($expectedData, $yamlResource->getData());
    }
}
