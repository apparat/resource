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

use Apparat\Kernel\Tests\AbstractTest;
use Apparat\Resource\Framework\Io\InMemory\Reader;
use Apparat\Resource\Framework\Model\Part\CommonMarkPart;
use Apparat\Resource\Framework\Model\Resource\CommonMarkResource;

/**
 * CommonMark file tests
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Tests
 */
class CommonMarkTest extends AbstractTest
{
    /**
     * Example CommonMark data
     *
     * @var string
     */
    protected $commonMark = null;

    /**
     * Example CommonMark file
     *
     * @var string
     */
    const COMMONMARK_FILE = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'commonmark.md';

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        parent::setUp();
        $this->commonMark = file_get_contents(self::COMMONMARK_FILE);
    }

    /**
     * Test the CommonMark file constructor
     */
    public function testCommonMarkResource()
    {
        $commonMarkResource = new CommonMarkResource();
        $this->assertInstanceOf(CommonMarkResource::class, $commonMarkResource);
        $this->assertEquals(CommonMarkPart::MIME_TYPE, $commonMarkResource->getMimeTypePart());
    }

    /**
     * Test the CommonMark file constructor with reader
     */
    public function testCommonMarkResourceReader()
    {
        $commonMarkResource = new CommonMarkResource(new Reader($this->commonMark));
        $this->assertEquals($this->commonMark, $commonMarkResource->getPart());
    }

    /**
     * Test setting the content of a CommonMark file
     */
    public function testCommonMarkResourceSet()
    {
        $randomSet = md5(rand());
        $commonMarkResource = new CommonMarkResource();
        $commonMarkResource->setPart($randomSet);
        $this->assertEquals($randomSet, $commonMarkResource->getPart());
    }

    /**
     * Test appending content to a CommonMark file
     */
    public function testCommonMarkResourceAppend()
    {
        $randomSet = md5(rand());
        $randomAppend = md5(rand());
        $commonMarkResource = new CommonMarkResource();
        $commonMarkResource->setPart($randomSet)->appendPart($randomAppend);
        $this->assertEquals($randomSet.$randomAppend, $commonMarkResource->getPart());
    }

    /**
     * Test prepending content to a CommonMark file
     */
    public function testCommonMarkResourcePrepend()
    {
        $randomSet = md5(rand());
        $randomPrepend = md5(rand());
        $commonMarkResource = new CommonMarkResource();
        $commonMarkResource->setPart($randomSet)->prependPart($randomPrepend);
        $this->assertEquals($randomPrepend.$randomSet, $commonMarkResource->getPart());
    }

    /**
     * Test getting the HTML content of a CommonMark file with unallowed subparts
     *
     * @expectedException \Apparat\Resource\Domain\Model\Part\InvalidArgumentException
     * @expectedExceptionCode 1447365624
     */
    public function testCommonMarkResourceHtmlSubparts()
    {
        $commonMarkResource = new CommonMarkResource(new Reader($this->commonMark));
        $commonMarkResource->getHtmlPart('a/b/c');
    }

    /**
     * Test getting the HTML content part of a CommonMark file
     */
    public function testCommonMarkResourceHtmlPart()
    {
        $expectedHtml = $this->normalizeHtml(
            file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'commonmark.html')
        );
        $commonMarkResource = new CommonMarkResource(new Reader($this->commonMark));
        $this->assertEquals($expectedHtml, $this->normalizeHtml($commonMarkResource->getHtmlPart()));
    }

    /**
     * Test getting the HTML content of a CommonMark file
     */
    public function testCommonMarkResourceHtml()
    {
        $expectedHtml = $this->normalizeHtml(
            file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'commonmark.html')
        );
        $commonMarkResource = new CommonMarkResource(new Reader($this->commonMark));
        $this->assertEquals($expectedHtml, $this->normalizeHtml($commonMarkResource->getHtml()));
    }
}
