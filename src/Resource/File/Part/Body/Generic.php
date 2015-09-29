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

/**
 * Generic file part
 *
 * @package Apparat\Resource\File\Part\Body
 */
class Generic extends Body
{
    /**
     * File part string content
     *
     * @var string
     */
    protected $_content = '';

    /**
     * Reset the part to its default state
     *
     * @return Generic             Self reference
     */
    public function reset()
    {
        $this->_content = '';
        return $this;
    }

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
     * Parse a content string and bring the part model to live
     *
     * @param string $content Content string
     * @return Body                Self reference
     */
    public function parse($content)
    {
        $this->_content = strval($content);
        return $this;
    }
}