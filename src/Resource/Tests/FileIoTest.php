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

namespace Apparat\Resource\Tests {

    use Apparat\Dev\Tests\AbstractTest;
    use Apparat\Kernel\Ports\Kernel;
    use Apparat\Resource\Infrastructure\Io\File\Reader;
    use Apparat\Resource\Infrastructure\Io\File\ReaderWriter;
    use Apparat\Resource\Infrastructure\Io\File\Writer;
    use Apparat\Resource\Infrastructure\Model\Resource\TextResource;

    /**
     * FileIo tests
     *
     * @package     Apparat\Resource
     * @subpackage  Apparat\Resource\Tests
     */
    class FileIoTest extends AbstractTest
    {
        /**
         * Example text file
         *
         * @var string
         */
        const TXT_FILE = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'cc0.txt';
        /**
         * Example text data
         *
         * @var string
         */
        protected $text = null;

        /**
         * Tears down the fixture
         */
        public function tearDown()
        {
            putenv('MOCK_IS_READABLE');
            putenv('MOCK_IS_WRITEABLE');
            parent::tearDown();
        }

        /**
         * Test the file reader with an invalid file path
         *
         * @expectedException \Apparat\Resource\Ports\InvalidReaderArgumentException
         * @expectedExceptionCode 1447616824
         */
        public function testFileReaderWithInvalidFilepath()
        {
            Kernel::create(Reader::class, [self::TXT_FILE.'_invalid']);
        }

        /**
         * Test the file reader with a directory path
         *
         * @expectedException \Apparat\Resource\Ports\InvalidReaderArgumentException
         * @expectedExceptionCode 1447618938
         */
        public function testFileReaderWithDirectory()
        {
            Kernel::create(Reader::class, [dirname(self::TXT_FILE)]);
        }

        /**
         * Test the file reader with an unreadable file
         *
         * @expectedException \Apparat\Resource\Ports\InvalidReaderArgumentException
         * @expectedExceptionCode 1447617006
         */
        public function testFileReaderWithUnreadableFile()
        {
            putenv('MOCK_IS_READABLE=1');
            Kernel::create(Reader::class, [self::TXT_FILE]);
        }

        /**
         * Test the file reader
         */
        public function testFileReader()
        {
            $fileReader = Kernel::create(Reader::class, [self::TXT_FILE]);
            $this->assertInstanceOf(Reader::class, $fileReader);
            $textResource = Kernel::create(TextResource::class, [$fileReader]);
            $inMemoryWriter = Kernel::create(\Apparat\Resource\Infrastructure\Io\InMemory\Writer::class);
            $textResource->dump($inMemoryWriter);
            $this->assertEquals($this->text, $inMemoryWriter->getData());
        }

        /**
         * Test the file writer with invalid writer options
         *
         * @expectedException \Apparat\Resource\Ports\InvalidWriterArgumentException
         * @expectedExceptionCode 1447617559
         */
        public function testFileWriterWithInvalidOptions()
        {
            Kernel::create(\Apparat\Resource\Infrastructure\Io\File\Writer::class, [self::TXT_FILE, pow(2, 10)]);
        }

        /**
         * Test the file writer with non-creatable file
         *
         * @expectedException \Apparat\Resource\Ports\InvalidWriterArgumentException
         * @expectedExceptionCode 1447617960
         */
        public function testFileWriterWithNonCreatableFile()
        {
            Kernel::create(\Apparat\Resource\Infrastructure\Io\File\Writer::class, [self::TXT_FILE.'_new', 0]);
        }

        /**
         * Test the file writer with non-overwriteable file
         *
         * @expectedException \Apparat\Resource\Ports\InvalidWriterArgumentException
         * @expectedExceptionCode 1447617979
         */
        public function testFileWriterWithNonOverwriteableFile()
        {
            Kernel::create(\Apparat\Resource\Infrastructure\Io\File\Writer::class, [self::TXT_FILE, 0]);
        }

