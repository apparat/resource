<?php

/**
 * bauwerk-resource
 *
 * @category    Jkphl
 * @package     Jkphl_Bauwerk
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

namespace Bauwerk\Resource\Container;


abstract class AbstractModel
{
    /**
     * Minimum occurrences
     *
     * @var int
     */
    protected $_min = 0;
    /**
     * Maximum occurrences
     *
     * @var int
     */
    protected $_max = 0;
    /**
     * Content classes
     *
     * @var array
     */
    protected $_classes = array();
    /**
     * Unbound occurences
     *
     * @var int
     */
    const UNBOUND = -1;

    /**
     * Constructor
     *
     * @param array $classes Part class names and their keys
     * @param int $min Minimum occurences
     * @param int $max Maximum occurences
     * @throws InvalidArgumentException     If the class names array is empty
     * @throws InvalidArgumentException     If the minimum / maximum occurences is not an integer
     * @throws InvalidArgumentException     If the minimum / maximum occurences is out of range
     */
    public function __construct(array $classes, $min, $max)
    {
        if (!count($classes)) {
            throw new InvalidArgumentException('Invalid class names array for container model',
                InvalidArgumentException::INVALID_CLASSNAMES_ARRAY);
        }

        $this->_classes = $classes;

        if (!is_int($min) || ($min < 0)) {
            throw new InvalidArgumentException(sprintf('Invalid minimum occurences value "%s"', $min),
                InvalidArgumentException::INVALID_MINIMUM_OCCURENCES);
        }
        $this->_min = intval($min);

        if (!is_int($max) || ($max < -1)) {
            throw new InvalidArgumentException(sprintf('Invalid maximum occurences value "%s"', $max),
                InvalidArgumentException::INVALID_MAXIMUM_OCCURENCES);
        }
        $this->_max = intval($max);
    }
}