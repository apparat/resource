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

namespace Bauwerk\Resource\File\Part;

use Bauwerk\Resource\File\PartInterface;
use Bauwerk\Resource\File\Part\Container\Exception\OutOfRange;

/**
 * Container file part interface
 *
 * @package Bauwerk\Resource\File\Part
 */
interface ContainerInterface extends PartInterface, \ArrayAccess , \SeekableIterator , \Countable
{
    /**
     * Unbound parts
     *
     * @var int
     */
    const UNBOUND = -1;

    /**
     * Return a container part
     *
     * @param string $key                       Part key
     * @param int $occurrence                   Part index (within the same key)
     * @return PartInterface                    Part
     * @throws OutOfRange                       If an invalid part is requested
     * @throws OutOfRange                       If the requested part key is emptyW
     */
    public function getPart($key, $occurrence = 0);

    /**
     * Set a file part
     *
     * @param string $key                       Part key
     * @param PartInterface $part               Part
     * @param int $occurrence                   Part index (within the same key)
     * @return ContainerInterface               Self reference
     * @throws OutOfRange                       If an invalid part is requested
     */
    public function setPart($key, PartInterface $part, $occurrence = 0);
}