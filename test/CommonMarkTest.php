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

use Apparat\Resource\File\CommonMark;

/**
 * Tests for CommonMark file resource
 *
 * @package ApparatTest
 */
class CommonMarkTest extends TestBase
{
    /**
     * Example CommonMark file
     *
     * @var string
     */
    const COMMONMARK_FILE = __DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'commonmark.md';

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        parent::setUp();

//        $this->_yaml = require self::YAML_PHP_FILE;
    }

    /**
     * Test constructor and content without argument
     */
    public function testContentWithoutFilePath()
    {
        $file = new CommonMark(self::COMMONMARK_FILE);
        $this->assertInstanceOf('Apparat\Resource\File\CommonMark', $file);
        $this->assertEquals(1, count($file));

        /** @var \Apparat\Resource\File\Part\Body\CommonMark $commonMarkPart */
        $commonMarkPart = $file->getPart(\Apparat\Resource\File\PartInterface::DEFAULT_NAME);
        $this->assertInstanceOf('Apparat\Resource\File\Part\Body\CommonMark', $commonMarkPart);
//        echo $commonMarkPart->toHTML();
    }
}
