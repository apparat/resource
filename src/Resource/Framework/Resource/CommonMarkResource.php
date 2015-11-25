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

namespace Apparat\Resource\Framework\Resource;


use Apparat\Resource\Framework\Hydrator\CommonMarkHydrator;
use Apparat\Resource\Model\SinglePartResource;
use Apparat\Resource\Model\Reader;
use Apparat\Resource\Model\Writer;

/**
 * CommonMark resource
 *
 * @package Apparat\Resource\Framework\Resource
 * @method CommonMarkResource set() set(string $data) Set the content of the resource
 * @method CommonMarkResource setPart() setPart(string $data, string $part = '/') Set the content of the resource
 * @method CommonMarkResource appendPart() appendPart(string $data, string $part = '/') Append content to the resource
 * @method CommonMarkResource prependPart() prependPart(string $data, string $part = '/') Prepend content to the resource
 * @method string getHtmlPart() getHtmlPart(string $part = '/') Get the HTML content of the resource
 * @method string getMimeTypePart() getMimeTypePart(string $part = '/') Get the MIME type of this part
 * @method CommonMarkResource from($src) static from($src, ...$parameters) Instantiate from source
 * @method Writer to() to($target, ...$parameters) Write to target
 */
class CommonMarkResource extends SinglePartResource
{
	/**
	 * Use resource factory and text resource convenience methods and properties
	 */
	use FactoryMethods, TextResourceMethods;

	/**
	 * CommonMark resource constructor
	 *
	 * @param Reader $reader Reader instance
	 */
	public function __construct(Reader $reader = null)
	{
		parent::__construct($reader, CommonMarkHydrator::class);
	}

	/**
	 * Convert the sole CommonMark source to HTML
	 *
	 * @return string CommonMark HTML
	 */
	public function getHtml() {
		return $this->getHtmlPart('/');
	}
}