<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace ApparatTest;

use Apparat\Resource\Domain\Model\Part\AbstractPartAggregate;
use Apparat\Resource\Domain\Model\Part\PartAggregateInterface;

/**
 * Mock methods for multipart hydrators
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 */
trait AggregateHydratorMockTrait
{
    /**
     * Translate data to a file part
     *
     * @param string $data Part data
     * @return PartAggregateInterface Resource part
     */
    public function hydrate($data)
    {
        if (!empty($GLOBALS['mockAggregateClass'])) {
            $this->aggregateClass = self::class;
        }

        /** @var AbstractPartAggregate $aggregate */
        /** @noinspection  PhpUndefinedMethodInspection */
        $aggregate = parent::hydrate(null);
        foreach (explode('|', $data) as $part => $str) {
            if (!empty($GLOBALS['mockOccurrenceNumber'])) {
                $aggregate->assign(0, $str, $part);
            } elseif (!empty($GLOBALS['mockAssignmentPartIdentifier'])) {
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
        if (empty($GLOBALS['mockOccurrenceDehydration'])) {
            // If an empty occurrence shall be tested
            if (!empty($GLOBALS['mockEmptyOccurrence'])) {
                /** @noinspection PhpUndefinedMethodInspection */
                return parent::dehydrateOccurrence([]);

                // If an invalid subhydrator name should be tested
            } elseif (!empty($GLOBALS['mockSubhydratorName'])) {
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
            } elseif (!empty($GLOBALS['mockPartInstance'])) {
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

        // If the default validation should be used
        if (empty($GLOBALS['mockValidateParameters'])) {
            /** @noinspection PhpUndefinedMethodInspection */
            return parent::validateParameters(...$parameters);

            // Else return a mock result
        } else {
            return false;
        }
    }
}
