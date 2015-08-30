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

namespace Bauwerk\Resource;

interface FileInterface extends \ArrayAccess, \Countable, \SeekableIterator {
	/**
	 * Return the source of this file
	 *
	 * @return string
	 */
	public function getSource();

	/**
	 * Set the source of this file
	 *
	 * @param string $source                    Source
	 * @return File                             Self reference
	 */
	public function setSource($source);

	/**
	 * Return the file content
	 *
	 * @return string                           Content
	 */
	public function getContent();

	/**
	 * Set the file content
	 *
	 * @param string $content                   Content
	 * @return File                             Self reference
	 */
	public function setContent($content);

	/**
	 * Return a file part
	 *
	 * @param string $key                       Part key
	 * @return PartInterface                    Part
	 * @throws \OutOfRangeException             If an invalid part is requested
	 */
	public function getPart($key);

	/**
	 * Set a file part
	 *
	 * @param string $key                       Part key
	 * @param PartInterface $part               Part
	 * @return File                             Self reference
	 */
	public function setPart($key, PartInterface $part);
}