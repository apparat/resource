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

use Apparat\Dev\Tests\AbstractTest;
use Apparat\Resource\Domain\Factory\HydratorFactory;
use Apparat\Resource\Domain\Model\Hydrator\HydratorInterface;
use Apparat\Resource\Domain\Model\Hydrator\InvalidArgumentException;
use Apparat\Resource\Infrastructure\Model\Hydrator\TextHydrator;

/**
 * Hydrator tests
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Tests
 */
class HydratorTest extends AbstractTest
{
    /**
     * Test an invalid hydrator configuraton
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1447019565
     */
    public function testInvalidHydratorConfig()
    {
        HydratorFactory::build([]);
    }

    /**
     * Test an invalid hydrator content model configuraton
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1447020287
     */
    public function testInvalidHydratorContentModel()
    {
        HydratorFactory::build([[]]);
    }

    /**
     * Test an invalid subhydrator name
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1447364401
     */
    public function testInvalidSubhydratorName()
    {
        HydratorFactory::build([['~' => true]]);
    }

    /**
     * Test an invalid singlepart hydrator class
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1447110065
     */
    public function testInvalidSinglepartHydratorClass()
    {
        HydratorFactory::build([[HydratorInterface::STANDARD => \stdClass::class]]);
    }

    /**
     * Test the text hydrator (short form)
     */
    public function testTextHydratorShort()
    {
        $textHydrator = HydratorFactory::build([TextHydrator::class]);
        $this->assertInstanceOf(TextHydrator::class, $textHydrator);
    }

    /**
     * Test the text hydrator (verbose form)
     */
    public function testTextHydratorVerbose()
    {
        $textHydrator = HydratorFactory::build([[HydratorInterface::STANDARD => TextHydrator::class]]);
        $this->assertInstanceOf(TextHydrator::class, $textHydrator);
    }

    /**
     * Test the text hydrator name
     */
    public function testTextHydratorName()
    {
        $textHydrator = HydratorFactory::build([[HydratorInterface::STANDARD => TextHydrator::class]]);
        $this->assertEquals(HydratorInterface::STANDARD, $textHydrator->getName());
    }
}
