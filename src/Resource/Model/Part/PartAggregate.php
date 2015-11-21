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

namespace Apparat\Resource\Model\Part;

/**
 * Abstract part aggregate
 *
 * @package Apparat\Resource\Model\Part
 */
abstract class PartAggregate extends AbstractPart implements \Countable, \Iterator
{
    /**
     * Subpart template
     *
     * @var array
     */
    protected $_template = array();
    /**
     * Minimum occurrences
     *
     * @var int
     */
    protected $_miniumOccurrences = 1;
    /**
     * Maximum occurrences
     *
     * @var int
     */
    protected $_maximumOccurrences = 1;
    /**
     * Occurrences
     *
     * @var array
     */
    protected $_occurrences = [];
    /**
     * Current occurrence index
     *
     * @var int
     */
    protected $_occurrenceCurrent = 0;
    /**
     * Current occurrence iterator
     *
     * @var int
     */
    protected $_occurrenceIterator = 0;

    /**
     * Unbound occurrences
     *
     * @var int
     */
    const UNBOUND = -1;

    /**
     * Part constructor
     *
     * @param array $template Subpart template
     * @param array|int $minOccurrences Minimum occurrences
     * @param int $maxOccurrences Maximum occurences
     */
    public function __construct(array $template, $minOccurrences = 1, $maxOccurrences = 1)
    {
        self::validateOccurrences($minOccurrences, $maxOccurrences);
        $this->_template = $template;
        $this->_miniumOccurrences = intval($minOccurrences);
        $this->_maximumOccurrences = intval($maxOccurrences);

        // Initialize the occurrences
        $this->_initializeOccurrences($this->_miniumOccurrences);
    }

    /**
     * Return the mime type of this part
     *
     * @param array $subparts Subpart path identifiers
     * @return string   MIME type
     */
    public function getMimeType(array $subparts)
    {
        return null;
    }

    /**
     * Return a nested subpart (or the part itself)
     *
     * @param array $subparts Subpart path identifiers
     * @return Part Nested subpart (or the part itself)
     * @throws InvalidArgumentException If there are too few subpart identifiers given
     * @throws InvalidArgumentException If the occurrence index is invalid
     * @throws OutOfBoundsException If the occurrence index is out of bounds
     */
    public function get(array $subparts)
    {
        // If a subpart is requested
        if (count($subparts)) {

            // Check if there are at least 2 subpart path identifiers available
            if (count($subparts) < 2) {
                throw new InvalidArgumentException(sprintf('Too few subpart identifiers ("%s")',
                    implode('/', $subparts)),
                    InvalidArgumentException::TOO_FEW_SUBPART_IDENTIFIERS);
            }

            // Validate the occurrence index
            $occurrence = array_shift($subparts);
            if ((strval(intval($occurrence)) != $occurrence)) {
                throw new InvalidArgumentException(sprintf('Invalid occurrence index "%s"', $occurrence),
                    InvalidArgumentException::INVALID_OCCURRENCE_INDEX);
            }

            // Test if the occurrence index is within bounds
            if ((intval($occurrence) < 0) || ($occurrence >= count($this->_occurrences))) {
                throw new OutOfBoundsException(sprintf('Occurrence index "%s" out of bounds', $occurrence),
                    OutOfBoundsException::OCCURRENCE_INDEX_OUT_OF_BOUNDS);
            }

            // Validate the part identifier
            $part = array_shift($subparts);
            self::validatePartIdentifier($part);

            // Test if the part identifier is known
            if (!array_key_exists($part, $this->_occurrences[$occurrence])) {
                throw new InvalidArgumentException(sprintf('Unknown part identifier "%s"', $part),
                    InvalidArgumentException::UNKOWN_PART_IDENTIFIER);
            }

            // If the part is empty
            if (!($this->_occurrences[$occurrence][$part] instanceof Part)) {
                throw new InvalidArgumentException(sprintf('Part "%s" does not exist', $occurrence.'/'.$part),
                    InvalidArgumentException::PART_DOES_NOT_EXIST);
            }

	        /** @var Part $subpart */
	        $subpart = $this->_occurrences[$occurrence][$part];
            return $subpart->get($subparts);

            // Else: return this
        } else {
            return $this;
        }
    }

