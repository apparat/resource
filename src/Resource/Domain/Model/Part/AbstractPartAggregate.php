<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage Apparat\Resource\Domain
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

namespace Apparat\Resource\Domain\Model\Part;

use Apparat\Resource\Domain\Model\Hydrator\AbstractMultipartHydrator;

/**
 * Abstract part aggregate
 *
 * @package     Apparat\Resource
 * @subpackage Apparat\Resource\Domain
 */
abstract class AbstractPartAggregate extends AbstractPart implements PartAggregateInterface
{
    /**
     * Unbound occurrences
     *
     * @var int
     */
    const UNBOUND = -1;
    /**
     * Subpart template
     *
     * @var array
     */
    protected $template = array();
    /**
     * Minimum occurrences
     *
     * @var int
     */
    protected $minimumOccurrences = 1;
    /**
     * Maximum occurrences
     *
     * @var int
     */
    protected $maximumOccurrences = 1;
    /**
     * Occurrences
     *
     * @var array
     */
    protected $occurrences = [];
    /**
     * Current occurrence index
     *
     * @var int
     */
    protected $occurrenceCurrent = 0;
    /**
     * Current occurrence iterator
     *
     * @var int
     */
    protected $occurrenceIterator = 0;

    /**
     * Part constructor
     *
     * @param AbstractMultipartHydrator $hydrator
     * @param array $template Subpart template
     * @param array|int $minOccurrences Minimum occurrences
     * @param int $maxOccurrences Maximum occurrences
     */
    public function __construct(
        AbstractMultipartHydrator $hydrator,
        array $template,
        $minOccurrences = 1,
        $maxOccurrences = 1
    ) {
        self::validateOccurrences($minOccurrences, $maxOccurrences);
        $this->template = $template;
        $this->minimumOccurrences = intval($minOccurrences);
        $this->maximumOccurrences = intval($maxOccurrences);

        parent::__construct($hydrator);

        // Initialize the occurrences
        $this->initializeOccurrences($this->minimumOccurrences);
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
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid part aggregate minimum occurrences "%s"',
                    $minOccurrences
                ),
                InvalidArgumentException::INVALID_MINIMUM_OCCURRENCES
            );
        }

        // Maximum occurrences
        $maxOccurrences = intval($maxOccurrences);
        if (($maxOccurrences < $minOccurrences) && ($maxOccurrences != self::UNBOUND)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid part aggregate maximum occurrences "%s"',
                    $maxOccurrences
                ),
                InvalidArgumentException::INVALID_MAXIMUM_OCCURRENCES
            );
        }
    }

    /**
     * Initialize a particular number of occurrences
     *
     * @param int $occurrences Occurrences number
     * @throws OutOfBoundsException If an invalid number of occurrences is specified
     */
    protected function initializeOccurrences($occurrences)
    {
        // If the occurrences number is invalid
        if (($occurrences < $this->minimumOccurrences) ||
            (($this->maximumOccurrences != self::UNBOUND) && ($occurrences > $this->maximumOccurrences))
        ) {
            throw new OutOfBoundsException(
                sprintf('Invalid occurrences number "%s"', $occurrences),
                OutOfBoundsException::INVALID_OCCURRENCES_NUMBER
            );
        }

        // Initialize the particular number of occurrences
        for ($occurrence = count($this->occurrences); $occurrence < $occurrences; ++$occurrence) {
            $this->addOccurrence();
        }
    }

    /**
     * Add an occurrence
     *
     * @return void
     */
    abstract protected function addOccurrence();

    /**
     * Serialize this file part
     *
     * @return string   File part content
     */
    public function __toString()
    {
        return $this->hydrator->dehydrate($this);
    }

    /**
     * Return the mime type of this part
     *
     * @return string   MIME type
     */
    public function getMimeType()
    {
        return null;
    }

    /**
     * Set the contents of a part
     *
     * @param mixed $data Contents
     * @param array $subparts Subpart identifiers
     * @return PartInterface Modified part
     */
    public function set($data, array $subparts = [])
    {
        // If there are subparts: Delegate
        if (count($subparts)) {
            $occurrence = 0;
            $part = '';
            $subpart = $this->get($subparts, $occurrence, $part)->set($data, []);
            $this->occurrences[$occurrence][$part] = $subpart;
            return $this;
        }

        return $this->hydrator->hydrate($data);
    }

    /**
     * Return a nested subpart (or the part itself)
     *
     * @param array $subparts Subpart path identifiers
     * @param int $occurrence Effective occurrence index
     * @param string $part Effective part identifier
     * @return PartInterface Nested subpart (or the part itself)
     * @throws InvalidArgumentException If there are too few subpart identifiers given
     * @throws InvalidArgumentException If the occurrence index is invalid
     * @throws OutOfBoundsException If the occurrence index is out of bounds
     */
    public function get(array $subparts = array(), &$occurrence = 0, &$part = '')
    {
        // If a subpart is requested
        if (count($subparts)) {
            $subpart = $this->getImmediateSubpart($subparts, $occurrence, $part);
            return $subpart->get($subparts);
        }

        return $this;
    }

    /**
     * Return an immediate subpart
     *
     * @param array $subparts Subpart path identifiers
     * @param int $occurrence Effective occurrence index
     * @param string $part Effective part identifier
     * @return PartInterface Immediate subpart
     * @throws InvalidArgumentException If there are too few subpart identifiers
     * @throws InvalidArgumentException If the occurrence index is invalid
     * @throws OutOfBoundsException If the occurrence index is out of bounds
     * @throws InvalidArgumentException If the subpart identifier is unknown
     * @throws InvalidArgumentException If the subpart does not exist
     */
    protected function getImmediateSubpart(array &$subparts, &$occurrence = 0, &$part = '')
    {

        // Check if there are at least 2 subpart path identifiers available
        if (count($subparts) < 2) {
            throw new InvalidArgumentException(
                sprintf(
                    'Too few subpart identifiers ("%s")',
                    implode('/', $subparts)
                ),
                InvalidArgumentException::TOO_FEW_SUBPART_IDENTIFIERS
            );
        }

        // Validate the occurrence index
        $occurrence = array_shift($subparts);
        if ((strval(intval($occurrence)) != $occurrence)) {
            throw new InvalidArgumentException(
                sprintf('Invalid occurrence index "%s"', $occurrence),
                InvalidArgumentException::INVALID_OCCURRENCE_INDEX
            );
        }

        // If the occurrence index is out of bounds
        if ((intval($occurrence) < 0) || ($occurrence >= count($this->occurrences))) {
            throw new OutOfBoundsException(
                sprintf('Occurrence index "%s" out of bounds', $occurrence),
                OutOfBoundsException::OCCURRENCE_INDEX_OUT_OF_BOUNDS
            );
        }

        // Validate the part identifier
        $part = array_shift($subparts);
        self::validatePartIdentifier($part);

        // Test if the part identifier is known
        if (!$this->isKnownPartIdentifier($occurrence, $part)) {
            throw new InvalidArgumentException(
                sprintf('Unknown part identifier "%s"', $part),
                InvalidArgumentException::UNKNOWN_PART_IDENTIFIER
            );
        }

        // If the part is empty
        $partInstance = $this->getOccurrencePart($occurrence, $part);
        if (!($partInstance instanceof PartInterface)) {
            throw new InvalidArgumentException(
                sprintf('Part "%s" does not exist', $occurrence.'/'.$part),
                InvalidArgumentException::PART_DOES_NOT_EXIST
            );
        }

        return $partInstance;
    }

    /**
     * Test if a particular part identifier is known for a particular occurrence
     *
     * @param int $occurrence Occurrence index
     * @param string $part Part identifier
     * @return bool Is known part identifier
     */
    protected function isKnownPartIdentifier($occurrence, $part)
    {
        return array_key_exists($part, $this->occurrences[$occurrence]);
    }

    /**
     * Return a particular part of a particular occurrence
     *
     * @param int $occurrence Occurrence index
     * @param string $part Part identifier
     * @return PartInterface Part instance
     */
    protected function getOccurrencePart(&$occurrence, &$part)
    {
        return $this->occurrences[$occurrence][$part];
    }

    /**
     * Delegate a method call to a subpart
     *
     * @param string $method Method nae
     * @param array $subparts Subpart identifiers
     * @param array $arguments Method arguments
     * @return mixed Method result
     */
    public function delegate($method, array $subparts, array $arguments)
    {
        // If there are subpart identifiers: Delegate method call
        if (count($subparts)) {
            $occurrence = 0;
            $part = '';
            $subpart = $this->getImmediateSubpart($subparts, $occurrence, $part);
            $result = $subpart->delegate($method, $subparts, $arguments);

            // If it's a setter method
            if (!strncmp('set', $method, 3)) {
                // Exchange the modified part
                $this->occurrences[$occurrence][$part] = $result;

                // Return a self reference
                return $this;
            }

            // Return the method result
            return $result;
        }

        return parent::delegate($method, $subparts, $arguments);
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
     * Return the number of occurrences
     *
     * @return int Number of occurrences
     */
    public function count()
    {
        return count($this->occurrences);
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Return the current occurrence
     *
     * @return array Current occurrence
     */
    public function current()
    {
        return $this->occurrences[$this->occurrenceIterator];
    }

    /**
     * Increment the internal occurrence iterator
     *
     * @return void
     */
    public function next()
    {
        ++$this->occurrenceIterator;
    }

    /**
     * Return the internal occurrence iterator
     *
     * @return int Internal occurrence iterator
     */
    public function key()
    {
        return $this->occurrenceIterator;
    }

    /**
     * Test if the current occurrence is valid
     *
     * @return boolean The current occurrence is valid
     */
    public function valid()
    {
        return isset($this->occurrences[$this->occurrenceIterator]);
    }

    /**
     * Reset the internal occurrence iterator
     *
     * @return void
     */
    public function rewind()
    {
        $this->occurrenceIterator = 0;
    }

    /**
     * Prepare a part assignment
     *
     * @param string $part Part identifier
     * @param null|int $occurrence Occurrence to assign the part data to
     * @return int Occurrence index
     * @throws InvalidArgumentException If the part identifier is invalid
     */
    protected function prepareAssignment($part, $occurrence = null)
    {

        // If the part identifier is invalid
        if (!strlen($part) || !array_key_exists($part, $this->template)) {
            throw new InvalidArgumentException(
                sprintf('Invalid part identifier "%s"', $part),
                InvalidArgumentException::INVALID_PART_IDENTIFIER
            );
        }

        // Use the current occurrence if not specified
        if ($occurrence === null) {
            $occurrence = $this->occurrenceCurrent;
        }

        // Initialize the required number or occurrences
        $this->initializeOccurrences($occurrence + 1);

        return $occurrence;
    }
}
