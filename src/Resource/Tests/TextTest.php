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
use Apparat\Kernel\Tests\AbstractTest;
use Apparat\Resource\Domain\Model\Part\InvalidArgumentException;
use Apparat\Resource\Domain\Model\Resource\RuntimeException;
use Apparat\Resource\Infrastructure\Io\InMemory\Reader;
use Apparat\Resource\Infrastructure\Io\InMemory\ReaderWriter;
use Apparat\Resource\Infrastructure\Io\InMemory\Writer;
use Apparat\Resource\Infrastructure\Model\Part\TextPart;
use Apparat\Resource\Infrastructure\Model\Resource\TextResource;

/**
 * Text file tests
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Tests
 */
class TextTest extends AbstractTest
{
    /**
     * Example text data
     *
     * @var string
     */
    protected $text = null;

    /**
     * Example text file
     *
     * @var string
     */
    const TXT_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'cc0.txt';

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        parent::setUp();
        $this->text = file_get_contents(self::TXT_FILE);
    }

    /**
     * Test the text file constructor
     */
    public function testTextResource()
    {
        $textReource = Kernel::create(TextResource::class, [null]);
        $this->assertInstanceOf(TextResource::class, $textReource);
        $this->assertEquals(TextPart::MEDIA_TYPE, $textReource->getMediaTypePart());
    }

    /**
     * Test the text file constructor with reader
     */
    public function testTextResourceReader()
    {
        $textReource = Kernel::create(TextResource::class, [Kernel::create(Reader::class, [$this->text])]);
        $this->assertEquals($this->text, $textReource->getPart());
    }

    /**
     * Test the text file constructor with explicit loading
     */
    public function testTextResourceLoad()
    {
        $textReource = Kernel::create(TextResource::class, [null]);
        $textReource->load(Kernel::create(Reader::class, [$this->text]));
        $this->assertEquals($this->text, $textReource->getPart());
    }

    /**
     * Test invalid file method
     *
     * @expectedException RuntimeException
     * @expectedExceptionCode 1447450449
     */
    public function testTextResourceInvalidMethod()
    {
        $textReource = Kernel::create(TextResource::class, [null]);
        $textReource->undefinedMethod();
    }

    /**
     * Test setting the content part of a text file
     */
    public function testTextResourceSetPart()
    {
        $randomSet = md5(rand());
        $textReource = Kernel::create(TextResource::class, [null]);
        $textReource->setPart($randomSet);
        $this->assertEquals($randomSet, $textReource->getPart());
    }

    /**
     * Test setting the content of a text file
     */
    public function testTextResourceSet()
    {
        $randomSet = md5(rand());
        $textReource = Kernel::create(TextResource::class, [null]);
        $textReource->set($randomSet);
        $this->assertEquals($randomSet, $textReource->getPart());
    }

    /**
     * Test getting the content of a text file
     */
    public function testTextResourceGet()
    {
        $textReource = Kernel::create(TextResource::class, [Kernel::create(Reader::class, [$this->text])]);
        $this->assertEquals($this->text, $textReource->get());
    }

    /**
     * Test setting and appending content to a text file part
     */
    public function testTextResourceAppendPart()
    {
        $randomSet = md5(rand());
        $randomAppend = md5(rand());
        $textReource = Kernel::create(TextResource::class, [null]);
        $textReource->setPart($randomSet)->appendPart($randomAppend);
        $this->assertEquals($randomSet . $randomAppend, $textReource->getPart());
    }

    /**
     * Test appending content to a text file
     */
    public function testTextResourceAppend()
    {
        $randomSet = md5(rand());
        $randomAppend = md5(rand());
        $textReource = Kernel::create(TextResource::class, [null]);
        $textReource->set($randomSet)->append($randomAppend);
        $this->assertEquals($randomSet . $randomAppend, $textReource->get());
    }

    /**
     * Test prepending content to a text file part
     */
    public function testTextResourcePrependPart()
    {
        $randomSet = md5(rand());
        $randomPrepend = md5(rand());
        $textReource = Kernel::create(TextResource::class, [null]);
        $textReource->setPart($randomSet)->prependPart($randomPrepend);
        $this->assertEquals($randomPrepend . $randomSet, $textReource->getPart());
    }


    /**
     * Test appending content to a text file
     */
    public function testTextResourcePrepend()
    {
        $randomSet = md5(rand());
        $randomPrepend = md5(rand());
        $textReource = Kernel::create(TextResource::class, [null]);
        $textReource->set($randomSet)->prepend($randomPrepend);
        $this->assertEquals($randomPrepend . $randomSet, $textReource->get());
    }

    /**
     * Test an invalid path identifier
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1447364401
     */
    public function testTextResourceInvalidPathIdentifier()
    {
        $textReource = Kernel::create(TextResource::class, [null]);
        $textReource->getPart('-');
    }

    /**
     * Test appending to the content of a text file with unallowed subparts
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1447365624
     */
    public function testTextResourceAppendSubparts()
    {
        $textReource = Kernel::create(TextResource::class, [null]);
        $textReource->appendPart(md5(rand()), 'a/b/c');
    }

    /**
     * Test prepending to the content of a text file with unallowed subparts
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1447365624
     */
    public function testTextResourcePrependSubparts()
    {
        $textReource = Kernel::create(TextResource::class, [null]);
        $textReource->prependPart(md5(rand()), 'a/b/c');
    }

    /**
     * Test getting the media type of a text file with unallowed subparts
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1447365624
     */
    public function testTextResourceMimeTypeSubparts()
    {
        $textReource = Kernel::create(TextResource::class, [null]);
        $textReource->getMediaTypePart('a/b/c');
    }

    /**
     * Test dumping the contents of a text file
     */
    public function testTextResourceDump()
    {
        $randomSet = md5(rand());
        $writer = Kernel::create(Writer::class);
        $textReource = Kernel::create(TextResource::class, [null]);
        $textReource->setPart($randomSet)->dump($writer);
        $this->assertEquals($randomSet, $writer->getData());
    }

    /**
     * Test the in-memory universal reader / writer
     */
    public function testTextResourceReaderWriter()
    {
        $randomAppend = md5(rand());
        $readerWriter = Kernel::create(ReaderWriter::class, [$this->text]);
        $textReource = Kernel::create(TextResource::class, [$readerWriter]);
        $textReource->appendPart($randomAppend)->dump($readerWriter);
        $this->assertEquals($this->text . $randomAppend, $readerWriter->getData());
    }
}
