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

namespace Bauwerk\Resource\File\Part;

use Bauwerk\Resource\File\PartInterface;
use Bauwerk\Resource\File\Part\Container\Exception\OutOfBounds;
use Bauwerk\Resource\File\Part\Container\Exception\OutOfRange;
use Bauwerk\Resource\File\Part\Container\Exception\InvalidArgument;
use Bauwerk\Resource\File\Part\Container\Exception\Runtime;
use Bauwerk\Resource\File\Part;

/**
 * Container file part trait
 *
 * @package Bauwerk\Resource
 */
trait ContainerTrait
{
    /**
     * Part model of this container
     *
     * @var array
     */
    protected $_partModel = null;
    /**
     * File parts
     *
     * @var array
     */
    protected $_parts = array();
    /**
     * Current part index
     *
     * @var int
     */
    protected $_partPosition = 0;
    /**
     * Minimum occurences
     *
     * @var int
     */
    protected $_minOccurs = 1;
    /**
     * Maximum occurences
     *
     * @var int
     */
    protected $_maxOccurs = 1;

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Return a file part
     *
     * @param string $key Part key
     * @return PartInterface                    Part
     * @throws OutOfRange                       If an invalid part is requested
     * @throws OutOfRange                       If the requested part key is empty
     */
    public function getPart($key)
    {

        // Verify the container's part model
        $this->_verifyPartModel();

        // TODO: Parts must be created on the fly according to the part model

        // If the requested part key is not valid
        if (!$this->_isValidPartKey($key)) {
            throw new OutOfRange(sprintf('Invalid file part key "%s"', $key),
                OutOfRange::INVALID_PART_KEY);

        // Else: If the requested part key is not set
        } elseif (!isset($this->_parts[$key])) {
            throw new OutOfRange(sprintf('File part key "%s" is empty', $key),
                OutOfRange::PART_KEY_EMPTY);
        }

        return $this->_parts[$key];
    }

    /**
     * Set a file part
     *
     * @param string $key Part key
     * @param PartInterface $part Part
     * @return File                             Self reference
     * @throws OutOfRange                       If an invalid part is requested
     * @todo Implement in accordance to the part model
     */
    public function setPart($key, PartInterface $part)
    {
        // Verify the container's part model
        $this->_verifyPartModel();

        // If the requested part key is not valid
        if (!$this->_isValidPartKey($key)) {
            throw new OutOfRange(sprintf('Invalid file part key "%s"', $key),
                OutOfRange::INVALID_PART_KEY);
        }

        // If the default part is set and doesn't match the default part class
        if (($key == Part::DEFAULT_NAME) && !($part instanceof $this->_defaultPartClass)) {
            throw new InvalidArgument(sprintf('Invalid default part class "%s"', get_class($part)),
                InvalidArgument::INVALID_DEFAULT_PART_CLASS);
        }

        $this->_parts[$key] = $part;
        return $this;
    }

    /**
     * Return the part contents as string
     *
     * @return string           Part contents
     */
    public function __toString()
    {
        return implode('', array_map('strval', $this->_parts));
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
        } catch (OutOfBounds $e) {
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
        } catch (OutOfBounds $e) {
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

        // Verify the container's part model
        $this->_verifyPartModel();

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

        // Verify the container's part model
        $this->_verifyPartModel();

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

        // Verify the container's part model
        $this->_verifyPartModel();

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

        // Verify the container's part model
        $this->_verifyPartModel();

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

        // Verify the container's part model
        $this->_verifyPartModel();

        return count($this->_parts);
    }

    /**
     * Seeks to a position
     *
     * @link http://php.net/manual/en/seekableiterator.seek.php
     * @param int $position The position to seek to
     * @return void
     * @since 5.1.0
     * @throws OutOfBounds              If the requested position doesn't exist
     */
    public function seek($position)
    {

        // Verify the container's part model
        $this->_verifyPartModel();

        // If the requested position doesn't exist
        if (!isset($this->_parts[$this->_partAtPosition($position)])) {
            throw new OutOfBounds(sprintf('Invalid seek position (%s)', $position));
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
     * @throws OutOfBounds              If the requested position doesn't exist
     */
    protected function _partAtPosition($position)
    {

        // Verify the container's part model
        $this->_verifyPartModel();

        $partKeys = array_keys($this->_parts);
        if (!isset($partKeys[$position])) {
            throw new OutOfBounds(sprintf('Invalid part key position (%s)', $position),
                OutOfBounds::INVALID_PART_KEY_POSITION);
        }
        return $partKeys[$position];
    }

    /**
     * Test if a valid part key is given
     *
     * @param string $key Part key
     * @return bool                        Part key is valid
     * @todo Must represent the part model!
     */
    protected function _isValidPartKey($key)
    {

        // TODO: Needs to be checked against the part model

        return $key == Part::DEFAULT_NAME;
    }

    /**
     * Set the part model of this container
     *
     * @param array $partModel Container part model
     * @param int $minOccurs                Minimum occurences
     * @param int $maxOccurs                Maximum occurences
     * @throws InvalidArgument              If the class names list is not valid
     * @throws InvalidArgument              If the minimum / maximum occurrences are invalid
     */
    protected function _setPartModel(array $partModel, $minOccurs = 1, $maxOccurs = 1)
    {

        // Run through all part model classes and verify their validity
        array_filter($partModel, function ($partClass) {
            return $partClass instanceof Part;
        });

        // If there's no valid part class
        if (!count($partModel)) {
            throw new InvalidArgument('Invalid class names array for container part model',
                InvalidArgument::INVALID_CLASSNAMES_ARRAY);
        }

        $this->_partModel = $partModel;

        // Verify and set the minimum occurences
        if (!is_int($minOccurs) || ($minOccurs < 0)) {
            throw new InvalidArgument(sprintf('Invalid minimum occurences value "%s"', $minOccurs),
                InvalidArgument::INVALID_MINIMUM_OCCURENCES);
        }
        $this->_minOccurs = intval($minOccurs);

        // Verify and set the maximum occurences
        if (!is_int($maxOccurs) || ($maxOccurs < -1)) {
            throw new InvalidArgument(sprintf('Invalid maximum occurences value "%s"', $maxOccurs),
                InvalidArgument::INVALID_MAXIMUM_OCCURENCES);
        }
        $this->_maxOccurs = intval($maxOccurs);
    }

    /**
     * Verify that this container has a valid part model
     *
     * @throw Runtime                       If this container's part model isn't valid
     */
    protected function _verifyPartModel()
    {
        if (!is_array($this->_partModel) || !count($this->_partModel)) {
            throw new Runtime('Invalid part model', Runtime::INVALID_PART_MODEL);
        }
    }
}