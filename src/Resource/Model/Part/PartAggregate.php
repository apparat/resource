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
abstract class PartAggregate extends AbstractPart
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
    protected $_occurrence = 0;

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
     * Return the parts content
     *
     * @param array $subparts Subpart path identifiers
     * @return ContentPart Self reference
     * @throws InvalidArgumentException If there are subpart identifiers given
     */
    public function get(array $subparts)
    {
//        print_r($subparts);

        return $this;
    }

    /**
     * Assign data to a particular part
     *
     * @param string $part Part identifier
     * @param string $data Part data
     * @param null|int $occurrence Occurrence to assign the part data to
     */
    abstract public function assign($part, $data, $occurrence = null);

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
    protected function _prepareAssignment($part, $occurrence = null) {

        // If the part identifier is invalid
        if (!strlen($part) || !array_key_exists($part, $this->_template)) {
            throw new InvalidArgumentException(sprintf('Invalid part identifier "%s"', $part),
                InvalidArgumentException::INVALID_PART_IDENTIFIER);
        }

        // Use the current occurrence if not specified
        if ($occurrence === null) {
            $occurrence = $this->_occurrence;
        }

        // Initialize the required number or occurrences
        $this->_initializeOccurrences($occurrence + 1);

        return $occurrence;
    }
}