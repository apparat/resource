<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
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

namespace Apparat\Resource\Framework\Io\File;

/**
 * File reader/writer invalid argument exception
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 */
class InvalidArgumentException extends \InvalidArgumentException
{
    /**
     * File does not exist
     *
     * @var int
     */
    const FILE_DOES_NOT_EXIST = 1447616824;

    /**
     * File is not a file
     *
     * @var int
     */
    const FILE_IS_NOT_A_FILE = 1447618938;

    /**
     * File is not readable
     *
     * @var int
     */
    const FILE_NOT_READABLE = 1447617006;

    /**
     * Invalid file writer options
     *
     * @var int
     */
    const INVALID_FILE_WRITER_OPTIONS = 1447617559;

    /**
     * File cannot be created
     *
     * @var int
     */
    const FILE_CANNOT_BE_CREATED = 1447617960;

    /**
     * File cannot be overwritten
     *
     * @var int
     */
    const FILE_CANNOT_BE_OVERWRITTEN = 1447617979;
}
