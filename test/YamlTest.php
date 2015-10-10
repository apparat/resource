<?php

/**
 * Apparat
 *
 * @category    Apparat
 * @package     Apparat_Resource
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

use Apparat\Resource\File\Yaml;

/**
 * Tests for YAML file resource
 *
 * @package ApparatTest
 */
class YamlTest extends TestBase
{
    /**
     * Example YAML data
     *
     * @var array
     */
    protected $_yaml = null;
    /**
     * Example YAML file
     *
     * @var string
     */
    const YAML_FILE = __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'invoice.yaml';
    /**
     * Example YAML file as PHP array
     *
     * @var string
     */
    const YAML_PHP_FILE = __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'invoice.php';

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_yaml = require self::YAML_PHP_FILE;
    }

    /**
     * Test constructor and content
     */
    public function testContent()
    {
        $file = new Yaml(self::YAML_FILE);
        $this->assertInstanceOf('Apparat\Resource\File\Yaml', $file);
        $this->assertEquals(1, count($file));

        /** @var \Apparat\Resource\File\Part\Body\Yaml $yamlPart */
        $yamlPart = $file->getPart(\Apparat\Resource\File\PartInterface::DEFAULT_NAME);
        $this->assertInstanceOf('Apparat\Resource\File\Part\Body\Yaml', $yamlPart);
        $this->assertArrayEquals($this->_yaml, $yamlPart->getData());
    }

    /**
     * Test content modifications via prepend()
     */
    public function testContentModificationViaPrepend() {
        $file = new Yaml(self::YAML_FILE);
        $now = time();
        $file->prepend("now: $now");
        $yaml = $this->_yaml;
        $yaml['now'] = $now;

        /** @var \Apparat\Resource\File\Part\Body\Yaml $yamlPart */
        $yamlPart = $file->getPart(\Apparat\Resource\File\PartInterface::DEFAULT_NAME);
        $this->assertArrayEquals($yaml, $yamlPart->getData());
    }

    /**
     * Test content modifications via append()
     */
    public function testContentModificationViaAppend() {
        $file = new Yaml(self::YAML_FILE);
        $now = time();
        $file->append("now: $now");
        $yaml = $this->_yaml;
        $yaml['now'] = $now;

        /** @var \Apparat\Resource\File\Part\Body\Yaml $yamlPart */
//        $yamlPart = $file->getPart(\Apparat\Resource\File\PartInterface::DEFAULT_NAME);
//        $this->assertArrayEquals($yaml, $yamlPart->getData());
    }
}
