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

use Apparat\Resource\Application\Utility;
use Apparat\Resource\Domain\Model\Hydrator\Hydrator;
use Apparat\Resource\Domain\Model\Hydrator\SequenceHydrator;
use Apparat\Resource\Domain\Model\Part\PartAggregateInterface;

/**
 * FrontMark part hydrator (combination of YAML / JSON front matter and CommonMark part)
 *
 * @package Apparat\Resource\Framework\Hydrator
 */
class FrontMarkHydrator extends SequenceHydrator
{
	/**
	 * Translate data to a YAML resource part
	 *
	 * @param string $data Part data
	 * @return PartAggregateInterface Part aggregate
	 */
	public function hydrate($data)
	{
		$aggregate = parent::hydrate(null);

		// Prepare and split the frontmatter data
		$data = Utility::stripBom($data);
		$frontMatter = '';
		$commonMarkBody = $data;

		// Check for a YAML document end marker
		$yamlParts = preg_split("%\R(\.\.\.)\R%", $data, 2);
		if (count($yamlParts) > 1) {
			$frontMatter = array_shift($yamlParts);
			$commonMarkBody = implode('...', $yamlParts);

			// Else: Check for JSON front matter
		} elseif (!strncmp('{', trim($data), 1)) {
			list($frontMatter, $commonMarkBody) = $this->_extractJsonFrontmatter($data);
		}

		// Assign the front matter and body part
		$aggregate->assign(FrontMatterHydrator::FRONTMATTER, $frontMatter, 0);
		$aggregate->assign(Hydrator::STANDARD, $commonMarkBody, 0);

		return $aggregate;
	}

	/**
	 * Extract the JSON front matter from a string
	 *
	 * @param string $data String
	 * @return array    JSON front matter and string remainder
	 */
	protected function _extractJsonFrontmatter($data)
	{
		$jsonFrontMatter = '';
		$remainder = $data;

		// Try decoding the whole string first
		if (is_object(@json_decode(trim($data)))) {
			$jsonFrontMatter = trim($data);
			$remainder = '';

			// Else: If the data contains potential JSON closing brackets
		} elseif (preg_match_all("%\}[\s\R]*[^\,\}\]]%", $data, $closingBrackets, PREG_OFFSET_CAPTURE)) {
			foreach ($closingBrackets[0] as $closingBracket) {
				$jsonData = substr($data, 0, $closingBracket[1] + 1);
				if (is_object(@json_decode($jsonData))) {
					$jsonFrontMatter = trim($jsonData);
					$remainder = ltrim(substr($data, $closingBracket[1] + 1));
					break;
				}
			}
		}

		return [$jsonFrontMatter, $remainder];
	}
}