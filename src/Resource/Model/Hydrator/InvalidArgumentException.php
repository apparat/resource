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

namespace Apparat\Resource\Model\Hydrator;


class InvalidArgumentException extends \InvalidArgumentException
{
	/**
	 * Invalid hydrator configuration
	 *
	 * @var int
	 */
	const INVALID_HYDRATOR_CONFIGURATION = 1447019565;
	/**
	 * Invalid hydrator content model
	 *
	 * @var int
	 */
	const INVALID_HYDRATOR_CONTENT_MODEL = 1447020287;
	/**
	 * Missing multipart hydrator
	 *
	 * @var int
	 */
	const MISSING_MULTIPART_HYDRATOR = 1447107537;
	/**
	 * Invalid single part hydrator class
	 *
	 * @var int
	 */
	const INVALID_SINGLEPART_HYDRATOR_CLASS = 1447110065;
	/**
	 * Invalid multipart hydrator class
	 *
	 * @var int
	 */
	const INVALID_MULTIPART_HYDRATOR_CLASS = 1447107792;
	/**
	 * Invalid multipart subhydrator class
	 *
	 * @var int
	 */
	const INVALID_MULTIPART_SUBHYDRATOR_CLASS = 1447868909;
	/**
	 * Invalid multipart hydrator parameters
	 *
	 * @var int
	 */
	const INVALID_MULTIPART_HYDRATOR_PARAMETERS = 1447109790;
	/**
	 * Invalid multipart hydrator parameter count
	 *
	 * @var int
	 */
	const INVALID_MULTIPART_HYDRATOR_PARAMETER_COUNT = 1447866302;
	/**
	 * Invalid part configuration
	 *
	 * @var int
	 */
	const INVALID_PART_CONFIGURATION = 1447021916;
	/**
	 * Invalid part class
	 *
	 * @var int
	 */
	const INVALID_PART_CLASS = 1447022020;
	/**
	 * Too few subpart identifiers
	 *
	 * @var int
	 */
	const TOO_FEW_SUBPART_IDENTIFIERS = 1448056671;
	/**
	 * Invalid part class for dehydration
	 *
	 * @var int
	 */
	const INVALID_DEHYDRATION_PART_CLASS = 1448107001;
}