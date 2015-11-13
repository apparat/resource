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

namespace ApparatTest;

use Apparat\Resource\Framework\Hydrator\TextHydrator;
use Apparat\Resource\Model\Hydrator\Hydrator;
use Apparat\Resource\Model\Hydrator\HydratorFactory;
use Apparat\Resource\Model\Hydrator\InvalidArgumentException;

/**
 * Hydrator tests
 *
 * @package ApparatTest
 */
class HydratorTest extends TestBase
{

    /**
     * Test an invalid hydrator configuraton
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1447019565
     */
    public function testInvalidHydratorConfig() {
        $hydrator = HydratorFactory::build([]);
    }

    /**
     * Test an invalid hydrator content model configuraton
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1447020287
     */
    public function testInvalidHydratorContentModel() {
        $hydrator = HydratorFactory::build([[]]);
    }

    /**
     * Test a missing multipart hydrator
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1447107537
     */
    public function testMissingMultipartHydrator() {
        $hydrator = HydratorFactory::build([[1, 2]]);
    }

    /**
     * Test an empty multipart hydrator class
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1447107792
     */
    public function testEmptyMultipartHydratorClass() {
        $hydrator = HydratorFactory::build([[1, 2], '']);
    }

    /**
     * Test an invalid multipart hydrator class
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1447107792
     */
    public function testInvalidMultipartHydratorClass() {
        $hydrator = HydratorFactory::build([[1, 2], stdClass::class]);
    }

    /**
     * Test invalid multipart hydrator parameters
     */
    public function testInvalidMultipartHydratorParameters() {
        // TODO: Implement
    }

    /**
     * Test an invalid singlepart hydrator class
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionCode 1447110065
     */
    public function testInvalidSinglepartHydratorClass() {
        $hydrator = HydratorFactory::build([[Hydrator::STANDARD => stdClass::class]]);
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
        $textHydrator = HydratorFactory::build([[Hydrator::STANDARD => TextHydrator::class]]);
        $this->assertInstanceOf(TextHydrator::class, $textHydrator);
    }

    /**
     * Test the text hydrator name
     */
    public function testTextHydratorName()
    {
        $textHydrator = HydratorFactory::build([[Hydrator::STANDARD => TextHydrator::class]]);
        $this->assertEquals(Hydrator::STANDARD, $textHydrator->getName());
    }

    /**
     * Test subhydrators on a single part hydrator
     *
     * @expectedException \Apparat\Resource\Model\Part\InvalidArgumentException
     * @expectedExceptionCode 1447365624
     */
    public function testTextHydratorSub()
    {
        $textHydrator = HydratorFactory::build([[Hydrator::STANDARD => TextHydrator::class]]);
        $textHydrator->getSub(['a', 'b', 'c']);
    }
}