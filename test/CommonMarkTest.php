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

use Apparat\Resource\Framework\File\CommonMarkFile;
use Apparat\Resource\Framework\Io\InMemory\Reader;
use Apparat\Resource\Framework\Part\CommonMarkPart;

/**
 * CommonMark file tests
 *
 * @package ApparatTest
 */
class CommonMarkTest extends TestBase
{
    /**
     * Example CommonMark data
     *
     * @var string
     */
    protected $_commonMark = null;

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
        $this->_commonMark = file_get_contents(self::COMMONMARK_FILE);
    }

    /**
     * Test the CommonMark file constructor
     */
    public function testCommonMarkFile()
    {
        $commonMarkFile = new CommonMarkFile();
        $this->assertInstanceOf(CommonMarkFile::class, $commonMarkFile);
        $this->assertEquals(CommonMarkPart::MIME_TYPE, $commonMarkFile->getMimeTypePart());
    }

    /**
     * Test the CommonMark file constructor with reader
     */
    public function testCommonMarkFileReader()
    {
        $commonMarkFile = new CommonMarkFile(new Reader($this->_commonMark));
        $this->assertEquals($this->_commonMark, $commonMarkFile->getPart());
    }

    /**
     * Test setting the content of a CommonMark file
     */
    public function testCommonMarkFileSet()
    {
        $randomSet = md5(rand());
        $commonMarkFile = new CommonMarkFile();
        $commonMarkFile->setPart($randomSet);
        $this->assertEquals($randomSet, $commonMarkFile->getPart());
    }

    /**
     * Test appending content to a CommonMark file
     */
    public function testCommonMarkFileAppend()
    {
        $randomSet = md5(rand());
        $randomAppend = md5(rand());
        $commonMarkFile = new CommonMarkFile();
        $commonMarkFile->setPart($randomSet)->appendPart($randomAppend);
        $this->assertEquals($randomSet.$randomAppend, $commonMarkFile->getPart());
    }

    /**
     * Test prepending content to a CommonMark file
     */
    public function testCommonMarkFilePrepend()
    {
        $randomSet = md5(rand());
        $randomPrepend = md5(rand());
        $commonMarkFile = new CommonMarkFile();
        $commonMarkFile->setPart($randomSet)->prependPart($randomPrepend);
        $this->assertEquals($randomPrepend.$randomSet, $commonMarkFile->getPart());
    }

    /**
     * Test getting the HTML content of a CommonMark file with unallowed subparts
     *
     * @expectedException \Apparat\Resource\Model\Part\InvalidArgumentException
     * @expectedExceptionCode 1447365624
     */
    public function testCommonMarkFileHtmlSubparts()
    {
        $commonMarkFile = new CommonMarkFile(new Reader($this->_commonMark));
        $commonMarkFile->getHtmlPart('a/b/c');
    }

    /**
     * Test getting the HTML content of a CommonMark file with unallowed subparts
     *
     * @todo Replace reference file as soon as the HTML entity parsing bug is fixed
     * @see https://github.com/thephpleague/commonmark/issues/206
     */
    public function testCommonMarkFileHtml()
    {
        $expectedHtml = $this->_normalizeHtml(file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'commonmark#206.html'));
        $commonMarkFile = new CommonMarkFile(new Reader($this->_commonMark));
        $this->assertEquals($expectedHtml, $this->_normalizeHtml($commonMarkFile->getHtmlPart()));
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Normalize HTML contents
     *
     * @param string $html Original HTML
     * @return string Normalized HTML
     */
    protected function _normalizeHtml($html)
    {
        $htmlDom = new \DOMDocument();
        $htmlDom->preserveWhiteSpace = false;
        $htmlDom->formatOutput = false;
        $htmlDom->loadXML("<html><head><title>apparat</title></head><body>$html</body></html>");
        return $htmlDom->saveXML();
    }
}