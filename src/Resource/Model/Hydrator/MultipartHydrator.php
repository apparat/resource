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

use Apparat\Resource\Model\Part\AbstractPart;
use Apparat\Resource\Model\Part\InvalidArgumentException as PartInvalidArgumentException;
use Apparat\Resource\Model\Part\PartAggregate;

/**
 * Multipart hydrator
 *
 * @package Apparat\Resource\Model\Hydrator
 */
abstract class MultipartHydrator extends AbstractHydrator
{
    /**
     * Subhydrators
     *
     * @var array
     */
    protected $_subhydrators = array();
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
     * Part aggregate class name
     *
     * @var string
     */
    protected $_aggregateClass = null;

    /**
     * Multipart hydrator constructor
     *
     * @param array $subhydrators Subpart hydrators
     * @param int $minOccurrences Minimum occurrences
     * @param int $maxOccurrences Maximum occurences
     */
    public function __construct(array $subhydrators, $minOccurrences = 1, $maxOccurrences = 1)
    {
        parent::__construct(Hydrator::STANDARD);

        // Run through all subhydrators
        foreach ($subhydrators as $part => $subhydrator) {

            // Validate the hydrator name
            AbstractPart::validatePartIdentifier($part);

            // If the subhydrator needs to be instancianted from a string or array
            if (!($subhydrator instanceof Hydrator)) {
                $subhydrator = HydratorFactory::build(is_array($subhydrator) ? $subhydrator : [[$part => $subhydrator]]);
            }

            $this->_subhydrators[$part] = $subhydrator;
        }

        // Validate the occurrence numbers
        self::validateParameters($minOccurrences, $maxOccurrences);
        $this->_miniumOccurrences = intval($minOccurrences);
        $this->_maximumOccurrences = intval($maxOccurrences);
    }

    /**
     * Initialize the aggregate part
     *
     * @param string $data Part data
     * @return PartAggregate Part aggregage
     */
    public function hydrate($data)
    {
        // If the part aggregate class isn't valid
        if (!$this->_aggregateClass || !class_exists($this->_aggregateClass) || !is_subclass_of($this->_aggregateClass,
                PartAggregate::class)
        ) {
            throw new RuntimeException(sprintf('Invalid part aggregate class "%s"', $this->_aggregateClass),
                RuntimeException::INVALID_PART_AGGREGATE_CLASS);
        }

        return new $this->_aggregateClass($this->_subhydrators, $this->_miniumOccurrences, $this->_maximumOccurrences);
    }

    /**
     * Get a subhydrator by name
     *
     * @param array $path Subhydrator path
     * @return Hydrator Subhydrator
     * @throws PartInvalidArgumentException If the subhydrator path is empty
     * @throws PartInvalidArgumentException If the subhydrator path is unknown
     */
    public function getSub(array $path)
    {

        // If the path part is empty: Return this hydrator
        if (!count($path)) {
            return $this;
//            throw new PartInvalidArgumentException('Empty part identifier',
//                PartInvalidArgumentException::EMPTY_PART_IDENTIFIER);
        }

        // Retrieve the subhydrator
        $subhydratorName = array_shift($path);
        if (!array_key_exists($subhydratorName, $this->_subhydrators)) {
            throw new PartInvalidArgumentException('Unknown part identifier "%s"',
                PartInvalidArgumentException::UNKOWN_PART_IDENTIFIER);
        }

        /** @var Hydrator $subhydrator */
        $subhydrator =& $this->_subhydrators[$subhydratorName];
        return count($path) ? $subhydrator->getSub($path) : $subhydrator;
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
    static function validateParameters(...$parameters)
    {

        // If the number of parameters isn't exactly 2
        if (count($parameters) != 2) {
            throw new InvalidArgumentException(sprintf('Invalid multipart hydrator parameter count (%s)',
                count($parameters)), InvalidArgumentException::INVALID_MULTIPART_HYDRATOR_PARAMETER_COUNT);
        }

        // Validate the occurrence numbers
        PartAggregate::validateOccurrences($parameters[0], $parameters[1]);

        return true;
    }
}