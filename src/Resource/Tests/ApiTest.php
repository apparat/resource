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
    use Apparat\Resource\Infrastructure\Io\InMemory\Writer;
    use Apparat\Resource\Infrastructure\Service\RuntimeException;
    use Apparat\Resource\Ports\InvalidArgumentException;
    use Apparat\Resource\Ports\Tools;

    /**
     * I/O handler test
     *
     * @package     Apparat\Resource
     * @subpackage  Apparat\Resource\Tests
     */
    class ApiTest extends AbstractTest
    {
        /**
         * Example text file
         *
         * @var string
         */
        const TXT_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'Fixture' . DIRECTORY_SEPARATOR . 'cc0.txt';

        /**
         * Test invalid reader stream wrapper while copying
         *
         * @expectedException InvalidArgumentException
         * @expectedExceptionCode 1448493550
         */
        public function testCopyInvalidReaderStreamWrapper()
        {
            Tools::copy('foo://bar');
        }

        /**
         * Test invalid writer stream wrapper while copying
         *
         * @expectedException InvalidArgumentException
         * @expectedExceptionCode 1448493564
         */
        public function testCopyInvalidWriterStreamWrapper()
        {
            Tools::copy('file://' . self::TXT_FILE)->toTarget('foo://bar');
        }

        /**
         * Test copying a string to a file
         */
        public function testCopyStringToFile()
        {
            $tempFile = $this->createTemporaryFileName();
            $randomString = md5(rand());
            Tools::copy($randomString)->toTarget('file://' . $tempFile);
            $this->assertStringEqualsFile($tempFile, $randomString);
        }

        /**
         * Test copying a file to a file
         */
        public function testCopyFileToFile()
        {
            $tempFile = $this->createTemporaryFileName();
            Tools::copy('file://' . self::TXT_FILE)->toTarget('file://' . $tempFile);
            $this->assertFileEquals($tempFile, self::TXT_FILE);
        }

        /**
         * Test error while copying a file to a file
         *
         * @expectedException RuntimeException
         * @expectedExceptionCode 1448569381
         */
        public function testCopyFileToFileError()
        {
            putenv('MOCK_COPY=1');
            $tempFile = $this->createTemporaryFileName();
            Tools::copy('file://' . self::TXT_FILE)->toTarget('file://' . $tempFile);
            $this->assertFileEquals($tempFile, self::TXT_FILE);
            putenv('MOCK_COPY');
        }

        /**
         * Test copying a string to a string
         */
        public function testCopyStringToString()
        {
            $randomString = md5(rand());
            /** @var Writer $writer */
            $writer = Tools::copy($randomString)->toTarget('');
            $this->assertInstanceOf(Writer::class, $writer);
            $this->assertEquals($randomString, $writer->getData());
        }

        /**
         * Test copying a file to a string
         */
        public function testCopyFileToString()
        {
            /** @var Writer $writer */
            $writer = Tools::copy('file://' . self::TXT_FILE)->toTarget('');
            $this->assertInstanceOf(Writer::class, $writer);
            $this->assertStringEqualsFile(self::TXT_FILE, $writer->getData());
        }

        /**
         * Test invalid reader stream wrapper while moving
         *
         * @expectedException InvalidArgumentException
         * @expectedExceptionCode 1448493550
         */
        public function testMoveInvalidReaderStreamWrapper()
        {
            Tools::move('foo://bar');
        }

        /**
         * Test invalid writer stream wrapper while moving
         *
         * @expectedException InvalidArgumentException
         * @expectedExceptionCode 1448493564
         */
        public function testMoveInvalidWriterStreamWrapper()
        {
            Tools::move('file://' . self::TXT_FILE)->toTarget('foo://bar');
        }

        /**
         * Test moving a string to a file
         */
        public function testMoveStringToFile()
        {
            $tempFile = $this->createTemporaryFileName();
            $randomString = md5(rand());
            Tools::move($randomString)->toTarget('file://' . $tempFile);
            $this->assertStringEqualsFile($tempFile, $randomString);
        }

        /**
         * Test moving a file to a file
         */
        public function testMoveFileToFile()
        {
            $srcFile = $this->createTemporaryFileName();
            copy(self::TXT_FILE, $srcFile);
            $tempFile = $this->createTemporaryFileName();
            Tools::move('file://' . $srcFile)->toTarget('file://' . $tempFile);
            $this->assertFileEquals(self::TXT_FILE, $tempFile);
        }

        /**
         * Test error while moving a file to a file
         *
         * @expectedException RuntimeException
         * @expectedExceptionCode 1448571473
         */
        public function testMoveFileToFileError()
        {
            putenv('MOCK_MOVE=1');
            $tempFile = $this->createTemporaryFileName();
            Tools::move('file://' . self::TXT_FILE)->toTarget('file://' . $tempFile);
            $this->assertFileEquals($tempFile, self::TXT_FILE);
            putenv('MOCK_MOVE');
        }

        /**
         * Test moving a string to a string
         */
        public function testMoveStringToString()
        {
            $randomString = md5(rand());
            /** @var Writer $writer */
            $writer = Tools::move($randomString)->toTarget('');
            $this->assertInstanceOf(Writer::class, $writer);
            $this->assertEquals($randomString, $writer->getData());
        }

        /**
         * Test moving a file to a string
         */
        public function testMoveFileToString()
        {
            $srcFile = $this->createTemporaryFileName();
            copy(self::TXT_FILE, $srcFile);
            /** @var Writer $writer */
            $writer = Tools::move('file://' . $srcFile)->toTarget('');
            $this->assertInstanceOf(Writer::class, $writer);
            $this->assertStringEqualsFile(self::TXT_FILE, $writer->getData());
        }

        /**
         * Test deleting a file
         */
        public function testDeleteFile()
        {
            $srcFile = $this->createTemporaryFileName();
            copy(self::TXT_FILE, $srcFile);
            $this->assertEquals(true, Tools::delete('file://' . $srcFile));
            $this->assertFileNotExists($srcFile);
        }

        /**
         * Test error while deleting a file
         *
         * @expectedException RuntimeException
         * @expectedExceptionCode 1448574428
         */
        public function testDeleteFileError()
        {
            putenv('MOCK_UNLINK=1');
            Tools::delete('file://' . self::TXT_FILE);
            putenv('MOCK_UNLINK');
        }

        /**
         * Test invalid reader stream wrapper while deleting a file
         *
         * @expectedException InvalidArgumentException
         * @expectedExceptionCode 1448493550
         */
        public function testDeleteInvalidReaderStreamWrapper()
        {
            Tools::delete('foo://bar');
        }
    }
}

namespace Apparat\Resource\Infrastructure\Service {

    /**
     * Mocked version of the native copy() function
     *
     * @param string $source Source file
     * @param string $dest Destination file
     * @return bool
     */
    function copy($source, $dest)
    {
        return (getenv('MOCK_COPY') != 1) ? \copy($source, $dest) : false;
    }

    /**
     * Mocked version of the native rename() function
     *
     * @param string $source Source file
     * @param string $dest Destination file
     * @return bool
     */
    function rename($source, $dest)
    {
        return (getenv('MOCK_MOVE') != 1) ? \rename($source, $dest) : false;
    }

    /**
     * Mocked version of the native unlink() function
     *
     * @param string $filename File name
     * @return bool
     */
    function unlink($filename)
    {
        return (getenv('MOCK_UNLINK') != 1) ? \unlink($filename) : false;
    }
}
