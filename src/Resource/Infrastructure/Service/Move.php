<?php

/**
 * apparat-resource
 *
 * @category    Apparat
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

namespace Apparat\Resource\Infrastructure\Service;

use Apparat\Resource\Domain\Contract\WriterInterface;
use Apparat\Resource\Ports\InvalidArgumentException;
use Apparat\Resource\Ports\Tools;
use Apparat\Resource\Infrastructure\Io\File\Reader as FileReader;
use Apparat\Resource\Infrastructure\Io\File\Writer as FileWriter;
use Apparat\Resource\Infrastructure\Io\InMemory\Writer as InMemoryWriter;

/**
 * Resource move operation
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Infrastructure
 */
class Move extends AbstractService
{
    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Move / rename the source resource to a target resource
     *
     * @param string $target Stream wrapped target
     * @param array $parameters Writer parameters
     * @return WriterInterface Writer instance
     * @throws InvalidArgumentException If the writer stream wrapper is invalid
     */
    public function toTarget($target, ...$parameters)
    {
        $writer = Tools::writer($target, $parameters);

        // If it's a file writer
        if ($writer instanceof FileWriter) {
            return $this->moveToFile($writer);
        }

        // If it's an in-memory writer
        if ($writer instanceof InMemoryWriter) {
            return $this->moveToInMemory($writer);
        }

        throw new InvalidArgumentException(
            'Invalid writer stream wrapper',
            InvalidArgumentException::INVALID_WRITER_STREAM_WRAPPER
        );
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Move / rename the resource to a file
     *
     * @param FileWriter $writer Target file writer
     * @return FileWriter File writer instance
     * @throws RuntimeException If the resource cannot be moved / renamed
     */
    protected function moveToFile(FileWriter $writer)
    {
        // If a file resource is read
        if ($this->reader instanceof FileReader) {
            // If a copy error occurs
            if (!@rename($this->reader->getFile(), $writer->getFile())) {
                throw new RuntimeException(
                    sprintf(
                        'Could not move / rename "%s" to "%s"',
                        $this->reader->getFile(),
                        $writer->getFile()
                    ),
                    RuntimeException::COULD_NOT_MOVE_FILE_TO_FILE
                );
            }

            // Else: In-memory resource
        } else {
            $writer->write($this->reader->read());
        }

        return $writer;
    }

    /**
     * Move / rename the resource to a in-memory buffer
     *
     * @param InMemoryWriter $writer Target in-memory writer
     * @return InMemoryWriter In-memory writer instance
     */
    protected function moveToInMemory(InMemoryWriter $writer)
    {
        $writer->write($this->reader->read());
        return $writer;
    }
}
