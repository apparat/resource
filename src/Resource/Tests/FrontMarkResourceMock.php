<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Tests
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
 *  this software and associated documentation Fixture (the "Software"), to deal in
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

namespace Apparat\Resource\Tests;

use Apparat\Resource\Domain\Model\Hydrator\HydratorInterface;
use Apparat\Resource\Domain\Model\Part\PartSequence;
use Apparat\Resource\Domain\Model\Resource\AbstractResource;
use Apparat\Resource\Infrastructure\Io\InMemory\Reader;
use Apparat\Resource\Infrastructure\Model\Hydrator\CommonMarkHydrator;
use Apparat\Resource\Infrastructure\Model\Hydrator\FrontMatterHydrator;
use Apparat\Resource\Infrastructure\Model\Hydrator\JsonHydrator;
use Apparat\Resource\Infrastructure\Model\Hydrator\YamlHydrator;

/**
 * Mocked FrontMark file
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Tests
 */
class FrontMarkResourceMock extends AbstractResource
{
    /**
     * FrontMark file constructor
     *
     * @param Reader $reader Reader instance
     */
    public function __construct(Reader $reader = null)
    {
        parent::__construct(
            array(
                [
                    FrontMatterHydrator::FRONTMATTER => [
                        [
                            JsonHydrator::JSON => JsonHydrator::class,
                            YamlHydrator::YAML => YamlHydrator::class
                        ],
                        FrontMatterHydrator::class
                    ],
                    HydratorInterface::STANDARD => CommonMarkHydrator::class,
                ],
                FrontMarkHydratorMock::class
            ),
            $reader
        );
    }

    /**
     * Invalidate the CommonMark part
     */
    public function invalidateCommonMarkPart()
    {
        /** @var PartSequenceMock $sequence */
        $sequence = $this->part();
        $sequence->invalidateCommonMarkPart();
    }

    /**
     * Return the sequence aggregate
     *
     * @return PartSequence Sequence aggregate
     */
    public function getSequence() {
        return $this->part();
    }
}
