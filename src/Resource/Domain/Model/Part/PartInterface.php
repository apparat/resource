<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage Apparat\Resource\Domain
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
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

namespace Apparat\Resource\Domain\Model\Part;

use Apparat\Resource\Domain\Model\Hydrator\HydratorInterface;

/**
 * File part interface
 *
 * @package     Apparat\Resource
 * @subpackage Apparat\Resource\Domain
 */
interface PartInterface
{
    /**
     * Serialize this file part
     *
     * @return string   File part content
     */
    public function __toString();

    /**
     * Set the contents of a part
     *
     * @param mixed $data Contents
     * @param array $subparts Subpart identifiers
     * @return PartInterface Modified part
     */
    public function set($data, array $subparts = []);

    /**
     * Return a nested subpart (or the part itself)
     *
     * @param array $subparts Subpart identifiers
     * @return PartInterface Nested subpart (or the part itself)
     */
    public function get(array $subparts = []);

    /**
     * Get the MIME type of this part
     *
     * @return string   MIME type
     */
    public function getMimeType();

    /**
     * Return the associated hydrator
     *
     * @return HydratorInterface Associated hydrator
     */
    public function getHydrator();

    /**
     * Delegate a method call to a subpart
     *
     * @param string $method Method nae
     * @param array $subparts Subpart identifiers
     * @param array $arguments Method arguments
     * @return mixed Method result
     */
    public function delegate($method, array $subparts, array $arguments);
}
