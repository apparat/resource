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

use Apparat\Resource\Framework\File\TextFile;
use Apparat\Resource\Framework\Part\TextPart;
use Apparat\Resource\Framework\Reader\InMemoryReader;
use Apparat\Resource\Framework\Writer\InMemoryWriter;
use Apparat\Resource\Model\File\RuntimeException;

/**
 * Text file tests
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
     * Test the text file constructor
     */
    public function testTextFile()
    {
        $textFile = new TextFile();
        $this->assertInstanceOf(TextFile::class, $textFile);
        $this->assertEquals(TextPart::MIME_TYPE, $textFile->getMimeTypePart());
    }

    /**
     * Test the text file constructor with reader
     */
    public function testTextFileReader()
    {
        $textFile = new TextFile(new InMemoryReader($this->_text));
        $this->assertEquals($this->_text, $textFile->getPart());
    }

    /**
     * Test the text file constructor with explicit loading
     */
    public function testTextFileLoad()
    {
        $textFile = new TextFile();
        $textFile->load(new InMemoryReader($this->_text));
        $this->assertEquals($this->_text, $textFile->getPart());
    }

    /**
     * Test invalid file method
     *
     * @expectedException RuntimeException
     * @expectedExceptionCode 1447450449
     */
    public function testTextFileInvalidMethod()
    {
        $textFile = new TextFile();
        $textFile->undefinedMethod();
    }

    /**
     * Test invalid file part method
     *
     * @expectedException RuntimeException
     * @expectedExceptionCode 1447366704
     */
    public function testTextFilePartInvalidMethod()
    {
        $textFile = new TextFile();
        $textFile->undefinedMethodPart();
    }

    /**
     * Test setting the content of a text file
     */
    public function testTextFileSet()
    {
        $randomSet = md5(rand());
        $textFile = new TextFile();
        $textFile->setPart($randomSet);
        $this->assertEquals($randomSet, $textFile->getPart());
    }

    /**
     * Test appending content to a text file
     */
    public function testTextFileAppend()
    {
        $randomSet = md5(rand());
        $randomAppend = md5(rand());
        $textFile = new TextFile();
        $textFile->setPart($randomSet)->appendPart($randomAppend);
        $this->assertEquals($randomSet.$randomAppend, $textFile->getPart());
    }

    /**
     * Test an invalid path identifier
     *
     * @expectedException \Apparat\Resource\Model\File\InvalidArgumentException
     * @expectedExceptionCode 1447364401
     */
    public function testTextFileInvalidPathIdentifier()
    {
        $textFile = new TextFile();
        $textFile->getPart('-');
    }

    /**
     * Test setting the content of a text file with unallowed subparts
     *
     * @expectedException \Apparat\Resource\Model\Part\InvalidArgumentException
     * @expectedExceptionCode 1447365624
     */
    public function testTextFileSetSubparts()
    {
        $textFile = new TextFile();
        $textFile->setPart(md5(rand()), 'a/b/c');
    }

    /**
     * Test appending to the content of a text file with unallowed subparts
     *
     * @expectedException \Apparat\Resource\Model\Part\InvalidArgumentException
     * @expectedExceptionCode 1447365624
     */
    public function testTextFileAppendSubparts()
    {
        $textFile = new TextFile();
        $textFile->appendPart(md5(rand()), 'a/b/c');
    }

    /**
     * Test getting the content of a text file with unallowed subparts
     *
     * @expectedException \Apparat\Resource\Model\Part\InvalidArgumentException
     * @expectedExceptionCode 1447365624
     */
    public function testTextFileGetSubparts()
    {
        $textFile = new TextFile();
        $textFile->getPart('a/b/c');
    }

    /**
     * Test getting the MIME type of a text file with unallowed subparts
     *
     * @expectedException \Apparat\Resource\Model\Part\InvalidArgumentException
     * @expectedExceptionCode 1447365624
     */
    public function testTextFileMimeTypeSubparts()
    {
        $textFile = new TextFile();
        $textFile->getMimeTypePart('a/b/c');
    }

    /**
     * Test dumping the contents of a text file
     */
    public function testTextFileDump()
    {
        $randomSet = md5(rand());
        $writer = new InMemoryWriter();
        $textFile = new TextFile();
        $textFile->setPart($randomSet)->dump($writer);
        $this->assertEquals($randomSet, $writer->getData());
    }
}