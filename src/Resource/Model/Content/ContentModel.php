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

namespace Apparat\Resource\Model\Content;

use Apparat\Resource\Model\Part\Part;

/**
 * Content model interface
 *
 * @package Apparat\Resource\Model\Content
 */
abstract class ContentModel implements Part
{
    /**
     * Content model
     *
     * @var array
     */
    protected $_content = [];
    /**
     * Minimum occurrences
     *
     * @var int
     */
    protected $_miniumOccurrences = 1;
    /**
     * Maximum occurrences
     *
     * @var int
     */
    protected $_maximumOccurrences = 1;
    /**
     * Unbound occurrences
     *
     * @var int
     */
    const UNBOUND = -1;

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Constructor
     *
     * @param array $content        Content model
     * @param int $minOccurrences   Minimum occurrences
     * @param int $maxOccurrences   Maximmum occurrences
     * @throws InvalidArgumentException When the minimum occurrences are invalid
     */
    public function __construct(array $content, $minOccurrences = 1, $maxOccurrences = 1) {

        // Content model
        foreach ($content as $key => $config) {

            // If it's a content aggregate: Instanciate it
            if (is_array($config)) {
                $this->_content[$key] = self::fromConfig($config);

                // Else: If it's a string
            } elseif (is_string($config)) {

                // If it's not a valid part class
                if (!($config instanceof Part)) {
                    throw new InvalidArgumentException(sprintf('Invalid part class "%s"', $config), InvalidArgumentException::INVALID_PART_CLASS);

                    // Else: Instanciate
                } else {
                    $this->_content[$key] = $config;
                }

            } else {
                throw new InvalidArgumentException('Invalid part configuration', InvalidArgumentException::INVALID_PART_CONFIGURATION);
            }
        }

        // Minimum occurrences
        $minOccurrences = intval($minOccurrences);
        if ($minOccurrences < 0) {
            throw new InvalidArgumentException(sprintf('Invalid minium occurrences "%s"', $minOccurrences), InvalidArgumentException::INVALID_MINIMUM_OCCURRENCES);
        }
        $this->_miniumOccurrences = $minOccurrences;

        // Maximum occurrences
        $maxOccurrences = intval($maxOccurrences);
        if (($maxOccurrences < $minOccurrences) && ($maxOccurrences != self::UNBOUND)) {
            throw new InvalidArgumentException(sprintf('Invalid maximum occurrences "%s"', $maxOccurrences), InvalidArgumentException::INVALID_MAXIMUM_OCCURRENCES);
        }
        $this->_maximumOccurrences = $maxOccurrences;
    }

    /*******************************************************************************
     * STATIC METHODS
     *******************************************************************************/

    /**
     * Instanciate a content model from an array configuration
     *
     * @param array $config Configuration
     * @return ContentModel
     * @throws InvalidArgumentException If the content model configuration is invalid
     * @throws InvalidArgumentException If the content model is invalid
     * @throws InvalidArgumentException If the content model class is invalid
     */
    public static function fromConfig(array $config)
    {

        // If the configuration is empty
        if (!count($config)) {
            throw new InvalidArgumentException('Invalid content model configuration',
                InvalidArgumentException::INVALID_CONTENT_MODEL_CONFIGURATION);

            // Else: Make sure the content model is an array
        } elseif (!is_array($config[0]) || !count($config[0])) {
            throw new InvalidArgumentException('Invalid content model',
                InvalidArgumentException::INVALID_CONTENT_MODEL);
        }

        // Add the sequence model as default
        if (count($config) < 2) {
            $config[1] = Sequence::class;

            // Else: Make sure it's a valid content model
        } elseif (!strlen($config[1]) || !($config[1] instanceof self)) {
            throw new InvalidArgumentException(sprintf('Invalid content model class "%s"', $config[1]),
                InvalidArgumentException::INVALID_CONTENT_MODEL_CLASS);
        }

        // Add 1 as default minimum and maximum occurences
        $config = array_pad($config, 4, 1);

        // Instanciate and return the content model class
        return new $config[1]($config[0], $config[2], $config[3]);
    }
}