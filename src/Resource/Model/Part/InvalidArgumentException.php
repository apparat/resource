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

namespace Apparat\Resource\Model\Part;

/**
 * Invalid file part argument exception
 *
 * @package Apparat\Resource\Model\Part
 */
class InvalidArgumentException extends \InvalidArgumentException
{
    /**
     * Subparts are not allowed
     *
     * @var int
     */
    const SUBPARTS_NOT_ALLOWED = 1447365624;
    /**
     * Invalid minimum occurrences
     *
     * @var int
     */
    const INVALID_MINIMUM_OCCURRENCES = 1447021191;
    /**
     * Invalid maximum occurrences
     *
     * @var int
     */
    const INVALID_MAXIMUM_OCCURRENCES = 1447021211;
    /**
     * Invalid part identifier
     *
     * @var int
     */
    const INVALID_PART_IDENTIFIER = 1447364401;
    /**
     * Empty part identifier
     *
     * @var int
     */
    const EMPTY_PART_IDENTIFIER = 1447876355;
    /**
     * Unknown part identifier
     *
     * @var int
     */
    const UNKNOWN_PART_IDENTIFIER = 1447876475;
    /**
     * Too few subpart identifiers
     *
     * @var int
     */
    const TOO_FEW_SUBPART_IDENTIFIERS = 1448051332;
    /**
     * Invalid occurrence identifier
     *
     * @var int
     */
    const INVALID_OCCURRENCE_INDEX = 1448051596;
    /**
     * Part does not exist
     *
     * @var int
     */
    const PART_DOES_NOT_EXIST = 1448053518;
    /**
     * Unknown part method
     *
     * @var int
     */
    const UNKNOWN_PART_METHOD = 1448225222;
}