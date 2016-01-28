<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace ApparatTest;

use Apparat\Resource\Framework\Model\Hydrator\FrontMarkHydrator;

/**
 * Mocked FrontMark hydrator
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 */
class FrontMarkHydratorMock extends FrontMarkHydrator
{
    /**
     * Part aggregate class name
     *
     * @var string
     */
    protected $aggregateClass = PartSequenceMock::class;
}
