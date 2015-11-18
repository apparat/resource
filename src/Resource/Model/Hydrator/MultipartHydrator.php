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

namespace Apparat\Resource\Model\Hydrator;

use Apparat\Resource\Model\Part\PartAggregate;

/**
 * Multipart hydrator
 *
 * @package Apparat\Resource\Model\Hydrator
 */
abstract class MultipartHydrator extends AbstractHydrator
{
	/**
	 * Subpart aggregate occurences
	 *
	 * @var array
	 */
	protected $_occurrences = array();

	/**
	 * Multipart hydrator constructor
	 *
	 * @param array $subhydrators Subpart hydrators
	 * @param int $minOccurs Minimum occurrences
	 * @param int $maxOccurs Maximum occurences
	 * @throws InvalidArgumentException If a part path identifier is invalid
	 */
	public function __construct(array $subhydrators, $minOccurs = 1, $maxOccurs = 1)
	{
		parent::__construct(Hydrator::STANDARD);





		// TODO: Implement
		// InvalidArgumentException::INVALID_PART_IDENTIFIER
	}

	/**
	 * Get a subhydrator
	 *
	 * @param array $subparts Subpart path
	 * @return Hydrator Subhydrator
	 */
	public function getSub(array $subparts)
	{
		// TODO: Implement subpart getter
		die(__CLASS__.'->'.__METHOD__);
//		return $this;
	}

	/**
	 * Validate the parameters accepted by this hydrator
	 *
	 * By default, a multipart parameter accepts exactly two parameters:
	 * - the minimum number of occurrences of the contained part aggregate
	 * - the maximum number of occurrences of the contained part aggregate
	 *
	 * @param array $parameters Parameters
	 * @return boolean Parameters are valid
	 */
	static function validateParameters(...$parameters) {

		// If the number of parameters isn't exactly 2
		if (count($parameters) != 2) {
			throw new InvalidArgumentException(sprintf('Invalid multipart hydrator parameter count (%s)', count($parameters)), InvalidArgumentException::INVALID_MULTIPART_HYDRATOR_PARAMETER_COUNT);
		}

		// Validate the occurrence numbers
		PartAggregate::validateOccurrences($parameters[0], $parameters[1]);

		return true;
	}
}