    /**
     * Assign data to a particular part
     *
     * @param string $part Part identifier
     * @param string $data Part data
     * @param null|int $occurrence Occurrence to assign the part data to
     */
    abstract public function assign($part, $data, $occurrence = null);

    /**
     * Return the number of occurrences
     *
     * @return int Number of occurrences
     */
    public function count()
    {
        return count($this->_occurrences);
    }

    /**
     * Return the current occurrence
     *
     * @return array Current occurrence
     */
    public function current()
    {
        return $this->_occurrences[$this->_occurrenceIterator];
    }

    /**
     * Increment the internal occurrence iterator
     *
     * @return void
     */
    public function next()
    {
        ++$this->_occurrenceIterator;
    }

    /**
     * Return the internal occurrence iterator
     *
     * @return int Internal occurrence iterator
     */
    public function key()
    {
        return $this->_occurrenceIterator;
    }

    /**
     * Test if the current occurrence is valid
     *
     * @return boolean The current occurrence is valid
     */
    public function valid()
    {
        return isset($this->_occurrences[$this->_occurrenceIterator]);
    }

    /**
     * Reset the internal occurrence iterator
     *
     * @return void
     */
    public function rewind()
    {
        $this->_occurrenceIterator = 0;
    }

    /*******************************************************************************
     * STATIC METHODS
     *******************************************************************************/

    /**
     * Validate minimum / maximum occurrence numbers
     *
     * @param int $minOccurrences Minimum occurrences
     * @param int $maxOccurrences Maximum occurrences
     * @return void
     * @throws InvalidArgumentException If the minimum occurrences are less than 1
     * @throws InvalidArgumentException If the maximum occurrences are not unbound and less than the minimum occurrences
     */
    public static function validateOccurrences($minOccurrences, $maxOccurrences)
    {
        // Minimum occurrences
        $minOccurrences = intval($minOccurrences);
        if ($minOccurrences < 1) {
            throw new InvalidArgumentException(sprintf('Invalid part aggregate minimum occurrences "%s"',
                $minOccurrences), InvalidArgumentException::INVALID_MINIMUM_OCCURRENCES);
        }

        // Maximum occurrences
        $maxOccurrences = intval($maxOccurrences);
        if (($maxOccurrences < $minOccurrences) && ($maxOccurrences != self::UNBOUND)) {
            throw new InvalidArgumentException(sprintf('Invalid part aggregate maximum occurrences "%s"',
                $maxOccurrences), InvalidArgumentException::INVALID_MAXIMUM_OCCURRENCES);
        }
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Initialize a particular number of occurrences
     *
     * @param int $occurrences Occurrences number
     * @throws OutOfBoundsException If an invalid number of occurrences is specified
     */
    protected function _initializeOccurrences($occurrences)
    {
        // If the occurrences number is invalid
        if (($occurrences < $this->_miniumOccurrences) || (($this->_maximumOccurrences != self::UNBOUND) && ($occurrences > $this->_maximumOccurrences))) {
            throw new OutOfBoundsException(sprintf('Invalid occurrences number "%s"', $occurrences),
                OutOfBoundsException::INVALID_OCCURRENCES_NUMBER);
        }

        // Initialize the particular number of occurrences
        for ($occurrence = count($this->_occurrences); $occurrence < $occurrences; ++$occurrence) {
            $this->_addOccurrence();
        }
    }

    /**
     * Add an occurrence
     *
     * @return void
     */
    abstract protected function _addOccurrence();

    /**
     * Prepare a part assignment
     *
     * @param string $part Part identifier
     * @param null|int $occurrence Occurrence to assign the part data to
     * @return int Occurrence index
     * @throws InvalidArgumentException If the part identifier is invalid
     */
    protected function _prepareAssignment($part, $occurrence = null)
    {

        // If the part identifier is invalid
        if (!strlen($part) || !array_key_exists($part, $this->_template)) {
            throw new InvalidArgumentException(sprintf('Invalid part identifier "%s"', $part),
                InvalidArgumentException::INVALID_PART_IDENTIFIER);
        }

        // Use the current occurrence if not specified
        if ($occurrence === null) {
            $occurrence = $this->_occurrenceCurrent;
        }

        // Initialize the required number or occurrences
        $this->_initializeOccurrences($occurrence + 1);

        return $occurrence;
    }
}