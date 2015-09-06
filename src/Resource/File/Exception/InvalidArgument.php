<?php

/**
 * Bauwerk
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

namespace Bauwerk\Resource\File\Exception;

use Bauwerk\Resource\File\ExceptionInterface;

/**
 * Invalid argument file exception
 *
 * @package Resource\File
 */
class InvalidArgument extends \InvalidArgumentException implements ExceptionInterface {
	/**
	 * No source file
	 *
	 * @var int
	 */
	const NO_SOURCE_FILE = 1440346414;
	/**
	 * Source file doesn't exist or is not a file
	 *
	 * @var int
	 */
	const INVALID_SOURCE_FILE = 1440346451;
	/**
	 * Source is not a file
	 *
	 * @var int
	 */
	const SOURCE_NOT_A_FILE = 1440347668;
	/**
	 * Source file is not readable
	 *
	 * @var int
	 */
	const SOURCE_FILE_UNREADABLE = 1440346535;
	/**
	 * Invalid target file
	 *
	 * @var int
	 */
	const INVALID_TARGET_FILE = 1440361529;
}