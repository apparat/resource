<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Tests
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
 *  this software and associated documentation Fixture (the "Software"), to deal in
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

namespace Apparat\Resource\Tests;

use Apparat\Resource\Domain\Model\Part\AbstractPartAggregate;
use Apparat\Resource\Domain\Model\Part\PartAggregateInterface;

/**
 * Mock methods for multipart hydrators
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Tests
 * @property string $aggregateClass Part aggregate class name
 */
trait AggregateHydratorMockTrait
{
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

        // If the default validation should be used
        if (getenv('MOCK_VALIDATE_PARAMETERS') != 1) {
            /** @noinspection PhpUndefinedMethodInspection */
            return parent::validateParameters(...$parameters);

            // Else return a mock result
        } else {
            return false;
        }
    }

    /**
     * Translate data to a file part
     *
     * @param string $data Part data
     * @return PartAggregateInterface Resource part
     */
    public function hydrate($data)
    {
        if (getenv('MOCK_AGGREGATE_CLASS') == 1) {
            $this->aggregateClass = self::class;
        }

        /** @var AbstractPartAggregate $aggregate */
        /** @noinspection  PhpUndefinedMethodInspection */
        $aggregate = parent::hydrate(null);
        foreach (explode('|', $data) as $part => $str) {
            if (getenv('MOCK_OCCURRENCE_NUMBER') == 1) {
                $aggregate->assign(0, $str, $part);
            } elseif (getenv('MOCK_ASSIGNMENT_PART_IDENTIFIER') == 1) {
                $aggregate->assign("_$part", $str, 0);
            } else {
                $aggregate->assign("$part", $str);
            }
        }
        return $aggregate;
    }

    /**
     * Dehydrate a single occurrence
     *
     * @param array $occurrence Occurrence
     * @return string Dehydrated occurrence
     */
    protected function dehydrateOccurrence(array $occurrence)
    {
        // If the default validation should be used
        if (getenv('MOCK_OCCURRENCE_DEHYDRATION') != 1) {
            // If an empty occurrence shall be tested
            if (getenv('MOCK_EMPTY_OCCURRENCE') == 1) {
                /** @noinspection PhpUndefinedMethodInspection */
                return parent::dehydrateOccurrence([]);

                // If an invalid subhydrator name should be tested
            } elseif (getenv('MOCK_SUBHYDRATOR_NAME') == 1) {
                /** @noinspection PhpUndefinedMethodInspection */
                return parent::dehydrateOccurrence(
                    array_combine(
                        array_map(
                            function ($name) {
                                return '_'.$name.'_';
                            },
                            array_keys($occurrence)
                        ),
                        array_values($occurrence)
                    )
                );

                // If an invalid part instance should be tested
            } elseif (getenv('MOCK_PART_INSTANCE') == 1) {
                /** @noinspection PhpUndefinedMethodInspection */
                return parent::dehydrateOccurrence(array_fill_keys(array_keys($occurrence), null));

                // Else: Regular processing
            } else {
                /** @noinspection PhpUndefinedMethodInspection */
                return parent::dehydrateOccurrence($occurrence);
            }
            // Else return a mock result
        } else {
            return [];
        }
    }
}
