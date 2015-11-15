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

namespace Apparat\Resource\Framework\Io\File {

    /**
     * Mocked version of the native is_readable() function
     *
     * @param $filename
     * @return bool
     */
    function is_readable($filename) {
        return empty($GLOBALS['mockIsReadable']) ? \is_readable($filename) : false;
    }
}

namespace ApparatTest {

    use Apparat\Resource\Framework\Io\File\InvalidArgumentException;
    use Apparat\Resource\Framework\Io\File\Reader;

    /**
     * FileIo tests
     *
     * @package ApparatTest
     */
    class FileIoTest extends TestBase
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
         * Test the file reader
         */
        public function testFileReader()
        {
            $reader = new Reader(self::TXT_FILE);
            $this->assertInstanceOf(Reader::class, $reader);
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
        public function testFileReadeWithDirectory()
        {
            new Reader(dirname(self::TXT_FILE));
        }

        /**
         * Test the file reader with an unreadable file
         *
         * @expectedException InvalidArgumentException
         * @expectedExceptionCode 1447617006
         */
        public function testFileReadeWithUnreadableFile()
        {
            $GLOBALS['mockIsReadable'] = true;
            new Reader(self::TXT_FILE);
            unset($GLOBALS['mockIsReadable']);
        }
    }
}