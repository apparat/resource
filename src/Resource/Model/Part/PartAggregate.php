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
        $this->_initializeOccurrences();
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

    /**
     * Initialize the occurrences
     *
     * @return void
     */
    protected function _initializeOccurrences()
    {
        for ($occurrence = 0; $occurrence < $this->_miniumOccurrences; ++$occurrence) {
            $this->_addOccurrence();
        }
    }

    /**
     * Add an occurrence
     *
     * @return void
     */
    abstract protected function _addOccurrence();
}