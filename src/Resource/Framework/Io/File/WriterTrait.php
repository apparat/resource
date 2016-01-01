<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
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
 * File writer trait
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 * @property string $_file File path
 */
trait WriterTrait

{
	/**
	 * File options
	 *
	 * @var int
	 */
	protected $_options;

	/**
	 * Set the file options
	 *
	 * @param int $options File options
	 */
	protected function _setOptions($options)
	{
		$options = intval($options);
		$allOptions = Writer::FILE_CREATE | Writer::FILE_OVERWRITE;

		if (($options & $allOptions) != $options) {
			throw new InvalidArgumentException(sprintf('Invalid file writer option "%s"', $options & ~$allOptions),
				InvalidArgumentException::INVALID_FILE_WRITER_OPTIONS);
		}

		$this->_options = $options;
	}

	/**
	 * Validate the writer file
	 *
	 * @throws InvalidArgumentException If the file cannot be created
	 * @throws InvalidArgumentException If the file cannot be overwritten
	 */
	protected function _validateWriterFile()
	{
		// If the file does not exist and cannot be created
		if (!@file_exists($this->_file) && !($this->_options & Writer::FILE_CREATE)) {
			throw new InvalidArgumentException(sprintf('File "%s" cannot be created', $this->_file),
				InvalidArgumentException::FILE_CANNOT_BE_CREATED);
		}

		// If the file exists but cannot be overwritten
		if (@file_exists($this->_file) && (!@is_file($this->_file) || !@is_writeable($this->_file) || !($this->_options & Writer::FILE_OVERWRITE))) {
			throw new InvalidArgumentException(sprintf('File "%s" cannot be overwritten', $this->_file),
				InvalidArgumentException::FILE_CANNOT_BE_OVERWRITTEN);
		}
	}

	/**
	 * Write data
	 *
	 * @param string $data Data to write
	 * @return int Bytes written
	 */
	public function write($data)
	{
		return file_put_contents($this->_file, $data);
	}
}