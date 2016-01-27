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

/**
 * Runtime exception
 *
 * @package     Apparat\Resource
 * @subpackage Apparat\Resource\Domain
 */
class RuntimeException extends \RuntimeException
{
    /**
     * Invalid part aggregate class
     *
     * @var int
     */
    const INVALID_PART_AGGREGATE_CLASS = 1447887703;
    /**
     * Empty occurrence
     *
     * @var int
     */
    const EMPTY_OCCURRENCE = 1448108316;
    /**
     * No matching subhydrator
     *
     * @var int
     */
    const NO_MATCHING_SUBHYDRATOR = 1448108444;
    /**
     * Invalid part instance
     *
     * @var int
     */
    const INVALID_PART_INSTANCE = 1448108849;
    /**
     * Occurrence dehydration must return a string
     *
     * @var int
     */
    const OCCURRENCE_DEHYDRATION_MUST_RETURN_A_STRING = 1448112964;
}
