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
use Apparat\Resource\Infrastructure\Model\Part\JsonPart;
use Apparat\Resource\Infrastructure\Model\Resource\JsonResource;

/**
 * JSON file tests
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Tests
 */
class JsonTest extends AbstractDataTest
{
    /**
     * Example JSON data
     *
     * @var string
     */
    protected $json = null;

    /**
     * Example JSON file
     *
     * @var string
     */
    const JSON_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'invoice.json';

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
        $this->json = file_get_contents(self::JSON_FILE);
    }

    /**
     * Test the JSON file constructor
     */
    public function testJsonResource()
    {
        $jsonResource = Kernel::create(JsonResource::class, [null]);
        $this->assertInstanceOf(JsonResource::class, $jsonResource);
        $this->assertEquals(JsonPart::MEDIA_TYPE, $jsonResource->getMediaTypePart());
    }

    /**
     * Test the JSON file constructor with reader
     */
    public function testJsonResourceReader()
    {
        $jsonResource = Kernel::create(JsonResource::class, [Kernel::create(Reader::class, [$this->json])]);
        $this->assertEquals($this->json, $jsonResource->getPart());
    }

    /**
     * Test getting the data of a JSON file with unallowed subparts
     *
     * @expectedException \Apparat\Resource\Domain\Model\Part\InvalidArgumentException
     * @expectedExceptionCode 1447365624
     */
    public function testJsonResourceHtmlSubparts()
    {
        $jsonResource = Kernel::create(JsonResource::class, [Kernel::create(Reader::class, [$this->json])]);
        $jsonResource->getDataPart('a/b/c');
    }

    /**
     * Test getting the data of a Json file
     */
    public function testJsonResourceGetData()
    {
        $expectedData = include __DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'invoice.php';
        $jsonResource = Kernel::create(JsonResource::class, [Kernel::create(Reader::class, [$this->json])]);
        $this->assertArrayEquals($expectedData, $jsonResource->getDataPart());
    }

    /**
     * Test getting the data of a Json file
     */
    public function testJsonResourceSetData()
    {
        $expectedData = $this->getExpectedInvoiceData();
        $jsonResource = Kernel::create(JsonResource::class, [Kernel::create(Reader::class, [$this->json])]);
        $jsonResource->setDataPart($expectedData);
        $this->assertArrayEquals($expectedData, $jsonResource->getDataPart());
    }
}
