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

use \Bauwerk\Resource\File\InvalidArgumentException;
use \Bauwerk\Resource\File\RuntimeException;
use \Bauwerk\Resource\File\OutOfBoundsException;
use \Bauwerk\Resource\File\OutOfRangeException;

/**
 * Base class for file resources
 *
 * @package Bauwerk\Resource
 */
class File implements FileInterface {
	/**
	 * File source
	 *
	 * @var string
	 */
	protected $_source = null;
	/**
	 * File parts
	 *
	 * @var array
	 */
	protected $_parts = null;
	/**
	 * Current part index
	 *
	 * @var int
	 */
	protected $_partPosition = 0;
	/**
	 * Default part class
	 *
	 * @var string
	 */
	protected $_defaultPartClass = 'Bauwerk\\Resource\\Part';

	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * Constructor
	 *
	 * @param string $source Source
	 */
	public function __construct($source = null) {
		$this->setSource($source);
	}

	/**
	 * Serialize this file
	 *
	 * @return string                   Serialized file contents
	 */
	public function __toString() {
		try {
			return strval($this->getContent());
		} catch(FileException $e) {
			return '';
		}
	}

	/**
	 * Return the source of this file
	 *
	 * @return string
	 */
	public function getSource() {
		return $this->_source;
	}

	/**
	 * Set the source of this file
	 *
	 * @param string $source Source
	 * @return File                 Self reference
	 */
	public function setSource($source) {
		$this->_source      = trim($source);
		$this->_parts       = null;
		return $this;
	}

	/**
	 * Return the file content
	 *
	 * @return string                           Content
	 */
	public function getContent() {
		return strval($this->getPart(Part::DEFAULT_NAME));
	}

	/**
	 * Set the file content
	 *
	 * @param string $content                   Content
	 * @return File                             Self reference
	 */
	public function setContent($content) {
		return $this->setPart(Part::DEFAULT_NAME, new $this->_defaultPartClass($content));
	}

	/**
	 * Return a file part
	 *
	 * @param string $key                       Part key
	 * @return PartInterface                    Part
	 * @throws OutOfRangeException             If an invalid part is requested
	 * @throws OutOfRangeException             If the requested part key is empty
	 */
	public function getPart($key) {

		// Read the file contents once
		$this->_readSource();

		// If the requested part key is not valid
		if (!$this->_isValidPartKey($key)) {
			throw new OutOfRangeException(sprintf('Invalid file part key "%s"', $key), OutOfRangeException::INVALID_PART_KEY);

		// Else: If the requested part key is not set
		} elseif (!isset($this->_parts[$key])) {
			throw new OutOfRangeException(sprintf('File part key "%s" is empty', $key), OutOfRangeException::PART_KEY_EMPTY);
		}

		return $this->_parts[$key];
	}

	/**
	 * Set a file part
	 *
	 * @param string $key                       Part key
	 * @param PartInterface $part               Part
	 * @return File                             Self reference
	 * @throws OutOfRangeException				If an invalid part is requested
	 */
	public function setPart($key, PartInterface $part) {
		if ($this->_parts === null) {
			$this->_parts = array();
		}

		// If the requested part key is not valid
		if (!$this->_isValidPartKey($key)) {
			throw new OutOfRangeException(sprintf('Invalid file part key "%s"', $key), OutOfRangeException::INVALID_PART_KEY);
		}

		// If the default part is set and doesn't match the default part class
		if (($key == Part::DEFAULT_NAME) && !($part instanceof $this->_defaultPartClass)) {
			throw new InvalidArgumentException(sprintf('Invalid default part class "%s"', get_class($part)), InvalidArgumentException::INVALID_DEFAULT_PART_CLASS);
		}

		$this->_parts[$key] = $part;
		return $this;
	}

	/**
	 * Save the file
	 *
	 * @param string $target Target file
	 * @param bool|false $createDirectories Create directories if necessary
	 * @param bool|false $overwrite Overwrite existing file
	 * @return int                              Number of bytes written
	 * @throws InvalidArgumentException         If the target file is invalid
	 * @throws RuntimeException                 If the target directory doesn't exist and cannot be created
	 */
	public function save($target = null, $createDirectories = false, $overwrite = false) {

		// Use the source path if target was not given
		if ($target === null) {
			$target = $this->_source;
		}

		$target = trim($target);
		if (!strlen($target)) {
			throw new InvalidArgumentException(sprintf('Invalid target file "%s"', $target), InvalidArgumentException::INVALID_TARGET_FILE);
		}

		// If the target directory doesn't exist and should not be created
		if (!@is_dir(dirname($target)) && !$createDirectories) {
			throw new RuntimeException(sprintf('Directory "%s" doesn\'t exist', dirname($target)), RuntimeException::INVALID_TARGET_DIRECTORY);
		}

		// If the target directory doesn't exist and could not be created
		if (!@is_dir(dirname($target)) && !mkdir(dirname($target), 0777, true)) {
			throw new RuntimeException(sprintf('Directory "%s" couldn\'t be created', dirname($target)), RuntimeException::TARGET_DIRECTORY_NOT_CREATED);
		}

		// If the target file already exists but should not be overwritten
		if (@file_exists($target) && !$overwrite) {
			throw new RuntimeException(sprintf('Target file "%s" already exists', $target), RuntimeException::TARGET_EXISTS);
		}

		// If the target file already exists but can't be overwritten
		if (@file_exists($target) && !@unlink($target)) {
			throw new RuntimeException(sprintf('Target file "%s" already exists and couldn\'t be overwritten', $target), RuntimeException::TARGET_NOT_OVERWRITTEN);
		}

		return @file_put_contents($target, strval($this));
	}

