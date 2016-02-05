<?php

/**
 * apparat/resource
 *
 * @category    Jkphl
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Infrastructure
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

namespace Apparat\Resource\Infrastructure\Model\Resource;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Resource\Domain\Contract\WriterInterface;
use Apparat\Resource\Domain\Model\Resource\AbstractResource;
use Apparat\Resource\Ports\InvalidArgumentException;
use Apparat\Resource\Ports\Tools;
use Apparat\Resource\Infrastructure\Io\InMemory\Writer as InMemoryWriter;

/**
 * Resource factory methods
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Infrastructure
 */
trait ResourceTrait
{
	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * String serialization
	 *
	 * @return string String value (file content)
	 */
	public function __toString()
	{
		/** @var InMemoryWriter $writer */
		$writer = Kernel::create(InMemoryWriter::class);

		/** @var AbstractResource $this */
		$this->dump($writer);

		return $writer->getData();
	}

	/**
	 * Dump this file to a stream-wrapped target
	 *
	 * @param string $target Stream-wrapped target
	 * @param array $parameters Writer parameters
	 * @return WriterInterface Writer instance
	 * @throws InvalidArgumentException If an invalid reader stream wrapper is given
	 */
	public function toTarget($target, ...$parameters)
	{
		$writer = Tools::writer($target, $parameters);
		if ($writer instanceof WriterInterface) {
			$this->dump($writer);
			return $writer;
		}

		throw new InvalidArgumentException(
			'Invalid writer stream wrapper',
			InvalidArgumentException::INVALID_WRITER_STREAM_WRAPPER
		);
	}

	/**
	 * Dump this files contents into a writer
	 *
	 * @param WriterInterface $writer Writer instance
	 * @return Resource Self reference
	 */
	abstract public function dump(WriterInterface $writer);
}
