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

use Apparat\Resource\Domain\Contract\ReaderInterface;
use Apparat\Resource\Domain\Contract\WriterInterface;
use Apparat\Resource\Domain\Model\Hydrator\Hydrator;
use Apparat\Resource\Domain\Model\Part\AbstractContentPart;
use Apparat\Resource\Domain\Model\Part\PartChoice;
use Apparat\Resource\Domain\Model\Resource\AbstractResource;
use Apparat\Resource\Framework\Hydrator\CommonMarkHydrator;
use Apparat\Resource\Framework\Hydrator\FrontMarkHydrator;
use Apparat\Resource\Framework\Hydrator\FrontMatterHydrator;
use Apparat\Resource\Framework\Hydrator\JsonHydrator;
use Apparat\Resource\Framework\Hydrator\YamlHydrator;

/**
 * FrontMark resource (CommonMark resource with YAML or JSON front matter)
 *
 * @package Apparat\Resource\Framework\Resource
 * @method FrontMarkResource setPart() setPart(array $data, string $part = '/') Set the content of the resource
 * @method FrontMarkResource appendPart() appendPart(string $data, string $part = '/') Append content to the resource
 * @method FrontMarkResource prependPart() prependPart(string $data, string $part = '/') Prepend content to the resource
 * @method string getHtmlPart() getHtmlPart(string $part = '/') Get the HTML content of the resource
 * @method array getDataPart() getDataPart(string $part = '/') Get the YAML / JSON front matter data of the resource
 * @method FrontMarkResource setDataPart() setDataPart(array $data, string $part = '/') Set the YAML / JSON front matter data of the resource
 * @method string getMimeTypePart() getMimeTypePart(string $part = '/') Get the MIME type of this part
 * @method FrontMarkResource from($src) static from($src, ...$parameters) Instantiate from source
 * @method WriterInterface to() to($target, ...$parameters) Write to target
 */
class FrontMarkResource extends AbstractResource
{
	/**
	 * Use resource factory methods and properties
	 */
	use FactoryMethods;

	/**
	 * FrontMark resource constructor
	 *
	 * @param ReaderInterface $reader Reader instance
	 */
	public function __construct(ReaderInterface $reader = null)
	{
		parent::__construct($reader, array(
			[
				FrontMatterHydrator::FRONTMATTER => [
					[
						JsonHydrator::JSON => JsonHydrator::class,
						YamlHydrator::YAML => YamlHydrator::class
					],
					FrontMatterHydrator::class
				],
				Hydrator::STANDARD => CommonMarkHydrator::class,
			],
			FrontMarkHydrator::class
		));
	}

	/**
	 * Set the content of the sole part
	 *
	 * @param string $data Content
	 * @return FrontMarkResource Self reference
	 */
	public function set($data)
	{
		return $this->setPart($data, '/0/'.Hydrator::STANDARD);
	}

	/**
	 * Return the sole part's content
	 *
	 * @return string Part content
	 */
	public function get()
	{
		return $this->getPart('/0/'.Hydrator::STANDARD);
	}

	/**
	 * Append content to the sole part
	 *
	 * @param string $data Contents
	 * @return FrontMarkResource New part
	 */
	public function append($data)
	{
		return $this->appendPart($data, '/0/'.Hydrator::STANDARD);
	}

	/**
	 * Prepend content to the sole part
	 *
	 * @param string $data Contents
	 * @return FrontMarkResource New text part
	 */
	public function prepend($data)
	{
		return $this->prependPart($data, '/0/'.Hydrator::STANDARD);
	}

	/**
	 * Convert the sole CommonMark source to HTML
	 *
	 * @return string CommonMark HTML
	 */
	public function getHtml()
	{
		return $this->getHtmlPart('/0/'.Hydrator::STANDARD);
	}

	/**
	 * Return the unserialized sole data content
	 *
	 * @return array Unserialized data content
	 */
	public function getData()
	{
		return $this->getDataPart('/0/'.FrontMatterHydrator::FRONTMATTER.'/0/'.PartChoice::WILDCARD);
	}

	/**
	 * Set the sole data content
	 *
	 * @param array $data New data
	 * @return AbstractContentPart Self reference
	 */
	public function setData(array $data)
	{
		return $this->setDataPart($data, '/0/'.FrontMatterHydrator::FRONTMATTER.'/0/'.PartChoice::WILDCARD);
	}


}