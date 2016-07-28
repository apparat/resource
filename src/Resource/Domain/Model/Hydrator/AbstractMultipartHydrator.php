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

namespace Apparat\Resource\Domain\Model\Hydrator;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Resource\Domain\Factory\HydratorFactory;
use Apparat\Resource\Domain\Model\Part\AbstractPart;
use Apparat\Resource\Domain\Model\Part\AbstractPartAggregate;
use Apparat\Resource\Domain\Model\Part\PartAggregateInterface;
use Apparat\Resource\Domain\Model\Part\PartInterface;

/**
 * Multipart hydrator
 *
 * @package     Apparat\Resource
 * @subpackage Apparat\Resource\Domain
 */
abstract class AbstractMultipartHydrator extends AbstractHydrator
{
    /**
     * Subhydrators
     *
     * @var array
     */
    protected $subhydrators = array();
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
     * Part aggregate class name
     *
     * @var string
     */
    protected $aggregateClass = null;
    /**
     * Empty occurrence dehydration behaviour
     *
     * @var string
     */
    protected $occDhdrException = SkippedOccurrenceDehydrationException::class;

    /**
     * Multipart hydrator constructor
     *
     * @param array $subhydrators Subpart hydrators
     * @param int $minOccurrences Minimum occurrences
     * @param int $maxOccurrences Maximum occurrences
     */
    public function __construct(array $subhydrators, $minOccurrences = 1, $maxOccurrences = 1)
    {
        parent::__construct(HydratorInterface::STANDARD);

        // Run through all subhydrators
        foreach ($subhydrators as $part => $subhydrator) {
            // Validate the hydrator name
            AbstractPart::validatePartIdentifier($part);

            // If the subhydrator needs to be instantiated from a string or array
            if (!($subhydrator instanceof HydratorInterface)) {
                $subhydrator = HydratorFactory::build(
                    is_array($subhydrator) ? $subhydrator : [[$part => $subhydrator]]
                );
            }

            $this->subhydrators[$part] = $subhydrator;
        }

        // Validate the occurrence numbers
        self::validateParameters($minOccurrences, $maxOccurrences);
        $this->minimumOccurrences = intval($minOccurrences);
        $this->maximumOccurrences = intval($maxOccurrences);
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
    public static function validateParameters(...$parameters)
    {

        // If the number of parameters isn't exactly 2
        if (count($parameters) != 2) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid multipart hydrator parameter count (%s)',
                    count($parameters)
                ),
                InvalidArgumentException::INVALID_MULTIPART_HYDRATOR_PARAMETER_COUNT
            );
        }

        // Validate the occurrence numbers
        AbstractPartAggregate::validateOccurrences(intval($parameters[0]), intval($parameters[1]));

        return true;
    }

    /**
     * Serialize a file part
     *
     * @param PartInterface $part File part
     * @return string Serialized file part
     * @throws InvalidArgumentException If the part class cannot be dehydrated by this hydrator
     */
    public function dehydrate(PartInterface $part)
    {
        // Make sure it's a part aggregate that should be dehydrated
        if (!($part instanceof PartAggregateInterface)) {
            throw new InvalidArgumentException(
                sprintf('Invalid dehydration part class "%s"', get_class($part)),
                InvalidArgumentException::INVALID_DEHYDRATION_PART_CLASS
            );
        }

        $occurrences = [];

        // Run through all occurrences of the part
        foreach ($part as $occurrence) {
            $occurrence = $this->dehydrateOccurrence($occurrence);

            // If the occurrence is not a string
            if (!is_string($occurrence)) {
                throw new RuntimeException(
                    'Dehydrating an aggregate occurrence must return a string',
                    RuntimeException::OCCURRENCE_DEHYDRATION_MUST_RETURN_A_STRING
                );
            }

            $occurrences[] = $occurrence;
        }

        // Combine and return the dehydrated occurrences
        return $this->combineDehydratedOccurrences($occurrences);
    }

    /*******************************************************************************
     * STATIC METHODS
     *******************************************************************************/

    /**
     * Dehydrate a single occurrence
     *
     * @param array $occurrence Occurrence
     * @return string Dehydrated occurrence
     */
    abstract protected function dehydrateOccurrence(array $occurrence);

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Combine a list of dehydrated occurrences
     *
     * @param array $occurrences List of dehydrated occurrences
     * @return string Combined dehydrated occurrences
     */
    protected function combineDehydratedOccurrences(array $occurrences)
    {
        return implode('', array_map('strval', $occurrences));
    }

    /**
     * Initialize the aggregate part
     *
     * @param string $data Part data
     * @return AbstractPartAggregate Part aggregate
     */
    public function hydrate($data)
    {
        // If the part aggregate class isn't valid
        if (!$this->aggregateClass ||
            !class_exists($this->aggregateClass) ||
            !(new \ReflectionClass($this->aggregateClass))->implementsInterface(PartAggregateInterface::class)
        ) {
            throw new RuntimeException(
                sprintf('Invalid part aggregate class "%s"', $this->aggregateClass),
                RuntimeException::INVALID_PART_AGGREGATE_CLASS
            );
        }

        unset($data);

        return Kernel::create(
            $this->aggregateClass,
            [
                $this,
                $this->subhydrators,
                $this->minimumOccurrences,
                $this->maximumOccurrences
            ]
        );
    }

    /**
     * Dehydrate a single part with a particular subhydrator
     *
     * @param string $subhydrator Subhydrator name
     * @param PartInterface $part Part instance
     * @return string Dehydrated part
     */
    protected function dehydratePart($subhydrator, PartInterface $part)
    {
        /** @var HydratorInterface $subhydratorInstance */
        $subhydratorInstance = $this->subhydrators[$subhydrator];
        return $subhydratorInstance->dehydrate($part);
    }
}
