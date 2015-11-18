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

namespace Apparat\Resource\Framework\Hydrator;

use Apparat\Resource\Model\Hydrator\SequenceHydrator;
use Apparat\Resource\Model\Part\Part;
use Apparat\Resource\Model\Part\PartAggregate;

/**
 * FrontMark part hydrator (combination of YAML / JSON front matter and CommonMark part)
 *
 * @package Apparat\Resource\Framework\Hydrator
 */
class FrontMarkHydrator extends SequenceHydrator
{
    /**
     * Translate data to a YAML file part
     *
     * @param string $data Part data
     * @return PartAggregate Part aggregate
     */
    public function hydrate($data)
    {
        $aggregate = parent::hydrate(null);
        return $aggregate;
    }

    /**
     * Serialize a file part
     *
     * @param Part $part File part
     * @return string Serialized file part
     */
    public function dehydrate(Part $part)
    {
        // TODO: Implement dehydrate() method.
    }
}