<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
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

namespace Apparat\Resource\Framework\Io\File {

    /**
     * Mocked version of the native is_readable() function
     *
     * @param $filename
     * @return bool
     */
    function is_readable($filename)
    {
        return empty($GLOBALS['mockIsReadable']) ? \is_readable($filename) : false;
    }

    /**
     * Mocked version of the native is_writeable() function
     *
     * @param $filename
     * @return bool
     */
    function is_writeable($filename)
    {
        return empty($GLOBALS['mockIsWriteable']) ? \is_writeable($filename) : false;
    }
}

namespace ApparatTest {

    use Apparat\Kernel\Tests\AbstractTest;
    use Apparat\Resource\Framework\Io\File\InvalidArgumentException;
    use Apparat\Resource\Framework\Io\File\Reader;
    use Apparat\Resource\Framework\Io\File\ReaderWriter;
    use Apparat\Resource\Framework\Io\File\Writer;
    use Apparat\Resource\Framework\Model\Resource\TextResource;

    /**
     * FileIo tests
     *
     * @package     Apparat\Resource
     * @subpackage  Apparat\Resource\Framework
     */
    class FileIoTest extends AbstractTest
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
        const TXT_FILE = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'cc0.txt';

        /**
         * Sets up the fixture
         */
        protected function setUp()
        {
            parent::setUp();
            $this->text = file_get_contents(self::TXT_FILE);
        }

        /**
         * Test the file reader with an invalid file path
         *
         * @expectedException InvalidArgumentException
         * @expectedExceptionCode 1447616824
         */
        public function testFileReaderWithInvalidFilepath()
        {
            new Reader(self::TXT_FILE.'_invalid');
        }

        /**
         * Test the file reader with a directory path
         *
         * @expectedException InvalidArgumentException
         * @expectedExceptionCode 1447618938
         */
        public function testFileReaderWithDirectory()
        {
            new Reader(dirname(self::TXT_FILE));
        }

        /**
         * Test the file reader with an unreadable file
         *
         * @expectedException InvalidArgumentException
         * @expectedExceptionCode 1447617006
         */
        public function testFileReaderWithUnreadableFile()
        {
            $GLOBALS['mockIsReadable'] = true;
            new Reader(self::TXT_FILE);
            unset($GLOBALS['mockIsReadable']);
        }

        /**
         * Test the file reader
         */
        public function testFileReader()
        {
            $fileReader = new Reader(self::TXT_FILE);
            $this->assertInstanceOf(Reader::class, $fileReader);
            $textReource = new TextResource($fileReader);
            $inMemoryWriter = new \Apparat\Resource\Framework\Io\InMemory\Writer();
            $textReource->dump($inMemoryWriter);
            $this->assertEquals($this->text, $inMemoryWriter->getData());
        }

        /**
         * Test the file writer with invalid writer options
         *
         * @expectedException InvalidArgumentException
         * @expectedExceptionCode 1447617559
         */
        public function testFileWriterWithInvalidOptions()
        {
            new \Apparat\Resource\Framework\Io\File\Writer(self::TXT_FILE, pow(2, 10));
        }

        /**
         * Test the file writer with non-creatable file
         *
         * @expectedException InvalidArgumentException
         * @expectedExceptionCode 1447617960
         */
        public function testFileWriterWithNonCreatableFile()
        {
            new \Apparat\Resource\Framework\Io\File\Writer(self::TXT_FILE.'_new', 0);
        }

        /**
         * Test the file writer with non-overwriteable file
         *
         * @expectedException InvalidArgumentException
         * @expectedExceptionCode 1447617979
         */
        public function testFileWriterWithNonOverwriteableFile()
        {
            new \Apparat\Resource\Framework\Io\File\Writer(self::TXT_FILE, 0);
        }

        /**
         * Test the file writer with non-writeable file
         *
         * @expectedException InvalidArgumentException
         * @expectedExceptionCode 1447617979
         */
        public function testFileWriterWithNonWriteableFile()
        {
            $GLOBALS['mockIsWriteable'] = true;
            new \Apparat\Resource\Framework\Io\File\Writer(self::TXT_FILE, Writer::FILE_OVERWRITE);
            unset($GLOBALS['mockIsWriteable']);
        }

        /**
         * Test the file writer with a newly created file
         */
        public function testFileWriterWithCreatedFile()
        {
            $textReource = new TextResource(new \Apparat\Resource\Framework\Io\InMemory\Reader($this->text));
            $tempFile = $this->createTemporaryFileName();
            $textReource->dump(new Writer($tempFile, Writer::FILE_CREATE));
            $this->assertFileEquals(self::TXT_FILE, $tempFile);
        }

        /**
         * Test the file writer with an overwritten file
         */
        public function testFileWriterWithOverwrittenFile()
        {
            $textReource = new TextResource(new \Apparat\Resource\Framework\Io\InMemory\Reader($this->text));
            $tempFile = $this->createTemporaryFile();
            $textReource->dump(new Writer($tempFile, Writer::FILE_OVERWRITE));
            $this->assertFileEquals(self::TXT_FILE, $tempFile);
        }

        /**
         * Test the file reader/writer
         */
        public function testFileReaderWriterWithCreatedFile()
        {
            $tempFile = $this->createTemporaryFileName();
            copy(self::TXT_FILE, $tempFile);
            $randomAppend = md5(rand());
            $fileReaderWriter = new ReaderWriter($tempFile);
            $textReource = new TextResource($fileReaderWriter);
            $textReource->appendPart($randomAppend)->dump($fileReaderWriter);
            $this->assertStringEqualsFile($tempFile, $this->text.$randomAppend);
        }
    }
}
