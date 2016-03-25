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

namespace Apparat\Resource\Infrastructure\Model\Part;

use Apparat\Resource\Domain\Model\Part\AbstractContentPart;

/**
 * JSON resource part
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Infrastructure
 */
class JsonPart extends AbstractContentPart
{
    /**
     * Media type
     *
     * @var string
     */
    const MEDIA_TYPE = 'application/json';

    /**
     * Return the unserialized JSON source
     *
     * @return array Unserialized JSON data
     */
    public function getData()
    {
        $data = array();

        if (strlen($this->content)) {
            $data = json_decode($this->content, true);
        }

        return $data;
    }

    /**
     * Set JSON data
     *
     * @param array $data New data
     * @return JsonPart Self reference
     */
    public function setData(array $data)
    {
        return $this->set(json_encode($data, JSON_PRETTY_PRINT));
    }
}
