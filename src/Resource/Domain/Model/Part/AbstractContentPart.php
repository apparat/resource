<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage Apparat\Resource\Domain
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

namespace Apparat\Resource\Domain\Model\Part;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Resource\Domain\Model\Hydrator\AbstractSinglepartHydrator;

/**
 * Content part
 *
 * @package     Apparat\Resource
 * @subpackage Apparat\Resource\Domain
 */
abstract class AbstractContentPart extends AbstractPart
{
    /**
     * Media type
     *
     * @var string
     */
    const MEDIA_TYPE = 'application/octet-stream';

    /**
     * Text content
     *
     * @var string
     */
    protected $content = '';

    /**
     * Part constructor
     *
     * @param AbstractSinglepartHydrator $hydrator Associated hydrator
     * @param string $content Part content
     */
    public function __construct(AbstractSinglepartHydrator $hydrator, $content = '')
    {
        parent::__construct($hydrator);
        $this->content = $content;
    }

    /**
     * Serialize this file part
     *
     * @return string   File part content
     */
    public function __toString()
    {
        return strval($this->content);
    }

    /**
     * Return the media type of this part
     *
     * @return string   Media type
     */
    public function getMediaType()
    {
        return static::MEDIA_TYPE;
    }

    /**
     * Set the contents of a part
     *
     * @param string $data Contents
     * @param array $subparts Subparts
     * @return AbstractContentPart New content part
     */
    public function set($data, array $subparts = [])
    {
        unset($subparts);
        $class = get_class($this);
        return Kernel::create($class, [$this->hydrator, $data]);
    }

    /**
     * Return the part itself
     *
     * Content parts don't have subparts, so this method will always return the part itself
     *
     * @param array $subparts Subpart identifiers
     * @return PartInterface Self reference
     */
    public function get(array $subparts = [])
    {
        unset($subparts);
        return $this;
    }

    /**
     * Delegate a method call to a subpart
     *
     * @param string $method Method nae
     * @param array $subparts Subpart identifiers
     * @param array $arguments Method arguments
     * @return mixed Method result
     * @throws InvalidArgumentException If there are subpart identifiers
     */
    public function delegate($method, array $subparts, array $arguments)
    {
        // If there are subpart identifiers given
        if (count($subparts)) {
            throw new InvalidArgumentException(
                sprintf('Subparts are not allowed (%s)', implode('/', $subparts)),
                InvalidArgumentException::SUBPARTS_NOT_ALLOWED
            );
        }

        return parent::delegate($method, $subparts, $arguments);
    }
}
