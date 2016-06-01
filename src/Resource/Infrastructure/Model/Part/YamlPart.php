<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Infrastructure
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

namespace Apparat\Resource\Infrastructure\Model\Part;

use Apparat\Resource\Domain\Model\Part\AbstractContentPart;
use Symfony\Component\Yaml\Yaml;

/**
 * YAML resource part
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Infrastructure
 * @see http://yaml.org/spec/1.2/spec.pdf
 * @see http://yaml.org/spec/1.2/spec.html
 */
class YamlPart extends AbstractContentPart
{
    /**
     * Media type
     *
     * @var string
     */
    const MEDIA_TYPE = 'text/x-yaml';
    /**
     * Document end marker
     *
     * @var string
     */
    const DOCUMENT_END = '...';

    /**
     * Return the unserialized YAML source
     *
     * @return array Unserialized YAML data
     */
    public function getData()
    {
        $data = array();

        if (strlen($this->content)) {
            $defaultTimezone = date_default_timezone_get();
            date_default_timezone_set('UTC');
            $data = Yaml::parse($this->content, Yaml::PARSE_DATETIME);
            date_default_timezone_set($defaultTimezone);
        }

        return $data;
    }

    /**
     * Set YAML data
     *
     * @param array $data New data
     * @return YamlPart Self reference
     */
    public function setData(array $data)
    {
        $defaultTimezone = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $dataStr = Yaml::dump($data);
        date_default_timezone_set($defaultTimezone);
        return $this->set($dataStr);
    }
}