        /**
         * Test the file writer with non-writeable file
         *
         * @expectedException \Apparat\Resource\Ports\InvalidWriterArgumentException
         * @expectedExceptionCode 1447617979
         */
        public function testFileWriterWithNonWriteableFile()
        {
            putenv('MOCK_IS_WRITEABLE=1');
            Kernel::create(
                \Apparat\Resource\Infrastructure\Io\File\Writer::class,
                [self::TXT_FILE, Writer::FILE_OVERWRITE]
            );
        }

        /**
         * Test the file writer with a newly created file
         */
        public function testFileWriterWithCreatedFile()
        {
            $textResource = Kernel::create(
                TextResource::class,
                [Kernel::create(\Apparat\Resource\Infrastructure\Io\InMemory\Reader::class, [$this->text])]
            );
            $tempFile = $this->createTemporaryFileName();
            $textResource->dump(Kernel::create(Writer::class, [$tempFile, Writer::FILE_CREATE]));
            $this->assertFileEquals(self::TXT_FILE, $tempFile);
        }

        /**
         * Test the file writer with an overwritten file
         */
        public function testFileWriterWithOverwrittenFile()
        {
            $textResource = Kernel::create(
                TextResource::class,
                [Kernel::create(\Apparat\Resource\Infrastructure\Io\InMemory\Reader::class, [$this->text])]
            );
            $tempFile = $this->createTemporaryFile();
            $textResource->dump(Kernel::create(Writer::class, [$tempFile, Writer::FILE_OVERWRITE]));
            $this->assertFileEquals(self::TXT_FILE, $tempFile);
        }

        /**
         * Test the file writer with recursive directory creation
         */
        public function testFileWriterWithRecursiveDirectoryCreation()
        {
            $textResource = Kernel::create(
                TextResource::class,
                [Kernel::create(\Apparat\Resource\Infrastructure\Io\InMemory\Reader::class, [$this->text])]
            );
            $tempFile = $this->createTemporaryFile();
            unlink($tempFile);
            $this->tmpFiles[] = $tempFile .= DIRECTORY_SEPARATOR.'recursive.txt';
            $textResource->dump(
                Kernel::create(Writer::class, [$tempFile, Writer::FILE_CREATE | Writer::FILE_CREATE_DIRS])
            );
            $this->assertFileEquals(self::TXT_FILE, $tempFile);
        }

        /**
         * Test the file writer with recursive directory creation
         *
         * @expectedException \Apparat\Resource\Ports\InvalidWriterArgumentException
         * @expectedExceptionCode 1461448384
         */
        public function testFileWriterWithoutDirectoryCreation()
        {
            $textResource = Kernel::create(
                TextResource::class,
                [Kernel::create(\Apparat\Resource\Infrastructure\Io\InMemory\Reader::class, [$this->text])]
            );
            $tempFile = $this->createTemporaryFile();
            unlink($tempFile);
            $this->tmpFiles[] = $tempFile .= DIRECTORY_SEPARATOR.'recursive.txt';
            $textResource->dump(Kernel::create(Writer::class, [$tempFile, Writer::FILE_CREATE]));
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
            $fileReaderWriter = Kernel::create(ReaderWriter::class, [$tempFile]);
            $textResource = Kernel::create(TextResource::class, [$fileReaderWriter]);
            $textResource->appendPart($randomAppend)->dump($fileReaderWriter);
            $this->assertStringEqualsFile($tempFile, $this->text.$randomAppend);
        }

        /**
         * Sets up the fixture
         */
        protected function setUp()
        {
            parent::setUp();
            $this->text = file_get_contents(self::TXT_FILE);
        }
    }
}

namespace Apparat\Resource\Infrastructure\Io\File {

    /**
     * Mocked version of the native is_readable() function
     *
     * @param $filename
     * @return bool
     */
    function is_readable($filename)
    {
        return (getenv('MOCK_IS_READABLE') != 1) ? \is_readable($filename) : false;
    }

    /**
     * Mocked version of the native is_writeable() function
     *
     * @param $filename
     * @return bool
     */
    function is_writeable($filename)
    {
        return (getenv('MOCK_IS_WRITEABLE') != 1) ? \is_writeable($filename) : false;
    }
}
