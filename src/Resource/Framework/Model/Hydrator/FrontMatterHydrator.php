<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
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

namespace Apparat\Resource\Framework\Model\Hydrator;

use Apparat\Resource\Domain\Model\Hydrator\AbstractChoiceHydrator;
use Apparat\Resource\Domain\Model\Part\PartInterface;
use Apparat\Resource\Framework\Model\Part\YamlPart;

/**
 * FrontMark part hydrator (combination of YAML / JSON front matter and CommonMark part)
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
 */
class FrontMatterHydrator extends AbstractChoiceHydrator
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
	 * @return PartInterface Resource part
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
	 * @param PartInterface $part Part instance
	 * @return string Dehydrated part
	 */
	protected function _dehydratePart($subhydrator, PartInterface $part)
	{
		$content = trim(parent::_dehydratePart($subhydrator, $part));

		// If it's a YAML part: Terminate if necessary
		if (strlen($content) && ($part instanceof YamlPart) && !preg_match('%\R'.preg_quote(YamlPart::DOCUMENT_END).'$%',
				$content)
		) {
			$content .= "\n".YamlPart::DOCUMENT_END;
		}

		return $content."\n";
	}
}