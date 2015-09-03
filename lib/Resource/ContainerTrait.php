<?php

/**
 * bauwerk-resource
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

use Bauwerk\Resource\Container\OutOfBoundsException;
use Bauwerk\Resource\Container\OutOfRangeException;
use Bauwerk\Resource\Container\InvalidArgumentException;

/**
 * Container trait
 *
 * @package Bauwerk\Resource
 */
trait ContainerTrait
{
    /**
     * File parts
     *
     * @var array
     */
    protected $_parts = null;
    /**
     * Current part index
     *
     * @var int
     */
    protected $_partPosition = 0;
    /**
     * Container content model
     *
     * @var array
     */
    protected $_contentModel = array(
        ContainerInterface::TYPE => ContainerInterface::TYPE_SEQUENCE,
        ContainerInterface::MIN => 1,
        ContainerInterface::MAX => 1,
        ContainerInterface::CLASSES => ['Bauwerk\\Resource\\Part'],
    );

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Return a file part
     *
     * @param string $key Part key
     * @return PartInterface                    Part
     * @throws OutOfRangeException             If an invalid part is requested
     * @throws OutOfRangeException             If the requested part key is empty
     */
    public function getPart($key)
    {

        // Read the file contents once
        $this->_readSource(); // TODO: Not part of this class / trait!

        // If the requested part key is not valid
        if (!$this->_isValidPartKey($key)) {
            throw new OutOfRangeException(sprintf('Invalid file part key "%s"', $key),
                OutOfRangeException::INVALID_PART_KEY);

            // Else: If the requested part key is not set
        } elseif (!isset($this->_parts[$key])) {
            throw new OutOfRangeException(sprintf('File part key "%s" is empty', $key),
                OutOfRangeException::PART_KEY_EMPTY);
        }

        return $this->_parts[$key];
    }

    /**
     * Set a file part
     *
     * @param string $key Part key
     * @param PartInterface $part Part
     * @return File                             Self reference
     * @throws OutOfRangeException                If an invalid part is requested
     */
    public function setPart($key, PartInterface $part)
    {
        if ($this->_parts === null) {
            $this->_parts = array();
        }

        // If the requested part key is not valid
        if (!$this->_isValidPartKey($key)) {
            throw new OutOfRangeException(sprintf('Invalid file part key "%s"', $key),
                OutOfRangeException::INVALID_PART_KEY);
        }

        // If the default part is set and doesn't match the default part class
        if (($key == Part::DEFAULT_NAME) && !($part instanceof $this->_defaultPartClass)) {
            throw new InvalidArgumentException(sprintf('Invalid default part class "%s"', get_class($part)),
                InvalidArgumentException::INVALID_DEFAULT_PART_CLASS);
        }

        $this->_parts[$key] = $part;
        return $this;
    }

    /**
     * Return the current file part
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return PartInterface        Current file part
     * @since 5.0.0
     */
    public function current()
    {
        return $this->_parts[$this->_partAtPosition($this->_partPosition)];
    }

    /**
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void
     * @since 5.0.0
     */
    public function next()
    {
        ++$this->_partPosition;
    }

    /**
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed                    Scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        try {
            return $this->_partAtPosition($this->_partPosition);
        } catch (\OutOfBoundsException $e) {
            return null;
        }
    }

    /**
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean                  Current item is valid
     * @since 5.0.0
     */
    public function valid()
    {
        try {
            return isset($this->_parts[$this->_partAtPosition($this->_partPosition)]);
        } catch (OutOfBoundsException $e) {
            return false;
        }
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->_partPosition = 0;
    }

    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param string $offset An offset to check for
     * @return boolean              TRUE on success or false on failure.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {

        // Read the file contents once
        $this->_readSource();

        return isset($this->_parts[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param string $offset The offset to retrieve
     * @return mixed                Element at offset
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {

        // Read the file contents once
        $this->_readSource();

        return isset($this->_parts[$offset]) ? $this->_parts[$offset] : null;
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset The offset to assign the value to.
     * @param PartInterface $value The value to set
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {

        // Read the file contents once
        $this->_readSource();

        if ($offset !== null) {
            $this->_parts[$offset] = $value;
        }
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset The offset to unset
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {

        // Read the file contents once
        $this->_readSource();

        unset($this->_parts[$offset]);
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int                      The custom count as an integer.
     * @since 5.1.0
     */
    public function count()
    {

        // Read the file contents once
        $this->_readSource();

        return count($this->_parts);
    }

    /**
     * Seeks to a position
     *
     * @link http://php.net/manual/en/seekableiterator.seek.php
     * @param int $position The position to seek to
     * @return void
     * @since 5.1.0
     * @throws OutOfBoundsException     If the requested position doesn't exist
     */
    public function seek($position)
    {

        // If the requested position doesn't exist
        if (!isset($this->_parts[$this->_partAtPosition($position)])) {
            throw new OutOfBoundsException(sprintf('Invalid seek position (%s)', $position));
        }

        $this->_partPosition = $position;
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Return the part key at a particular position
     *
     * @param int $position Position
     * @return string                   Part key
     * @throws OutOfBoundsException     If the requested position doesn't exist
     */
    protected function _partAtPosition($position)
    {

        // Read the file contents once
        $this->_readSource();

        $partKeys = array_keys($this->_parts);
        if (!isset($partKeys[$position])) {
            throw new OutOfBoundsException(sprintf('Invalid part key position (%s)', $position),
                OutOfBoundsException::INVALID_PART_KEY_POSITION);
        }
        return $partKeys[$position];
    }

    /**
     * Test if a valid part key is given
     *
     * @param string $key Part key
     * @return bool                        Part key is valid
     */
    protected function _isValidPartKey($key)
    {
        return $key == Part::DEFAULT_NAME;
    }
}