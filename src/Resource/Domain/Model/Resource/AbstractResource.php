<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage Apparat\Resource\Domain
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

namespace Apparat\Resource\Domain\Model\Resource;

use Apparat\Resource\Domain\Contract\ReaderInterface;
use Apparat\Resource\Domain\Contract\WriterInterface;
use Apparat\Resource\Domain\Factory\HydratorFactory;
use Apparat\Resource\Domain\Model\Hydrator\HydratorInterface;
use Apparat\Resource\Domain\Model\Part\AbstractPart;
use Apparat\Resource\Domain\Model\Part\PartInterface;

/**
 * File
 *
 * @package     Apparat\Resource
 * @subpackage Apparat\Resource\Domain
 */
abstract class AbstractResource
{
	/**
	 * Part or part aggregate
	 *
	 * @var PartInterface
	 */
	protected $_part = null;

	/**
	 * Reader instance
	 *
	 * @var ReaderInterface
	 */
	protected $_reader = null;
	/**
	 * File hydrator
	 *
	 * @var HydratorInterface
	 */
	private $_hydrator = null;

	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * Set a reader instance for this file
	 *
	 * @param ReaderInterface $reader Reader instance
	 * @return Resource Self reference
	 */
	public function load(ReaderInterface $reader)
	{
		$this->_reset();
		$this->_reader = $reader;
		return $this;
	}

	/**
	 * Dump this files contents into a writer
	 *
	 * @param WriterInterface $writer Writer instance
	 * @return Resource Self reference
	 */
	public function dump(WriterInterface $writer)
	{
		$writer->write($this->getPart('/'));
		return $this;
	}

	/**
	 * Set the content of a particular part
	 *
	 * @param mixed $data Content
	 * @param string $part Part path
	 * @return Resource Self reference
	 */
	public function setPart($data, $part = '/')
	{
		$this->_part = $this->_part()->set($data, $this->_partPath($part));
		return $this;
	}

	/**
	 * Return the part's content
	 *
	 * @param string $part Part path
	 * @return string Part content
	 */
	public function getPart($part = '/')
	{
		$partPath = $this->_partPath($part);
		$part = $this->_part()->get($partPath);
		return $part->getHydrator()->dehydrate($part);
	}

	/**
	 * Magic caller for part methods
	 *
	 * @param string $name Part method name
	 * @param array $arguments Part method arguments
	 * @return Resource Self reference
	 * @throw RuntimeException  If an invalid file method is called
	 * @throw RuntimeException  If an invalid file part method is called
	 */
	public function __call($name, array $arguments)
	{
		// If a (sub)part method is called
		if (preg_match("%^(.+)Part$%", $name, $partMethod)) {
			$partMethod = $partMethod[1];
			$isGetterMethod = (!strncmp('get', $partMethod, 3));
			$delegateArguments = $isGetterMethod ? array() : array_slice($arguments, 0, 1);
			$subpartPathArgumentIndex = $isGetterMethod ? 0 : 1;
			$subparts = $this->_partPath((count($arguments) > $subpartPathArgumentIndex) ? $arguments[$subpartPathArgumentIndex] : '/');
			$delegateResult = $this->_part()->delegate($partMethod, $subparts, $delegateArguments);
			if ($isGetterMethod) {
				return $delegateResult;
			} else {
				$this->_part = $delegateResult;
				return $this;
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
	 * @param ReaderInterface $reader Reader instance
	 * @param HydratorInterface|array|string $hydrator File hydrator
	 */
	protected function __construct(ReaderInterface $reader = null, $hydrator)
	{
		// If the hydrator needs to be instantiated from a string or array
		if (!($hydrator instanceof HydratorInterface)) {
			$hydrator = HydratorFactory::build((array)$hydrator);
		}

		// Register the hydrator
		$this->_hydrator = $hydrator;

		// Register the reader if available
		if ($reader instanceof ReaderInterface) {
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
	 * @return PartInterface Main file part
	 */
	protected function _part()
	{
		if (!($this->_part instanceof PartInterface)) {
			$this->_part = $this->_hydrator->hydrate(($this->_reader instanceof ReaderInterface) ? $this->_reader->read() : '');
		}
		return $this->_part;
	}

	/**
	 * Split a part path string into path identifiers
	 *
	 * @param string $path Part path string
	 * @return array Part path identifiers
	 */
	protected function _partPath($path)
	{
		return (trim($path) == '/') ? [] : array_map(function ($pathIdentifier) {
			$pathIdentifier = trim($pathIdentifier);
			AbstractPart::validatePartIdentifier($pathIdentifier);
			return $pathIdentifier;
		}, explode('/', ltrim($path, '/')));
	}
}