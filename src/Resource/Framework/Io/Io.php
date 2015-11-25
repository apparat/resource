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

namespace Apparat\Resource\Framework\Io;

use Apparat\Resource\Framework\Io\File\AbstractFileReaderWriter;
use Apparat\Resource\Framework\Io\File\Reader as FileReader;
use Apparat\Resource\Framework\Io\InMemory\AbstractInMemoryReaderWriter;
use Apparat\Resource\Framework\Io\InMemory\Reader as InMemoryReader;
use Apparat\Resource\Framework\Io\File\Writer as FileWriter;
use Apparat\Resource\Framework\Io\InMemory\Writer as InMemoryWriter;

/**
 * Reader / writer utilities
 *
 * @package Apparat\Resource\Framework\Io
 */
class Io
{
	/**
	 * Reader classes for stream wrappers
	 *
	 * @var array
	 */
	public static $readers = array(
		AbstractFileReaderWriter::WRAPPER => FileReader::class,
		AbstractInMemoryReaderWriter::WRAPPER => InMemoryReader::class,
	);

	/**
	 * Writer classes for stream wrappers
	 *
	 * @var array
	 */
	public static $writer = array(
		AbstractFileReaderWriter::WRAPPER => FileWriter::class,
		AbstractInMemoryReaderWriter::WRAPPER => InMemoryWriter::class,
	);
}