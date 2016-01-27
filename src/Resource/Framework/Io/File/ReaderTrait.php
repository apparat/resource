<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
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

namespace Apparat\Resource\Framework\Io\File;

/**
 * File reader trait
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 * @property string $_file File path
 */
trait ReaderTrait
{
    /**
     * Read the file content
     *
     * @return string File content
     */
    public function read()
    {
        return file_get_contents($this->_file);
    }

    /**
     * Validate the reader file
     *
     * @throws InvalidArgumentException If the file does not exist
     * @throws InvalidArgumentException If the file is not a file
     * @throws InvalidArgumentException If the file is not readable
     */
    protected function _validateReaderFile()
    {
        // If the file does not exist
        if (!@file_exists($this->_file)) {
            throw new InvalidArgumentException(
                sprintf('File "%s" does not exist', $this->_file),
                InvalidArgumentException::FILE_DOES_NOT_EXIST
            );
        }

        // If the file is not a file
        if (!@is_file($this->_file)) {
            throw new InvalidArgumentException(
                sprintf('File "%s" is not a file', $this->_file),
                InvalidArgumentException::FILE_IS_NOT_A_FILE
            );
        }

        // If the file is not readable
        if (!@is_readable($this->_file)) {
            throw new InvalidArgumentException(
                sprintf('File "%s" is not readable', $this->_file),
                InvalidArgumentException::FILE_NOT_READABLE
            );
        }
    }
}
