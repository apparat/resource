<?php

/**
 * Bauwerk
 *
 * @category    Jkphl
 * @package     Jkphl_Bauwerk
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

namespace Bauwerk\Resource;

use \Bauwerk\Resource;
use \Bauwerk\Resource\File\InvalidArgumentException;
use \Bauwerk\Resource\File\RuntimeException;

/**
 * Base class for file resources
 *
 * @package Bauwerk\Resource
 */
class File extends Resource implements FileInterface
{
    /**
     * Use container properties & methods
     */
    use ContainerTrait;

    /**
     * File source
     *
     * @var string
     */
    protected $_source = null;

    /**
     * Default part class
     *
     * @var string
     */
    protected $_defaultPartClass = 'Bauwerk\\Resource\\Part';

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Constructor
     *
     * @param string $source Source
     */
    public function __construct($source = null)
    {
        $this->setSource($source);
    }

    /**
     * Serialize this file
     *
     * @return string                   Serialized file contents
     */
    public function __toString()
    {
        try {
            return strval($this->getContent());
        } catch (FileExceptionInterface $e) {
            return '';
        }
    }

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
     * @param string $source Source
     * @return File                 Self reference
     */
    public function setSource($source)
    {
        $this->_source = trim($source);
        $this->_parts = null;
        return $this;
    }

    /**
     * Return the file content
     *
     * @return string                           Content
     */
    public function getContent()
    {
        return strval($this->getPart(Part::DEFAULT_NAME));
    }

    /**
     * Set the file content
     *
     * @param string $content Content
     * @return File                             Self reference
     */
    public function setContent($content)
    {
        return $this->setPart(Part::DEFAULT_NAME, new $this->_defaultPartClass($content));
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

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Read the source file
     *
     * @return boolean                          Success
     */
    protected function _readSource()
    {

        // Read the file parts only once
        if ($this->_parts === null) {

            // If the source file is valid
            if ($this->_source) {
                if ($this->_validateSource()) {
                    $this->_parts = array(Part::DEFAULT_NAME => new $this->_defaultPartClass(@file_get_contents($this->_source)));
                }
            } else {
                $this->_parts = array(Part::DEFAULT_NAME => new $this->_defaultPartClass());
            }
        }
    }

    /**
     * Validate the source file
     *
     * @return boolean                         TRUE if the source file exists and is readable
     * @throws InvalidArgumentException        If no source file was given
     * @throws InvalidArgumentException        If the source file doesn't exits
     * @throws InvalidArgumentException        If the source file isn't readable
     */
    protected function _validateSource()
    {

        // If no source file is set
        if (!$this->_source) {
            throw new InvalidArgumentException('No source file given', InvalidArgumentException::NO_SOURCE_FILE);
        }

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

        return true;
    }

    /**
     * Return the content model of this container
     *
     * @return Container\Sequence          Container model
     */
    public function getContentModel()
    {
        return new Container\Sequence(array('\\Bauwerk\\Resource\\Part'), 1, 1);
    }
}