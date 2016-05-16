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

namespace Apparat\Resource\Ports;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Resource\Domain\Contract\ReaderInterface;
use Apparat\Resource\Domain\Contract\WriterInterface;
use Apparat\Resource\Infrastructure\Io\File\AbstractFileReaderWriter;
use Apparat\Resource\Infrastructure\Io\File\Reader as FileReader;
use Apparat\Resource\Infrastructure\Io\File\Writer as FileWriter;
use Apparat\Resource\Infrastructure\Io\InMemory\AbstractInMemoryReaderWriter;
use Apparat\Resource\Infrastructure\Io\InMemory\Reader as InMemoryReader;
use Apparat\Resource\Infrastructure\Io\InMemory\Writer as InMemoryWriter;
use Apparat\Resource\Infrastructure\Service\Copy;
use Apparat\Resource\Infrastructure\Service\Delete;
use Apparat\Resource\Infrastructure\Service\Move;

/**
 * API tools
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Infrastructure
 */
class Tools
{
    /**
     * Reader classes for stream wrappers
     *
     * @var array
     */
    protected static $reader = array(
        AbstractFileReaderWriter::WRAPPER => FileReader::class,
        AbstractInMemoryReaderWriter::WRAPPER => InMemoryReader::class,
    );

    /**
     * Writer classes for stream wrappers
     *
     * @var array
     */
    protected static $writer = array(
        AbstractFileReaderWriter::WRAPPER => FileWriter::class,
        AbstractInMemoryReaderWriter::WRAPPER => InMemoryWriter::class,
    );

    /**
     * Find and instantiate a reader for a particular source
     *
     * @param string $src Source
     * @param array $parameters Parameters
     * @return null|ReaderInterface  Reader instance
     */
    public static function reader(&$src, array $parameters = array())
    {
        $reader = null;

        // Run through all registered readers
        foreach (self::$reader as $wrapper => $readerClass) {
            $wrapperLength = strlen($wrapper);

            // If this wrapper is used: Instantiate the reader and resource
            if ($wrapperLength ? !strncmp($wrapper, $src, $wrapperLength) : !preg_match("%^[a-z0-9\.]+\:\/\/%", $src)) {
                array_unshift($parameters, substr($src, $wrapperLength));
                $reader = Kernel::create($readerClass, $parameters);
                break;
            }
        }

        return $reader;
    }

    /**
     * Find and instantiate a writer for a particular target
     *
     * @param string $target Target
     * @param array $parameters Parameters
     * @return null|WriterInterface  Writer instance
     */
    public static function writer(&$target, array $parameters = array())
    {
        $writer = null;

        // Run through all registered writer
        foreach (self::$writer as $wrapper => $writerClass) {
            $wrapperLength = strlen($wrapper);

            // If this wrapper is used: Instantiate the reader and resource
            if ($wrapperLength ? !strncmp($wrapper, $target, $wrapperLength) : !preg_match(
                "%^[a-z0-9\.]+\:\/\/%",
                $target
            )
            ) {
                array_unshift($parameters, substr($target, $wrapperLength));
                $writer = Kernel::create($writerClass, $parameters);
                break;
            }
        }

        return $writer;
    }

    /**
     * Copy a resource
     *
     * @param string $src Stream-wrapped source
     * @param array ...$parameters Reader parameters
     * @return Copy Copy handler
     * @throws \Apparat\Resource\Ports\InvalidArgumentException If the reader stream wrapper is invalid
     * @api
     */
    public static function copy($src, ...$parameters)
    {
        $reader = self::reader($src, $parameters);
        if ($reader instanceof ReaderInterface) {
            return Kernel::create(Copy::class, [$reader]);
        }

        throw self::failInvalidReader();
    }

    /**
     * Move / rename a resource
     *
     * @param string $src Stream-wrapped source
     * @param array ...$parameters Reader parameters
     * @return Move move handler
     * @throws \Apparat\Resource\Ports\InvalidArgumentException If the reader stream wrapper is invalid
     * @api
     */
    public static function move($src, ...$parameters)
    {
        $reader = self::reader($src, $parameters);
        if ($reader instanceof ReaderInterface) {
            return Kernel::create(Move::class, [$reader]);
        }

        throw self::failInvalidReader();
    }

    /**
     * Delete a resource
     *
     * @param string $src Stream-wrapped source
     * @param array ...$parameters Reader parameters
     * @return Move move handler
     * @throws \Apparat\Resource\Ports\InvalidArgumentException If the reader stream wrapper is invalid
     * @api
     */
    public static function delete($src, ...$parameters)
    {
        $reader = self::reader($src, $parameters);
        if ($reader instanceof ReaderInterface) {
            /** @var Delete $deleter */
            $deleter = Kernel::create(Delete::class, [$reader]);
            return $deleter();
        }

        throw self::failInvalidReader();
    }

    /**
     * Fail because of an invalid reader stream wrapper
     *
     * @return InvalidArgumentException If the reader stream wrapper is invalid
     */
    protected static function failInvalidReader()
    {
        return new InvalidArgumentException(
            'Invalid reader stream wrapper',
            InvalidArgumentException::INVALID_READER_STREAM_WRAPPER
        );
    }
}
