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

use Apparat\Resource\Framework\File\YamlFile;
use Apparat\Resource\Framework\Io\InMemory\Reader;
use Apparat\Resource\Framework\Part\YamlPart;

\date_default_timezone_set('UTC');

/**
 * YAML file tests
 *
 * @package ApparatTest
 */
class YamlTest extends TestBase
{
    /**
     * Example YAML data
     *
     * @var string
     */
    protected $_yaml = null;

    /**
     * Example YAML file
     *
     * @var string
     */
    const YAML_FILE = __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'invoice.yaml';

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_yaml = file_get_contents(self::YAML_FILE);
    }

    /**
     * Test the YAML file constructor
     */
    public function testYamlFile()
    {
        $yamlFile = new YamlFile();
        $this->assertInstanceOf(YamlFile::class, $yamlFile);
        $this->assertEquals(YamlPart::MIME_TYPE, $yamlFile->getMimeTypePart());
    }

    /**
     * Test the YAML file constructor with reader
     */
    public function testYamlFileReader()
    {
        $yamlFile = new YamlFile(new Reader($this->_yaml));
        $this->assertEquals($this->_yaml, $yamlFile->getPart());
    }

    /**
     * Test getting the data of a YAML file with unallowed subparts
     *
     * @expectedException \Apparat\Resource\Model\Part\InvalidArgumentException
     * @expectedExceptionCode 1447365624
     */
    public function testYamlFileHtmlSubparts()
    {
        $yamlFile = new YamlFile(new Reader($this->_yaml));
        $yamlFile->getDataPart('a/b/c');
    }

    /**
     * Test getting the data of a Yaml file
     */
    public function testYamlFileGetData()
    {
        $expectedData = include __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'invoice.php';
        $yamlFile = new YamlFile(new Reader($this->_yaml));
        $this->assertArrayEquals($expectedData, $yamlFile->getDataPart());
    }

    /**
     * Test getting the data of a Yaml file
     */
    public function testYamlFileSetData()
    {
        // Prepare modified expected data
        $expectedData = include __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'invoice.php';
        $expectedData['date'] = time();
        $expectedData['bill-to']['given'] = 'John';
        $expectedData['bill-to']['family'] = 'Doe';
        $expectedData['product'][] = [
            'sku' => 'ABC123',
            'quantity' => 1,
            'description' => 'Dummy',
            'price' => 123
        ];
        unset($expectedData['comments']);
        $yamlFile = new YamlFile(new Reader($this->_yaml));
        $yamlFile->setDataPart($expectedData);
        $this->assertArrayEquals($expectedData, $yamlFile->getDataPart());
    }
}