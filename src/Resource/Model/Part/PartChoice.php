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

use Apparat\Resource\Model\Hydrator\Hydrator;

/**
 * Part choice
 *
 * @package Apparat\Resource\Model\Part
 */
class PartChoice extends PartAggregate
{
    /**
     * Serialize this file part
     *
     * @return string   File part content
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
    }

    /**
     * Set the contents of a this part
     *
     * @param mixed $data Contents
     * @param array $subparts Subpart path identifiers
     * @return Part Modified part
     */
    public function set($data, array $subparts)
    {
        // TODO: Implement set() method.
    }

    /**
     * Add an occurrence
     *
     * @return void
     */
    protected function _addOccurrence()
    {
        $this->_occurrences[] = null;
    }

    /**
     * Assign data to a particular part
     *
     * @param string $part Part identifier
     * @param string $data Part data
     * @param null|int $occurrence Occurrence to assign the part data to
     */
    public function assign($part, $data, $occurrence = null)
    {
        $occurrence = $this->_prepareAssignment($part, $occurrence);

        /** @var Hydrator $hydrator */
        $hydrator =& $this->_template[$part];
        $this->_occurrences[$occurrence] = [$part => $hydrator->hydrate($data)];
    }
}