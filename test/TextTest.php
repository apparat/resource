<?php

/**
 * Apparat
 *
 * @category    Jkphl
 * @package     Jkphl_Apparat
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

use Apparat\Resource\File\Text;

/**
 * Tests for text file resource
 *
 * @package ApparatTest
 */
class TextTest extends TestBase
{
    /**
     * Example text data
     *
     * @var array
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
     * Test constructor and content
     */
    public function testContent()
    {
        $file = new Text(self::TXT_FILE);
        $this->assertInstanceOf('Apparat\Resource\File\Text', $file);
        $this->assertEquals(1, count($file));

        /** @var \Apparat\Resource\File\Part\Body\Text $textPart */
        $textPart = $file->getPart(\Apparat\Resource\File\PartInterface::DEFAULT_NAME);
        $this->assertInstanceOf('Apparat\Resource\File\Part\Body\Text', $textPart);
        $this->assertEquals($this->_text, strval($textPart));
    }

    /**
     * Test content modifications via prepend()
     */
    public function testContentModificationViaPrepend() {
        $file = new Text(self::TXT_FILE);
        $now = time();
        $file->prepend($now);
        $this->assertEquals($now.$this->_text, strval($file));
    }

    /**
     * Test content modifications via append()
     */
    public function testContentModificationViaAppend() {
        $file = new Text(self::TXT_FILE);
        $now = time();
        $file->append($now);
        $this->assertEquals($this->_text.$now, strval($file));
    }
}
