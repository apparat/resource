<?php

/**
 * apparat/resource
 *
 * @category    Jkphl
 * @package     Jkphl_apparat/resource
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
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

namespace Apparat\Resource\Tests;

use Apparat\Dev\Tests\AbstractTest;

/**
 * Abstract data tests
 *
 * @package Apparat\Resource\Tests
 * @subpackage Apparat\Resource\Tests
 */
abstract class AbstractDataTest extends AbstractTest
{
    /**
     * Create expected invoice data
     *
     * @return array Expected data
     */
    protected function getExpectedInvoiceData()
    {
        // Prepare modified expected data
        $expectedData = include __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'invoice.php';
        $expectedData['date'] = time();
        $expectedData['bill-to']['given'] = 'John';
        $expectedData['bill-to']['family'] = 'Doe';
        $expectedData['product'][] = [
            'sku' => 'ABC123',
            'quantity' => 1,
            'description' => 'Dummy',
            'price' => 123
        ];
        unset($expectedData['comments']);
        return $expectedData;
    }
}
