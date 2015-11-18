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

namespace Apparat\Resource\Model\Part;

/**
 * Content part
 *
 * @package Apparat\Resource\Model\Part
 */
abstract class ContentPart extends AbstractPart
{
    /**
     * Mime type
     *
     * @var string
     */
    const MIME_TYPE = 'application/octet-stream';

    /**
     * Text content
     *
     * @var string
     */
    protected $_content = '';

    /**
     * Part constructor
     *
     * @param string $content Part content
     */
    public function __construct($content = '')
    {
        $this->_content = $content;
    }

    /**
     * Serialize this file part
     *
     * @return string   File part content
     */
    public function __toString()
    {
        return strval($this->_content);
    }

    /**
     * Return the mime type of this part
     *
     * @param array $subparts Subpart path identifiers
     * @return string   MIME type
     * @throws InvalidArgumentException If there are subpart identifiers given
     */
    public function getMimeType(array $subparts)
    {
        // If there are subpart identifiers given
        if (count($subparts)) {
            throw new InvalidArgumentException(sprintf('Subparts are not allowed (%s)', implode('/', $subparts)),
                InvalidArgumentException::SUBPARTS_NOT_ALLOWED);
        }

        return static::MIME_TYPE;
    }

    /**
     * Set the contents of this part
     *
     * @param string $data Contents
     * @param array $subparts Subpart path identifier
     * @throws InvalidArgumentException If there are subpart identifiers given
     */
    public function set($data, array $subparts)
    {
        // If there are subpart identifiers given
        if (count($subparts)) {
            throw new InvalidArgumentException(sprintf('Subparts are not allowed (%s)', implode('/', $subparts)),
                InvalidArgumentException::SUBPARTS_NOT_ALLOWED);
        }

        $class = get_class($this);
        return new $class($data);
    }

    /**
     * Return the parts content
     *
     * @param array $subparts Subpart path identifiers
     * @return ContentPart Self reference
     * @throws InvalidArgumentException If there are subpart identifiers given
     */
    public function get(array $subparts)
    {
        // If there are subpart identifiers given
        if (count($subparts)) {
            throw new InvalidArgumentException(sprintf('Subparts are not allowed (%s)', implode('/', $subparts)),
                InvalidArgumentException::SUBPARTS_NOT_ALLOWED);
        }

        return $this;
    }
}