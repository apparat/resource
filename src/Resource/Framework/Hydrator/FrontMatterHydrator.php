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

namespace Apparat\Resource\Framework\Hydrator;

use Apparat\Resource\Framework\Part\YamlPart;
use Apparat\Resource\Domain\Model\Hydrator\ChoiceHydrator;
use Apparat\Resource\Domain\Model\Part\Part;

/**
 * FrontMark part hydrator (combination of YAML / JSON front matter and CommonMark part)
 *
 * @package Apparat\Resource\Framework\Hydrator
 */
class FrontMatterHydrator extends ChoiceHydrator
{
	/**
	 * Front matter part identifier
	 *
	 * @var string
	 */
	const FRONTMATTER = 'frontmatter';

	/**
	 * Translate data to a resource part
	 *
	 * @param string $data Part data
	 * @return Part Resource part
	 */
	public function hydrate($data)
	{
		$aggregate = parent::hydrate(null);

		// If it's a JSON front matter
		if (!strncmp('{', trim($data), 1)) {
			$aggregate->assign(JsonHydrator::JSON, $data, 0);

			// Else: Assign as YAML front matter
		} else {
			$aggregate->assign(YamlHydrator::YAML, $data, 0);
		}

		return $aggregate;
	}

	/**
	 * Dehydrate a single part with a particular subhydrator
	 *
	 * @param string $subhydrator Subhydrator name
	 * @param Part $part Part instance
	 * @return string Dehydrated part
	 */
	protected function _dehydratePart($subhydrator, Part $part)
	{
		$content = trim(parent::_dehydratePart($subhydrator, $part));

		// If it's a YAML part: Terminate if necessary
		if (strlen($content) && ($part instanceof YamlPart) && !preg_match('%\R' . preg_quote(YamlPart::DOCUMENT_END) . '$%',
				$content)
		) {
			$content .= PHP_EOL.YamlPart::DOCUMENT_END;
		}

		return $content . PHP_EOL;
	}
}