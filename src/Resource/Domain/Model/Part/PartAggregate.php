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

namespace Apparat\Resource\Domain\Model\Part;

use Apparat\Resource\Domain\Model\Hydrator\MultipartHydrator;

/**
 * Abstract part aggregate
 *
 * @package Apparat\Resource\Domain\Model\Part
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
	protected $_minimumOccurrences = 1;
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
	 * @param int $maxOccurrences Maximum occurrences
	 * @param MultipartHydrator $hydrator
	 */
	public function __construct(array $template, $minOccurrences = 1, $maxOccurrences = 1, MultipartHydrator $hydrator)
	{
		self::validateOccurrences($minOccurrences, $maxOccurrences);
		$this->_template = $template;
		$this->_minimumOccurrences = intval($minOccurrences);
		$this->_maximumOccurrences = intval($maxOccurrences);

		parent::__construct($hydrator);

		// Initialize the occurrences
		$this->_initializeOccurrences($this->_minimumOccurrences);
	}

	/**
	 * Serialize this file part
	 *
	 * @return string   File part content
	 */
	public function __toString()
	{
		return $this->_hydrator->dehydrate($this);
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
	 * Return a nested subpart (or the part itself)
	 *
	 * @param array $subparts Subpart path identifiers
	 * @param int $occurrence Effective occurrence index
	 * @param string $part Effective part identifier
	 * @return Part Nested subpart (or the part itself)
	 * @throws InvalidArgumentException If there are too few subpart identifiers given
	 * @throws InvalidArgumentException If the occurrence index is invalid
	 * @throws OutOfBoundsException If the occurrence index is out of bounds
	 */
	public function get(array $subparts = array(), &$occurrence = 0, &$part = '')
	{
		// If a subpart is requested
		if (count($subparts)) {
			$subpart = $this->_getImmediateSubpart($subparts, $occurrence, $part);
			return $subpart->get($subparts);

			// Else: return this
		} else {
			return $this;
		}
	}

	/**
	 * Set the contents of a part
	 *
	 * @param mixed $data Contents
	 * @param array $subparts Subpart identifiers
	 * @return Part Modified part
	 */
	public function set($data, array $subparts = [])
	{
		// If there are subparts: Delegate
		if (count($subparts)) {
			$occurrence = 0;
			$part = '';
			$subpart = $this->get($subparts, $occurrence, $part)->set($data, []);
			$this->_occurrences[$occurrence][$part] = $subpart;
			return $this;

			// Else: Rehydrate
		} else {
			return $this->_hydrator->hydrate($data);
		}
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
			$subpart = $this->_getImmediateSubpart($subparts, $occurrence, $part);
			$result = $subpart->delegate($method, $subparts, $arguments);

			// If it's a setter method
			if (!strncmp('set', $method, 3)) {

				// Exchange the modified part
				$this->_occurrences[$occurrence][$part] = $result;

				// Return a self reference
				return $this;

				// Else: Return the method result
			} else {
				return $result;
			}
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
	 * Return an immediate subpart
	 *
	 * @param array $subparts Subpart path identifiers
	 * @param int $occurrence Effective occurrence index
	 * @param string $part Effective part identifier
	 * @return Part Immediate subpart
	 * @throws InvalidArgumentException If there are too few subpart identifiers
	 * @throws InvalidArgumentException If the occurrence index is invalid
	 * @throws OutOfBoundsException If the occurrence index is out of bounds
	 * @throws InvalidArgumentException If the subpart identifier is unknown
	 * @throws InvalidArgumentException If the subpart does not exist
	 */
	protected function _getImmediateSubpart(array &$subparts, &$occurrence = 0, &$part = '')
	{

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

		// If the occurrence index is out of bounds
		if ((intval($occurrence) < 0) || ($occurrence >= count($this->_occurrences))) {
			throw new OutOfBoundsException(sprintf('Occurrence index "%s" out of bounds', $occurrence),
				OutOfBoundsException::OCCURRENCE_INDEX_OUT_OF_BOUNDS);
		}

		// Validate the part identifier
		$part = array_shift($subparts);
		self::validatePartIdentifier($part);

		// Test if the part identifier is known
		if (!$this->_isKnownPartIdentifier($occurrence, $part)) {
			throw new InvalidArgumentException(sprintf('Unknown part identifier "%s"', $part),
				InvalidArgumentException::UNKNOWN_PART_IDENTIFIER);
		}

		// If the part is empty
		$partInstance = $this->_getOccurrencePart($occurrence, $part);
		if (!($partInstance instanceof Part)) {
			throw new InvalidArgumentException(sprintf('Part "%s" does not exist', $occurrence.'/'.$part),
				InvalidArgumentException::PART_DOES_NOT_EXIST);
		}

		return $partInstance;
	}

	/**
	 * Initialize a particular number of occurrences
	 *
	 * @param int $occurrences Occurrences number
	 * @throws OutOfBoundsException If an invalid number of occurrences is specified
	 */
	protected function _initializeOccurrences($occurrences)
	{
		// If the occurrences number is invalid
		if (($occurrences < $this->_minimumOccurrences) || (($this->_maximumOccurrences != self::UNBOUND) && ($occurrences > $this->_maximumOccurrences))) {
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

	/**
	 * Test if a particular part identifier is known for a particular occurrence
	 *
	 * @param int $occurrence Occurrence index
	 * @param string $part Part identifier
	 * @return bool Is known part identifier
	 */
	protected function _isKnownPartIdentifier($occurrence, $part)
	{
		return array_key_exists($part, $this->_occurrences[$occurrence]);
	}

	/**
	 * Return a particular part of a particular occurrence
	 *
	 * @param int $occurrence Occurrence index
	 * @param string $part Part identifier
	 * @return Part Part instance
	 */
	protected function _getOccurrencePart(&$occurrence, &$part)
	{
		return $this->_occurrences[$occurrence][$part];
	}
}