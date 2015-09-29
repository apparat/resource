<?php

/**
 * resource
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

namespace Apparat\Resource\File\Part\Body;

use Apparat\Resource\File\Part\Body;
use Apparat\Resource\File\Part\Body\Exception\OutOfBounds;

/**
 * YAML file part
 *
 * @package Apparat\Resource\File\Part\Body
 * @see http://yaml.org/spec/1.2/spec.pdf
 */
class Yaml extends Text implements \ArrayAccess, \Countable, \SeekableIterator
{
    /**
     * MIME type
     *
     * @var string
     */
    protected $_mimeType = 'text/x-yaml';

    /**
     * Internal position
     *
     * @var int
     */
    protected $_position = 0;
    /**
     * Yaml data
     *
     * @var array
     */
    protected $_data = null;

    /**
     * Reset the part to its default state
     *
     * @return Yaml             Self reference
     */
    public function reset()
    {
        $this->_data = null;
        $this->_position = 0;
        return parent::reset();
    }

    /**
     * Return the part contents as string
     *
     * @return string           Part contents
     */
    public function __toString()
    {
        return \Symfony\Component\Yaml\Yaml::dump($this->_data);
    }

    /**
     * Parse a content string and bring the part model to live
     *
     * @param string $content Content string
     * @return Yaml                Self reference
     */
    public function parse($content)
    {
        parent::parse($content);
        $this->_data = strlen($this->_content) ? \Symfony\Component\Yaml\Yaml::parse($this->_content) : array();
        return $this;
    }

    /**
     * Prepend text content
     *
     * @param string $content Text content to be prepended
     * @return Yaml Self reference
     */
    public function prepend($content)
    {
        // Check if the content starts with a document marker
        if (!strncmp($this->_content, '---', 3)) {
            $content = implode(PHP_EOL.trim($content).PHP_EOL, preg_split("%\R%u", $this->_content, 2));
        } else {
            $content = trim($content).PHP_EOL.$this->_content;
        }

        return $this->parse($content);
    }

    /**
     * Append text content
     *
     * @param string $content Text content to be appended
     * @return Yaml Self reference
     */
    public function append($content)
    {
        $content = $this->_content.(preg_match("%\R$%u", $this->_content) ? '' : PHP_EOL).trim($content);
        return $this->parse($content);
    }

    /**
     * Return the YAML data
     *
     * @return array                YAML data
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Set the YAML data
     *
     * @param array $data YAML data
     * @return Yaml
     */
    public function setData(array $data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset An offset to check for
     * @return boolean              TRUE on success or false on failure.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset The offset to retrieve
     * @return mixed                Element at offset
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value The value to set
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if ($offset !== null) {
            $this->_data[$offset] = $value;
        }
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset The offset to unset
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    /**
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed            Current element
     * @since 5.0.0
     */
    public function current()
    {
        return $this->_data[$this->_keyAtPosition($this->_position)];
    }

    /**
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void
     * @since 5.0.0
     */
    public function next()
    {
        ++$this->_position;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        try {
            return $this->_keyAtPosition($this->_position);
        } catch (OutOfBounds $e) {
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
    public function valid()
    {
        return isset($this->_data[$this->_keyAtPosition($this->_position)]);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->_position = 0;
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int                      The custom count as an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->_data);
    }

    /**
     * Seeks to a position
     * @link http://php.net/manual/en/seekableiterator.seek.php
     * @param int $position The position to seek to.
     * @return void
     * @since 5.1.0
     * @throws OutOfBounds     If the requested position doesn't exist
     */
    public function seek($position)
    {
        if (!isset($this->_data[$this->_keyAtPosition($position)])) {
            throw new OutOfBounds(sprintf('Invalid seek position (%s)', $position), OutOfBounds::INVALID_SEEK_POSITION);
        }

        $this->_position = $position;
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Return the data key at a particular position
     *
     * @param int $position Position
     * @return string                   Data key
     * @throws OutOfBounds              If the requested position doesn't exist
     */
    protected function _keyAtPosition($position)
    {
        $dataKeys = array_keys($this->_data);
        if (!isset($dataKeys[$position])) {
            throw new OutOfBounds(sprintf('Invalid data key (%s)', $position), OutOfBounds::INVALID_DATA_KEY);
        }
        return $dataKeys[$position];
    }
}