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

namespace Apparat\Resource\Framework\File;


use Apparat\Resource\Framework\Hydrator\TextHydrator;
use Apparat\Resource\Model\File\File;
use Apparat\Resource\Model\Reader;

/**
 * Text file
 *
 * @package Apparat\Resource\Framework\File
 * @method TextFile setPart() set(string $data, string $part = '/') Set the content of the file
 * @method TextFile appendPart() appendPart(string $data, string $part = '/') Append content to the file
 * @method TextFile prependPart() prependPart(string $data, string $part = '/') Prepend content to the file
 * @method void undefinedMethod() undefinedMethod() Undefined method for testing purposes
 * @method void undefinedMethodPart() undefinedMethodPart() Undefined method for testing purposes
 */
class TextFile extends File
{
    /**
     * Text file constructor
     *
     * @param Reader $reader Reader instance
     */
    public function __construct(Reader $reader = null)
    {
        parent::__construct($reader, TextHydrator::class);
    }
}