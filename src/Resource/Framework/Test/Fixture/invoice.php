<?php

/**
 * resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright � 2015 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright � 2015 Joschi Kuphal <joschi@kuphal.net> / @jkphl
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

return array(
	'invoice' => 34843,
	'date' => 980208000,
	'bill-to' =>
		array(
			'given' => 'Chris',
			'family' => 'Dumars',
			'address' =>
				array(
					'lines' => '458 Walkman Dr.
Suite #292
',
					'city' => 'Royal Oak',
					'state' => 'MI',
					'postal' => 48046,
				),
		),
	'ship-to' =>
		array(
			'given' => 'Chris',
			'family' => 'Dumars',
			'address' =>
				array(
					'lines' => '458 Walkman Dr.
Suite #292
',
					'city' => 'Royal Oak',
					'state' => 'MI',
					'postal' => 48046,
				),
		),
	'product' =>
		array(
			0 =>
				array(
					'sku' => 'BL394D',
					'quantity' => 4,
					'description' => 'Basketball',
					'price' => 450,
				),
			1 =>
				array(
					'sku' => 'BL4438H',
					'quantity' => 1,
					'description' => 'Super Hoop',
					'price' => 2392,
				),
		),
	'tax' => 251.41999999999999,
	'total' => 4443.5200000000004,
	'comments' => 'Late afternoon is best. Backup contact is Nancy Billsmer @ 338-4338.
',
);