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
 * Text file part
 *
 * @package Apparat\Resource\File\Part\Body
 */
class Text extends Generic
{
    /**
     * MIME type
     *
     * @var string
     */
    protected $_mimeType = 'text/plain';

    /**
     * Prepend text content
     *
     * @param string $content Text content to be prepended
     * @return Text Self reference
     */
    public function prepend($content)
    {
        $this->_content = $content.$this->_content;
        return $this;
    }

    /**
     * Append text content
     *
     * @param string $content Text content to be appended
     * @return Text Self reference
     */
    public function append($content)
    {
        $this->_content .= $content;
        return $this;
    }
}