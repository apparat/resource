<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Infrastructure
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

namespace Apparat\Resource\Infrastructure\Io\File;

/**
 * File writer trait
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Infrastructure
 * @property string $file File path
 */
trait WriterTrait
{
    /**
     * File options
     *
     * @var int
     */
    protected $options;

    /**
     * Write data
     *
     * @param string $data Data to write
     * @return int Bytes written
     */
    public function write($data)
    {
        // If the parent directory does not exist but may be created
        if (!@is_dir(dirname($this->file)) && ($this->options & Writer::FILE_CREATE_DIRS)) {
            mkdir(dirname($this->file), 0777, true);
        }

        return file_put_contents($this->file, $data);
    }

    /**
     * Set the file options
     *
     * @param int $options File options
     * @throws InvalidWriterArgumentException If the writer options are invalid
     */
    protected function setOptions($options)
    {
        $options = intval($options);
        $allOptions = Writer::FILE_CREATE | Writer::FILE_CREATE_DIRS | Writer::FILE_OVERWRITE;

        if (($options & $allOptions) != $options) {
            throw new InvalidWriterArgumentException(
                sprintf('Invalid file writer option "%s"', $options & ~$allOptions),
                InvalidWriterArgumentException::INVALID_WRITER_OPTIONS
            );
        }

        $this->options = $options;
    }

    /**
     * Validate the writer file
     *
     * @throws InvalidWriterArgumentException If the file cannot be created
     * @throws InvalidWriterArgumentException If the file cannot be replaced
     * @throws InvalidWriterArgumentException If the directory cannot be created
     */
    protected function validateWriterFile()
    {
        // If the parent directory does not exist and cannot be created
        if (!@is_dir(dirname($this->file)) && !($this->options & Writer::FILE_CREATE_DIRS)) {
            throw new InvalidWriterArgumentException(
                sprintf('Parent directory "%s" cannot be created', dirname($this->file)),
                InvalidWriterArgumentException::RESOURCE_CONTAINER_CANNOT_BE_CREATED
            );
        }

        // If the file does not exist and cannot be created
        if (!@file_exists($this->file) && !($this->options & Writer::FILE_CREATE)) {
            throw new InvalidWriterArgumentException(
                sprintf('File "%s" cannot be created', $this->file),
                InvalidWriterArgumentException::RESOURCE_CANNOT_BE_CREATED
            );
        }

        // If the file exists but cannot be overwritten
        if (@file_exists($this->file) &&
            (
                !@is_file($this->file) ||
                !@is_writeable(
                    $this->file
                ) ||
                !($this->options & Writer::FILE_OVERWRITE)
            )
        ) {
            throw new InvalidWriterArgumentException(
                sprintf('File "%s" cannot be overwritten', $this->file),
                InvalidWriterArgumentException::RESOURCE_CANNOT_BE_REPLACED
            );
        }
    }
}
