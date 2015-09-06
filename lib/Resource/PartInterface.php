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

/**
 * Interface for file parts
 *
 * @package Bauwerk\Resource
 */
interface PartInterface
{
    /**
     * Return the part contents as string
     *
     * @return string           Part contents
     */
    public function __toString();

    /**
     * Return the part content
     *
     * @return string           Part content
     */
    public function getContent();

    /**
     * Set the part content
     *
     * @param string $content Part content
     * @return Part             Self reference
     */
    public function setContent($content);

    /**
     * Return the MIME type
     *
     * @return string           MIME type
     */
    public function getMimeType();

    /**
     * Set the MIME type
     *
     * @param string $mimeType  MIME type
     * @return Part             Self reference
     */
    public function setMimeType($mimeType);

    /**
     * Return the owner file
     *
     * @return FileInterface    Owner file
     */
    public function getOwnerFile();

    /**
     * Set the owner file
     *
     * @param FileInterface $ownerFile      Owner file
     * @return Part                         Self reference
     */
    public function setOwnerFile(FileInterface $ownerFile);

    /**
     * Return the parent part
     *
     * @return PartInterface                Parent part
     */
    public function getParentPart();

    /**
     * Set the parent part
     *
     * @param PartInterface $part           Parent part
     * @return Part                         Self reference
     */
    public function setParentPart(PartInterface $part);
}