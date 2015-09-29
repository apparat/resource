<?php

/**
 * Apparat
 *
 * @category    Jkphl
 * @package     Jkphl_Apparat
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

namespace Apparat\Resource\File\Part\Container\Exception;

use Apparat\Resource\File\Part\Container\ExceptionInterface;

/**
 * Invalid argument container file part exception
 *
 * @package Resource\Container
 */
class InvalidArgument extends \InvalidArgumentException implements ExceptionInterface {
	/**
	 * Invalid default part class
	 *
	 * @var int
	 */
	const INVALID_DEFAULT_PART_CLASS = 1440530628;
	/**
	 * Invalid minimum occurences value
	 *
	 * @var int
	 */
	const INVALID_MINIMUM_OCCURENCES = 1441485633;
	/**
	 * Invalid maximum occurences value
	 *
	 * @var int
	 */
	const INVALID_MAXIMUM_OCCURENCES = 1441485648;
	/**
	 * Invalid class names array
	 *
	 * @var int
	 */
	const INVALID_CLASSNAMES_ARRAY = 1441485730;
	/**
	 * Invalid part class
	 *
	 * @var int
	 */
	const INVALID_PART_CLASS = 1442783125;
}