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

use Apparat\Resource\Domain\Model\Hydrator\HydratorInterface;

/**
 * Part choice
 *
 * @package     Apparat\Resource
 * @subpackage Apparat\Resource\Domain
 */
class PartChoice extends AbstractPartAggregate
{
    /**
     * Part name wildcard
     *
     * @var string
     */
    const WILDCARD = '*';

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

        /** @var HydratorInterface $hydrator */
        $hydrator =& $this->template[$part];
        $this->occurrences[$occurrence] = [$part => $hydrator->hydrate($data)];
    }

    /**
     * Add an occurrence
     *
     * @return void
     */
    protected function _addOccurrence()
    {
        $this->occurrences[] = null;
    }

    /**
     * Test if a particular part identifier is known for a particular occurrence
     *
     * @param int $occurrence Occurrence index
     * @param string $part Part identifier
     * @return bool Is known part identifier
     */
    protected function _isKnownPartIdentifier($occurrence, $part)
    {
        return parent::_isKnownPartIdentifier(
            $occurrence,
            $part
        ) || (($part == self::WILDCARD) && count($this->occurrences[$occurrence]));
    }

    /**
     * Return a particular part of a particular occurrence
     *
     * @param int $occurrence Occurrence index
     * @param string $part Part identifier
     * @return PartInterface Part instance
     */
    protected function _getOccurrencePart(&$occurrence, &$part)
    {
        reset($this->occurrences[$occurrence]);

        if ($part == self::WILDCARD) {
            $part = key($this->occurrences[$occurrence]);
        }

        return parent::_getOccurrencePart($occurrence, $part);
    }
}
