<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage Apparat\Resource\Domain
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

use Apparat\Resource\Domain\Model\Hydrator\HydratorInterface;

/**
 * Abstract base class for file parts
 *
 * @package     Apparat\Resource
 * @subpackage Apparat\Resource\Domain
 */
abstract class AbstractPart implements PartInterface
{
	/**
	 * Associated hydrator
	 *
	 * @var HydratorInterface
	 */
	protected $_hydrator = null;

	/**
	 * Abstract part constructor
	 *
	 * @param HydratorInterface $hydrator Associated hydrator
	 */
	public function __construct(HydratorInterface $hydrator)
	{
		$this->_hydrator = $hydrator;
	}

	/**
	 * Return the associated hydrator
	 *
	 * @return HydratorInterface Associated hydrator
	 */
	public function getHydrator()
	{
		return $this->_hydrator;
	}

	/**
	 * Delegate a method call to a subpart
	 *
	 * @param string $method Method nae
	 * @param array $subparts Subpart identifiers
	 * @param array $arguments Method arguments
	 * @return mixed Method result
	 * @throws InvalidArgumentException If the method is unknown
	 */
	public function delegate($method, array $subparts, array $arguments)
	{
		// If the method is unknown
		if (!is_callable(array($this, $method))) {
			throw new InvalidArgumentException(sprintf('Unknown part method "%s"', $method),
				InvalidArgumentException::UNKNOWN_PART_METHOD);
		}

		// Call the method
		return call_user_func_array(array($this, $method), $arguments);
	}

	/**
	 * Validate a part identifier
	 *
	 * @param string $part Part identifier
	 * @throws InvalidArgumentException If the part identifier is not valid
	 */
	public static function validatePartIdentifier($part)
	{
		$part = strval($part);
		if (!preg_match("%^[a-z0-9\_\*]+$%i", $part)) {
			throw new InvalidArgumentException(sprintf('Invalid part path identifier "%s"', $part),
				InvalidArgumentException::INVALID_PART_IDENTIFIER);
		}
	}
}