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
use Apparat\Resource\Framework\Io\InMemory\Reader;

/**
 * FrontMark file tests
 *
 * @package ApparatTest
 */
class FrontMarkTest extends TestBase
{
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
     * Sets up the fixture
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_yamlFrontMark = file_get_contents(self::YAML_FRONTMARK_FILE);
        $this->_jsonFrontMark = file_get_contents(self::JSON_FRONTMARK_FILE);
    }

    /**
     * Test a FrontMark file
     */
    public function testYamlFrontMarkFile() {
        $frontMarkFile = new FrontMarkFile(new Reader($this->_yamlFrontMark));
        $this->assertInstanceOf(FrontMarkFile::class, $frontMarkFile);
        $frontMarkFile->getPart();
//        print_r($frontMarkFile->getPart());
    }
}