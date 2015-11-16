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

namespace Apparat\Resource\Framework\Part;

use Apparat\Resource\Model\Part\ContentPart;
use Apparat\Resource\Model\Part\InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

/**
 * YAML file part
 *
 * @package Apparat\Resource\Framework\Part
 * @see http://yaml.org/spec/1.2/spec.pdf
 */
class YamlPart extends ContentPart
{
    /**
     * Mime type
     *
     * @var string
     */
    const MIME_TYPE = 'text/x-yaml';

    /**
     * Return the unserialized YAML source
     *
     * @param array $subparts Subpart path identifiers
     * @return array Unserialized YAML data
     * @throws InvalidArgumentException If there are subpart identifiers given
     */
    public function getData(array $subparts)
    {

        // If there are subpart identifiers given
        if (count($subparts)) {
            throw new InvalidArgumentException(sprintf('Subparts are not allowed (%s)', implode('/', $subparts)),
                InvalidArgumentException::SUBPARTS_NOT_ALLOWED);
        }

        $data = array();

        if (strlen($this->_content)) {

            $data =  Yaml::parse($this->_content);
        }

        return $data;
    }

    /**
     * Merge data
     *
     * @param array $data New data
     * @param array $subparts Subpart path identifiers
     * @return YamlPart Self reference
     * @throws InvalidArgumentException If there are subpart identifiers given
     */
    public function mergeData(array $data, array $subparts)
    {
        $orig = $this->getData($subparts);
        $data = $this->_mergeArraysRecursive($orig, $data);
        return $this->set(Yaml::dump($data), $subparts);
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Recursively merge two arrays (and potentially delete values)
     *
     * @param array $array1 Original array
     * @param array $array2 Overlay array
     * @param bool $unset Unset values
     * @return array Merged array
     */
    protected function _mergeArraysRecursive($array1, $array2, $unset = false)
    {
        $merged = $array1;
        foreach ($array2 as $key => $value) {
            if (array_key_exists($key, $merged) && is_array($merged[$key]) && is_array($value)) {
                $merged[$key] = $this->_mergeArraysRecursive($merged[$key], $value, $unset);
            } elseif ($unset) {
                unset($merged[$key]);
            } else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }
}