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
     * @implements ContainerInterface
     */

    /**
     * Part model of this container
     *
     * @var array
     */
    protected $_partModel = null;
    /**
     * Part sequence / choice occurrences
     *
     * @var array
     */
    protected $_occurrences = array();
    /**
     * Current occurrence index
     *
     * @var int
     */
    protected $_occurrencePosition = 0;
    /**
     * Current part index
     *
     * @var int
     */
    protected $_partPosition = 0;
    /**
     * Minimum occurrences
     *
     * @var int
     */
    protected $_minOccurs = 1;
    /**
     * Maximum occurrences
     *
     * @var int
     */
    protected $_maxOccurs = 1;

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Reset the file to its default state
     *
     * @return ContainerInterface               Self reference
     */
    public function reset()
    {
        $this->_partPosition = 0;
        $this->_occurrencePosition = 0;

        // Fill up the occurrences if necessary
        $this->_occurrences = array();
        $isSequence = ($this instanceof Part\Container\SequenceInterface);
        while (count($this->_occurrences) < $this->_minOccurs) {
            $this->_occurrences[] = $isSequence ? array_fill_keys(array_keys($this->_partModel),
                null) : array();
        }
    }

    /**
     * Return a container part
     *
     * @param string $key Part key
     * @param int $occurrence Part index (within the same key)
     * @return PartInterface                    Part
     * @throws OutOfBounds                      If an invalid part is requested
     * @throws OutOfRange                       If an invalid occurrence is requested
     */
    public function getPart($key, $occurrence = 0)
    {
        // Verify the container's part model
        $this->_verifyPartModel();

        // If the requested part key is not valid
        if (!$this->_isValidPartKey($key)) {
            throw new OutOfBounds(sprintf('Invalid file part key "%s"', $key),
                OutOfBounds::INVALID_PART_KEY);

            // If the requested occurrence is out of the valid range
        } elseif (($occurrence < 0) || (($this->_maxOccurs > ContainerInterface::UNBOUND) && ($occurrence >= $this->_maxOccurs))) {
            throw new OutOfRange(sprintf('Invalid occurrence index %s (%s to %s)', $occurrence, $this->_minOccurs,
                $this->_maxOccurs),
                OutOfBounds::INVALID_OCCURRENCE_INDEX);
        }

        // Fill up the occurrences if necessary
        $isSequence = ($this instanceof Part\Container\SequenceInterface);
        while (count($this->_occurrences) < $occurrence) {
            $this->_occurrences[] = $isSequence ? array_fill_keys(array_keys($this->_partModel),
                null) : array();
        }

        // Create the requested object if necessary
        if (empty($this->_occurrences[$occurrence][$key])) {
            /* @var PartInterface $part */
            $part = new $this->_partModel[$key]();
            /** @noinspection PhpParamsInspection */
            $part->setParentPart($this);
            $this->_occurrences[$occurrence][$key] = $part;
        }

        return $this->_occurrences[$occurrence][$key];
    }

    /**
     * Set a file part
     *
     * @param string $key Part key
     * @param PartInterface $part Part
     * @param int $occurrence Part index (within the same key)
     * @return ContainerInterface    Self reference
     * @throws InvalidArgument       If the part class is invalid
     */
    public function setPart($key, PartInterface $part, $occurrence = 0)
    {

        // Prepare and retrieve the requested file part
        $this->getPart($key, $occurrence);

        // Check if the part is of the right type
        if (!($part instanceof $this->_partModel[$key])) {
            throw new InvalidArgument(sprintf('Invalid part class "%s"', get_class($part)), InvalidArgument::INVALID_PART_CLASS);
        }

        // If no error occurred: Set the new file part
        $this->_occurrences[$occurrence][$key] = $part;

        return $this;
    }

    /**
     * Return the part contents as string
     *
     * @return string           Part contents
     */
    public function __toString()
    {
        $content = '';
        foreach ($this->_occurrences as $occurrence) {
            $content .= implode('', array_map('strval', $occurrence));
        }

        return $content;
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
        return $this->_occurrences[$this->_occurrencePosition][$this->_partAtPosition($this->_partPosition,
            $this->_occurrencePosition)];
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
        if (++$this->_partPosition >= count($this->_occurrences[$this->_occurrencePosition])) {
            $this->_partPosition = 0;
            ++$this->_occurrencePosition;
        }
    }

    /**
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed                    Array on success or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        try {
            return array(
                $this->_partAtPosition($this->_partPosition, $this->_occurrencePosition),
                $this->_occurrencePosition
            );
        } catch (OutOfRange $e) {
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
            $this->_partAtPosition($this->_partPosition, $this->_occurrencePosition);
            return true;
        } catch (OutOfRange $e) {
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
        $this->_occurrencePosition = 0;
        $this->_partPosition = 0;
    }

    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param array $offset Combination of occurrence and part key to check for
     * @return boolean              TRUE on success or false on failure.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {

        // Verify the container's part model
        $this->_verifyPartModel();

        if (is_array($offset) && (count($offset) == 2)) {
            $offset = array_values($offset);
            try {
                $this->_partAtPosition($offset[0], $offset[1]);
                return true;
            } catch (OutOfRange $e) {
            }
        }
        return false;
    }

    /**
     * Retrieve an offset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param string $offset Combination of occurrence and part key to retrieve
     * @return mixed                Element at offset
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {

        // Verify the container's part model
        $this->_verifyPartModel();

        if (is_array($offset) && (count($offset) == 2)) {
            $offset = array_values($offset);
            try {
                $this->_partAtPosition($offset[0], $offset[1]);
                return $this->_occurrences[$offset[1]][$offset[0]];
            } catch (OutOfRange $e) {
            }
        }

        return null;
    }

    /**
     * Set an offset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param array $offset Combination of occurrence and part key to set
     * @param PartInterface $value The part to set
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {

        // Verify the container's part model
        $this->_verifyPartModel();

        if (is_array($offset) && (count($offset) == 2)) {
            $offset = array_values($offset);
            try {
                $this->_partAtPosition($offset[0], $offset[1]);
                $this->_occurrences[$offset[1]][$offset[0]] = $value;
            } catch (OutOfRange $e) {
            }
        }
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset Combination of occurrence and part key to unset (reset)
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {

        // Verify the container's part model
        $this->_verifyPartModel();

        if (is_array($offset) && (count($offset) == 2)) {
            $offset = array_values($offset);
            try {
                $this->_partAtPosition($offset[0], $offset[1]);
                $isSequence = ($this instanceof Part\Container\SequenceInterface);
                if ($isSequence) {
                    $this->_occurrences[$offset[1]][$offset[0]] = null;
                } else {
                    $this->_occurrences[$offset[1]] = array();
                }
            } catch (OutOfRange $e) {
            }
        }
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

        return array_sum(array_map('count', $this->_occurrences));
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

        $outOfRange = true;
        foreach ($this->_occurrences as $occurrencePosition => $occurrence) {
            if (count($occurrence) > $position) {
                $this->_occurrencePosition = $occurrencePosition;
                $this->_partAtPosition = ($position - count($occurrence));
                $outOfRange = false;
                break;
            } else {
                $position -= count($occurrence);
            }
        }

        // If the requested position doesn't exist
        if ($outOfRange) {
            throw new OutOfBounds(sprintf('Invalid seek position (%s)', $position), OutOfBounds::INVALID_SEEK_POSITION);
        }
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Return the part key at a particular position
     *
     * @param int $position Part position
     * @param int $occurrence Occurence position
     * @return string                   Part key
     * @throws OutOfRange               If the requested occurrence doesn't exist
     * @throws OutOfRange               If the requested position doesn't exist
     */
    protected function _partAtPosition($position, $occurrence)
    {

        // Verify the container's part model
        $this->_verifyPartModel();

        // If a non-valid occurrence is requested
        if (($occurrence < 0) || ($occurrence >= count($this->_occurrences))) {
            throw new OutOfRange(sprintf('Invalid occurrence (%s)', $occurrence),
                OutOfRange::INVALID_OCCURRENCE_INDEX);
        }

        $partKeys = array_keys($this->_occurrences[$occurrence]);
        if (!isset($partKeys[$position])) {
            throw new OutOfRange(sprintf('Invalid part key position (%s)', $position),
                OutOfRange::INVALID_PART_KEY_POSITION);
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
        return array_key_exists($key, $this->_partModel);
    }

    /**
     * Set the part model of this container
     *
     * @param array $partModel Container part model
     * @param int $minOccurs Minimum occurrences
     * @param int $maxOccurs Maximum occurrences
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

        // Adopt the part model
        $this->_partModel = $partModel;

        // Verify and set the minimum occurrences
        if (!is_int($minOccurs) || ($minOccurs < 0)) {
            throw new InvalidArgument(sprintf('Invalid minimum occurrences value "%s"', $minOccurs),
                InvalidArgument::INVALID_MINIMUM_OCCURENCES);
        }
        $this->_minOccurs = intval($minOccurs);

        // Verify and set the maximum occurrences
        if (!is_int($maxOccurs) || ($maxOccurs < -1)) {
            throw new InvalidArgument(sprintf('Invalid maximum occurrences value "%s"', $maxOccurs),
                InvalidArgument::INVALID_MAXIMUM_OCCURENCES);
        }
        $this->_maxOccurs = intval($maxOccurs);

        // Reset the container
        $this->reset();
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