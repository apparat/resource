<?php

/**
 * bauwerk-resource
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
 * Part trait
 *
 * @package Bauwerk\Resource
 */
trait PartTrait
{
    /**
     * MIME type
     *
     * @var string
     */
    protected $_mimeType = 'application/octet-stream';

    /**
     * Content
     *
     * @var string
     */
    protected $_content = '';

    /**
     * Return the part contents as string
     *
     * @return string           Part contents
     */
    public function __toString()
    {
        return strval($this->_content);
    }

    /**
     * Return the part content
     *
     * @return string           Part content
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Set the part content
     *
     * @param string $content Part content
     * @return Part             Self reference
     */
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    /**
     * Return the MIME type
     *
     * @return string           MIME type
     */
    public function getMimeType()
    {
        return $this->_mimeType;
    }

    /**
     * Set the MIME type
     *
     * @param string $mimeType MIME type
     * @return Part             Self reference
     */
    public function setMimeType($mimeType)
    {
        $this->_mimeType = $mimeType;
        return $this;
    }
}