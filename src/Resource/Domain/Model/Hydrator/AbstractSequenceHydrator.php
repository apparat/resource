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

use Apparat\Resource\Domain\Model\Part\PartInterface;
use Apparat\Resource\Domain\Model\Part\PartSequence;

/**
 * Abstract sequence hydrator
 *
 * @package     Apparat\Resource
 * @subpackage Apparat\Resource\Domain
 */
abstract class AbstractSequenceHydrator extends AbstractMultipartHydrator
{
    /**
     * Part aggregate class name
     *
     * @var string
     */
    protected $aggregateClass = PartSequence::class;

    /**
     * Dehydrate a single occurrence
     *
     * @param array $occurrence Occurrence
     * @return string Dehydrated occurrence
     * @throws RuntimeException If the occurrence is invalid
     * @throws RuntimeException If a part name doesn't match a known subhydrator
     * @throws RuntimeException If a part is invalid
     */
    protected function dehydrateOccurrence(array $occurrence)
    {
        // If the occurrence is invalid
        if (!count($occurrence)) {
            throw new $this->occDhdrException(
                'Empty occurrence',
                constant($this->occDhdrException . '::EMPTY_OCCURRENCE')
            );
        }

        $sequence = [];

        // Run through the sequence
        foreach ($occurrence as $subhydrator => $part) {
            // If the part name doesn't match a known subhydrator
            if (!strlen($subhydrator) || !array_key_exists($subhydrator, $this->subhydrators)) {
                throw new $this->occDhdrException(
                    sprintf('No matching subhydrator "%s"', $subhydrator),
                    constant($this->occDhdrException . '::NO_MATCHING_SUBHYDRATOR')
                );
            }

            // If the part value is not a valid part instance
            if (!$part || !($part instanceof PartInterface)) {
                throw new $this->occDhdrException(
                    sprintf(
                        'Invalid part instance "%s"',
                        gettype($part) . (is_object($part) ? '<' . get_class($part) . '>' : '')
                    ),
                    constant($this->occDhdrException . '::INVALID_PART_INSTANCE')
                );
            }

            $sequence[$subhydrator] = $this->dehydratePart($subhydrator, $part);
        }

        return $this->combineOccurrenceSequence($sequence);
    }

    /**
     * Combine a part occurrence sequence for dehydration
     *
     * @param array $sequence Part occurrence sequence
     * @return string Combined sequence
     */
    protected function combineOccurrenceSequence(array $sequence)
    {
        return implode('', array_map('strval', $sequence));
    }
}
