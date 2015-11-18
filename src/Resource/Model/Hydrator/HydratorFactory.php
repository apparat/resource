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

/**
 * Hydrator factory
 *
 * @package Apparat\Resource\Model\Hydrator
 */
class HydratorFactory
{
    /**
     * Create a hydrator instance from a configuration array
     *
     * @param array $config Hydrator configuration
     * @return Hydrator Hydrator
     * @throws InvalidArgumentException If the hydrator configuration is invalid
     * @throws InvalidArgumentException If the hydrator model is invalid
     * @throws InvalidArgumentException If the hydrator model class is invalid
     */
    public static function build(array $config)
    {
        // If the configuration is empty
        if (!count($config)) {
            throw new InvalidArgumentException('Invalid hydrator configuration',
                InvalidArgumentException::INVALID_HYDRATOR_CONFIGURATION);

            // Else if it's the short instanciation notation
        } elseif (is_string($config[0]) && is_subclass_of($config[0], SinglepartHydrator::class)) {
            $config[0] = array(Hydrator::STANDARD => $config[0]);

            // Else: Make sure the content model is an array
        } elseif (!is_array($config[0]) || !count($config[0])) {
            throw new InvalidArgumentException('Invalid hydrator content model',
                InvalidArgumentException::INVALID_HYDRATOR_CONTENT_MODEL);
        }

        // If the content model has more than one part
        if (count($config[0]) > 1) {

            // If no multipart hydrator is specified
            if (count($config) < 2) {
                throw new InvalidArgumentException('A multipart hydrator must be specified',
                    InvalidArgumentException::MISSING_MULTIPART_HYDRATOR);

                // Else: if the multipart hydrator is invalid
            } elseif (!strlen(trim($config[1])) || !is_subclass_of(trim($config[1]), MultipartHydrator::class)) {
                throw new InvalidArgumentException(sprintf('Invalid multipart hydrator class "%s"', trim($config[1])),
                    InvalidArgumentException::INVALID_MULTIPART_HYDRATOR_CLASS);

                // Else: Validate the remaining hydrator arguments
            } elseif ((count($config) > 2) && !call_user_func_array(array($config[1], 'validateParameters'),
                    array_slice($config, 2))
            ) {
                throw new InvalidArgumentException('Invalid multipart hydrator parameters',
                    InvalidArgumentException::INVALID_MULTIPART_HYDRATOR_PARAMETERS);
            }

            // Instanciate the multipart hydrator
            $multipartHydrator = trim($config[1]);
            $multipartHydratorParameters = array_slice($config, 2);
            array_unshift($multipartHydratorParameters, $config[0]);
            return new $multipartHydrator(...$multipartHydratorParameters);

            // Else
        } else {
            reset($config[0]);
            $singlepartName = trim(key($config[0]));
            $singlepartHydrator = trim(current($config[0]));

            // If it's not a valid simple part hydrator
            if (!is_subclass_of($singlepartHydrator, SinglepartHydrator::class)) {
                throw new InvalidArgumentException(sprintf('Invalid single part hydrator class "%s"', $singlepartHydrator),
                    InvalidArgumentException::INVALID_SINGLEPART_HYDRATOR_CLASS);
            }

            // Instanciate the simple hydrator
            return new $singlepartHydrator($singlepartName);
        }
    }
}