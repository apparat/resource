<?php

/**
 * resource
 *
 * @category    Apparat
 * @package     Apparat_Resource
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

namespace Apparat\Resource\Domain\File;

use Apparat\Resource\Domain\Container\ContainerInterface;
use Apparat\Resource\Domain\Resource\ResourceInterface;
use Apparat\Resource\Domain\File\Exception\InvalidArgumentException;
use Apparat\Resource\Domain\File\Exception\RuntimeException;

/**
 * Abstract file resource interface
 *
 * @package     Apparat_Resource
 */
interface FileInterface extends ContainerInterface, ResourceInterface
{
    /**
     * Constructor
     *
     * @param string $source                    Source file
     */
    public function __construct($source = null);

    /**
     * Serialize this file
     *
     * @return string                           Serialized file contents
     */
    public function __toString();

    /**
     * Return the source of this file
     *
     * @return string
     */
    public function getSource();

    /**
     * Set the source of this file
     *
     * @param string $source Source
     * @return File                             Self reference
     */
    public function setSource($source);

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
    public function save($target = null, $createDirectories = false, $overwrite = false);
}