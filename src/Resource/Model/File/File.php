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

namespace Apparat\Resource\Model\File;

use Apparat\Resource\Model\Hydrator\Hydrator;
use Apparat\Resource\Model\Hydrator\HydratorFactory;
use Apparat\Resource\Model\File\InvalidArgumentException;
use Apparat\Resource\Model\Part\Part;
use Apparat\Resource\Model\Reader;
use Apparat\Resource\Model\Resource;
use Apparat\Resource\Model\Writer;

/**
 * File
 *
 * @package Apparat\Resource\Model\File
 */
abstract class File extends Resource
{
    /**
     * Part or part aggregate
     *
     * @var Part
     */
    protected $_part = null;

    /**
     * Reader instance
     *
     * @var Reader
     */
    protected $_reader = null;
    /**
     * File hydrator
     *
     * @var Hydrator
     */
    private $_hydrator = null;

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Set a reader instance for this file
     *
     * @param Reader $reader Reader instance
     * @return File Self reference
     */
    public function load(Reader $reader)
    {
        $this->_reset();
        $this->_reader = $reader;
        return $this;
    }

    /**
     * Dump this files contents into a writer
     *
     * @param Writer $writer Writer instance
     * @return File Self reference
     */
    public function dump(Writer $writer)
    {
        $writer->write($this->getPart('/'));
        return $this;
    }

    /**
     * Set the content of a particular part
     *
     * @param mixed $data Content
     * @param string $part Part path
     * @return File Self reference
     */
    public function setPart($data, $part = '/')
    {
        $this->_part = $this->_part()->set($data, $this->_partPath($part));
        return $this;
    }

    /**
     * Return the parts content
     *
     * @param string $part Part path
     * @return string Part content
     */
    public function getPart($part = '/')
    {
        $partPath = $this->_partPath($part);
        $part = $this->_part()->get($partPath);
        return $this->_hydrator->getSub($partPath)->dehydrate($part);
    }

    /**
     * Return the MIME type of a particular part
     *
     * @param string $part Part path
     * @return string MIME type
     */
    public function getMimeTypePart($part = '/')
    {
        return $this->_part()->getMimeType($this->_partPath($part));
    }

    /**
     * Magic caller for part methods
     *
     * @param string $name Part method name
     * @param array $arguments Part method arguments
     * @return File Self reference
     * @throw RuntimeException  If an invalid file method is called
     * @throw RuntimeException  If an invalid file part method is called
     */
    public function __call($name, array $arguments)
    {
        if (preg_match("%^(.+)Part$%", $name, $partMethod)) {
            if (@is_callable(array($this->_part(), $partMethod[1]))) {
                $data = (count($arguments) > 0) ? $arguments[0] : '';
                $path = $this->_partPath((count($arguments) > 1) ? $arguments[1] : '/');
                $this->_part = call_user_func(array($this->_part(), $partMethod[1]), $data, $path);
                return $this;
            } else {
                throw new RuntimeException(sprintf('Invalid file part method "%s"', $partMethod[1]),
                    RuntimeException::INVALID_FILE_PART_METHOD);
            }
        } else {
            throw new RuntimeException(sprintf('Invalid file method "%s"', $name),
                RuntimeException::INVALID_FILE_METHOD);
        }
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/


    /**
     * Private constructor
     *
     * @param Reader $reader Reader instance
     * @param Hydrator|array|string $hydrator File hydrator
     */
    protected function __construct(Reader $reader = null, $hydrator)
    {
        // If the hydrator needs to be instancianted from a string or array
        if (!($hydrator instanceof Hydrator)) {
            $hydrator = HydratorFactory::build((array)$hydrator);
        }

        // Register the hydrator
        $this->_hydrator = $hydrator;

        // Register the reader if available
        if ($reader instanceof Reader) {
            $this->load($reader);
        }
    }

    /**
     * Reset the file
     *
     * @return void
     */
    protected function _reset()
    {
        $this->_part = null;
    }

    /**
     * Lazy-hydrate and return the main file part
     *
     * @return Part Main file part
     */
    protected function _part()
    {
        if (!($this->_part instanceof Part)) {
            $this->_part = $this->_hydrator->hydrate(($this->_reader instanceof Reader) ? $this->_reader->read() : '');
        }
        return $this->_part;
    }

    /**
     * Split a part path string into path identifiers
     *
     * @param string $path Part path string
     * @return array Part path identifiers
     * @throws InvalidArgumentException If an invalid path part identifier is found
     */
    protected function _partPath($path)
    {
        return (trim($path) == '/') ? [] : array_map(function ($pathIdentifier) {
            $pathIdentifier = trim($pathIdentifier);
            if (!preg_match("%^[a-z0-9\_]+$%i", $pathIdentifier)) {
                throw new InvalidArgumentException(sprintf('Invalid part path identifier "%s"', $pathIdentifier),
                    InvalidArgumentException::INVALID_PART_IDENTIFIER);
            }
            return $pathIdentifier;
        }, explode('/', ltrim($path, '/')));
    }
}