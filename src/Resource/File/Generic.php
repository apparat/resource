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

namespace Apparat\Resource\File;

use Apparat\Resource\File;
use Apparat\Resource\File\Part\Container\SequenceInterface;

/**
 * Text file
 *
 * @package     Apparat_Resource
 */
class Generic extends File implements SequenceInterface
{
    /**
     * Default body part classs
     *
     * @var string
     */
    protected $_defaultBodyPartClass = Part\Body\Generic::class;

    /**
     * Constructor
     *
     * @param string $source Source file
     */
    public function __construct($source = null)
    {
        if ($this->_partModel === null) {
            $this->_setPartModel(array(PartInterface::DEFAULT_NAME => $this->_defaultBodyPartClass), 1, 1);
        }

        $this->setSource($source);
    }

    /**
     * Parse a content string and bring the part model to live
     *
     * @param string $content Content string
     * @return Generic       Self reference
     */
    public function parse($content)
    {
        $this->getBody()->parse($content);
        return $this;
    }

    /**
     * Return the default body part
     *
     * @return Part\Body\Generic        Default body part
     */
    public function getBody()
    {
        return $this->getPart(PartInterface::DEFAULT_NAME, 0);
    }
}