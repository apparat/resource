<?php

/**
 * resource
 *
 * @category    Apparat
 * @package     Apparat_Resource
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

namespace Apparat\Resource\Domain\File;

use Apparat\Resource\Domain\Container\ContainerTrait;
use Apparat\Resource\Domain\File\Exception\InvalidArgumentException;
use Apparat\Resource\Domain\File\Exception\RuntimeException;
use Apparat\Resource\Domain\Part\PartInterface;
use Apparat\Resource\Domain\Resource\Resource;

/**
 * Abstract file resource
 *
 * @package     Apparat_Resource
 */
abstract class File extends Resource implements FileInterface
{
    /**
     * Use the file part container properties and methods
     */
    use ContainerTrait;

    /**
     * File source
     *
     * @var string
     */
    protected $_source = null;

    /**
     * MIME type
     *
     * @var string
     */
    protected $_mimeType = 'application/octet-stream';

    /**
     * Return the source of this file
     *
     * @return string
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Set the source of this file
     *
     * @param string $source Source file
     * @return File                             Self reference
     * @throws InvalidArgumentException                  When the given source is not valid, doesn't exist, is not a file or is not readable
     */
    public function setSource($source)
    {
        $source = trim($source);

        // If source is a non-empty string
        if (strlen($source)) {
            $this->_source = $source;

            // If source file doesn't exist
            if (!@file_exists($this->_source)) {
                throw new InvalidArgumentException(sprintf('Source file "%s" doesn\'t exist', $this->_source),
                    InvalidArgumentException::INVALID_SOURCE_FILE);
            }

            // If source file is not a directory
            if (!@is_file($this->_source)) {
                throw new InvalidArgumentException(sprintf('Source "%s" is not a file', $this->_source),
                    InvalidArgumentException::SOURCE_NOT_A_FILE);
            }

            // If the source file is not readable
            if (fileperms($this->_source) & 0x04 != 0x04) {
                throw new InvalidArgumentException(sprintf('Source file "%s" is not readable', $this->_source),
                    InvalidArgumentException::SOURCE_FILE_UNREADABLE);
            }

            $this->parse(@file_get_contents($this->_source));

            // If source is empty: Reset the file
        } else {
            $this->_source = null;
            $this->reset();
        }
        return $this;
    }

    /**
     * Save the file
     *
     * @param string $target Target file
     * @param bool|false $createDirectories Create directories if necessary
     * @param bool|false $overwrite Overwrite existing file
     * @return int                              Number of bytes written
     * @throws InvalidArgumentException         If the target file is invalid
     * @throws RuntimeException                 If the target directory doesn't exist and cannot be created
     */
    public function save($target = null, $createDirectories = false, $overwrite = false)
    {
        // Use the source path if target was not given
        if ($target === null) {
            $target = $this->_source;
        }

        $target = trim($target);
        if (!strlen($target)) {
            throw new InvalidArgumentException(sprintf('Invalid target file "%s"', $target),
                InvalidArgumentException::INVALID_TARGET_FILE);
        }

        // If the target directory doesn't exist and should not be created
        if (!@is_dir(dirname($target)) && !$createDirectories) {
            throw new RuntimeException(sprintf('Directory "%s" doesn\'t exist', dirname($target)),
                RuntimeException::INVALID_TARGET_DIRECTORY);
        }

        // If the target directory doesn't exist and could not be created
        if (!@is_dir(dirname($target)) && !mkdir(dirname($target), 0777, true)) {
            throw new RuntimeException(sprintf('Directory "%s" couldn\'t be created', dirname($target)),
                RuntimeException::TARGET_DIRECTORY_NOT_CREATED);
        }

        // If the target file already exists but should not be overwritten
        if (@file_exists($target) && !$overwrite) {
            throw new RuntimeException(sprintf('Target file "%s" already exists', $target),
                RuntimeException::TARGET_EXISTS);
        }

        // If the target file already exists but can't be overwritten
        if (@file_exists($target) && !@unlink($target)) {
            throw new RuntimeException(sprintf('Target file "%s" already exists and couldn\'t be overwritten', $target),
                RuntimeException::TARGET_NOT_OVERWRITTEN);
        }

        return @file_put_contents($target, strval($this));
    }

    /**
     * Return the MIME type
     *
     * @return string           MIME type
     */
    public function getMimeType()
    {
        return $this->_mimeType;
    }

    /**
     * Set the MIME type
     *
     * @param string $mimeType MIME type
     * @return File             Self reference
     */
    public function setMimeType($mimeType)
    {
        $this->_mimeType = $mimeType;
        return $this;
    }

    /**
     * Return the owner file
     *
     * @return FileInterface    Owner file
     */
    public function getOwnerFile()
    {
        return $this;
    }

    /**
     * Set the owner file
     *
     * @param FileInterface $ownerFile Owner file
     * @return File                         Self reference
     */
    public function setOwnerFile(FileInterface $ownerFile)
    {
        // Do nothing
    }

    /**
     * Return the parent part
     *
     * @return PartInterface                Parent part
     */
    public function getParentPart()
    {
        return null;
    }

    /**
     * Set the parent part
     *
     * @param PartInterface $part Parent part
     * @return File                         Self reference
     */
    public function setParentPart(PartInterface $part)
    {
        // Do nothing
    }
}