	/**
	 * Return the current file part
	 *
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return PartInterface        Current file part
	 * @since 5.0.0
	 */
	public function current() {
		return $this->_parts[$this->_partAtPosition($this->_partPosition)];
	}

	/**
	 * Move forward to next element
	 *
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void
	 * @since 5.0.0
	 */
	public function next() {
		++$this->_partPosition;
	}

	/**
	 * Return the key of the current element
	 *
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed                    Scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public function key() {
		try {
			return $this->_partAtPosition($this->_partPosition);
		} catch (\OutOfBoundsException $e) {
			return null;
		}
	}

	/**
	 * Checks if current position is valid
	 *
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean                  Current item is valid
	 * @since 5.0.0
	 */
	public function valid() {
		try {
			return isset($this->_parts[$this->_partAtPosition($this->_partPosition)]);
		} catch(OutOfBoundsException $e) {
			return false;
		}
	}

	/**
	 * Rewind the Iterator to the first element
	 *
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void
	 * @since 5.0.0
	 */
	public function rewind() {
		$this->_partPosition = 0;
	}

	/**
	 * Whether a offset exists
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param string $offset        An offset to check for
	 * @return boolean              TRUE on success or false on failure.
	 * @since 5.0.0
	 */
	public function offsetExists($offset) {

		// Read the file contents once
		$this->_readSource();

		return isset($this->_parts[$offset]);
	}

	/**
	 * Offset to retrieve
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param string $offset        The offset to retrieve
	 * @return mixed                Element at offset
	 * @since 5.0.0
	 */
	public function offsetGet($offset) {

		// Read the file contents once
		$this->_readSource();

		return isset($this->_parts[$offset]) ? $this->_parts[$offset] : null;
	}

	/**
	 * Offset to set
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset         The offset to assign the value to.
	 * @param PartInterface $value  The value to set
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetSet($offset, $value) {

		// Read the file contents once
		$this->_readSource();

		if ($offset !== null) {
			$this->_parts[$offset] = $value;
		}
	}

	/**
	 * Offset to unset
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset         The offset to unset
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset($offset) {

		// Read the file contents once
		$this->_readSource();

		unset($this->_parts[$offset]);
	}

	/**
	 * Count elements of an object
	 *
	 * @link http://php.net/manual/en/countable.count.php
	 * @return int                      The custom count as an integer.
	 * @since 5.1.0
	 */
	public function count() {

		// Read the file contents once
		$this->_readSource();

		return count($this->_parts);
	}

	/**
	 * Seeks to a position
	 *
	 * @link http://php.net/manual/en/seekableiterator.seek.php
	 * @param int $position             The position to seek to
	 * @return void
	 * @since 5.1.0
	 * @throws OutOfBoundsException     If the requested position doesn't exist
	 */
	public function seek($position) {

		// If the requested position doesn't exist
		if (!isset($this->_parts[$this->_partAtPosition($position)])) {
			throw new OutOfBoundsException(sprintf('Invalid seek position (%s)', $position));
		}

		$this->_partPosition = $position;
	}

	/*******************************************************************************
	 * PRIVATE METHODS
	 *******************************************************************************/

	/**
	 * Read the source file
	 *
	 * @return boolean                          Success
	 */
	protected function _readSource() {

		// Read the file parts only once
		if ($this->_parts === null) {

			// If the source file is valid
			if ($this->_source) {
				if ($this->_validateSource()) {
					$this->_parts = array(Part::DEFAULT_NAME => new $this->_defaultPartClass(@file_get_contents($this->_source)));
				}
			} else {
				$this->_parts = array(Part::DEFAULT_NAME => new $this->_defaultPartClass());
			}
		}
	}

	/**
	 * Validate the source file
	 *
	 * @return boolean                         TRUE if the source file exists and is readable
	 * @throws InvalidArgumentException        If no source file was given
	 * @throws InvalidArgumentException        If the source file doesn't exits
	 * @throws InvalidArgumentException        If the source file isn't readable
	 */
	protected function _validateSource() {

		// If no source file is set
		if (!$this->_source) {
			throw new InvalidArgumentException('No source file given', InvalidArgumentException::NO_SOURCE_FILE);
		}

		// If source file doesn't exist
		if (!@file_exists($this->_source)) {
			throw new InvalidArgumentException(sprintf('Source file "%s" doesn\'t exist', $this->_source),
				InvalidArgumentException::INVALID_SOURCE_FILE);
		}

		// If source file is not a directory
		if (!@is_file($this->_source)) {
			throw new InvalidArgumentException(sprintf('Source "%s" is not a file', $this->_source),
				InvalidArgumentException::SOURCE_NOT_A_FILE);
		}

		// If the source file is not readable
		if (fileperms($this->_source) & 0x04 != 0x04) {
			throw new InvalidArgumentException(sprintf('Source file "%s" is not readable', $this->_source),
				InvalidArgumentException::SOURCE_FILE_UNREADABLE);
		}

		return true;
	}

	/**
	 * Return the part key at a particular position
	 *
	 * @param int $position             Position
	 * @return string                   Part key
	 * @throws OutOfBoundsException     If the requested position doesn't exist
	 */
	protected function _partAtPosition($position) {

		// Read the file contents once
		$this->_readSource();

		$partKeys           = array_keys($this->_parts);
		if (!isset($partKeys[$position])) {
			throw new OutOfBoundsException(sprintf('Invalid part key position (%s)', $position), OutOfBoundsException::INVALID_PART_KEY_POSITION);
		}
		return $partKeys[$position];
	}

	/**
	 * Test if a valid part key is given
	 *
	 * @param string $key				Part key
	 * @return bool						Part key is valid
	 */
	protected function _isValidPartKey($key) {
		return $key == Part::DEFAULT_NAME;
	}
}