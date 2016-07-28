<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage Apparat\Resource\Domain
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

namespace Apparat\Resource\Domain\Factory;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Resource\Domain\Model\Hydrator\AbstractMultipartHydrator;
use Apparat\Resource\Domain\Model\Hydrator\AbstractSinglepartHydrator;
use Apparat\Resource\Domain\Model\Hydrator\HydratorInterface;
use Apparat\Resource\Domain\Model\Hydrator\InvalidArgumentException;
use Apparat\Resource\Domain\Model\Part\AbstractPart;

/**
 * Hydrator factory
 *
 * @package     Apparat\Resource
 * @subpackage Apparat\Resource\Domain
 */
class HydratorFactory
{
    /**
     * Create a hydrator instance from a configuration array
     *
     * @param array $config Hydrator configuration
     * @return HydratorInterface Hydrator
     * @throws InvalidArgumentException If the hydrator configuration is invalid
     * @throws InvalidArgumentException If the hydrator model is invalid
     * @throws InvalidArgumentException If the hydrator model class is invalid
     */
    public static function build(array $config)
    {
        // If the configuration is empty
        if (!count($config)) {
            throw new InvalidArgumentException(
                'Invalid hydrator configuration',
                InvalidArgumentException::INVALID_HYDRATOR_CONFIGURATION
            );

            // Else if it's the short instantiation notation
        } elseif (is_string($config[0]) &&
            (new \ReflectionClass($config[0]))->isSubclassOf(AbstractSinglepartHydrator::class)
        ) {
            $config[0] = array(HydratorInterface::STANDARD => $config[0]);

            // Else: Make sure the content model is an array
        } elseif (!is_array($config[0]) || !count($config[0])) {
            throw new InvalidArgumentException(
                'Invalid hydrator content model',
                InvalidArgumentException::INVALID_HYDRATOR_CONTENT_MODEL
            );
        }

        // Run through all subhydrators
        foreach (array_keys($config[0]) as $subhydratorName) {
            AbstractPart::validatePartIdentifier($subhydratorName);
        }

        // If the content model has more than one part
        if (count($config[0]) > 1) {
            return self::buildMultipart($config);
        }

        // Build a single part hydrator
        return self::buildSingle($config);
    }

    /**
     * Build a multipart hydrator
     *
     * @param array $config Hydrator configuration
     * @return HydratorInterface Hydrator
     */
    protected static function buildMultipart(array $config)
    {
        // If no multipart hydrator is specified
        if (count($config) < 2) {
            throw new InvalidArgumentException(
                'A multipart hydrator must be specified',
                InvalidArgumentException::MISSING_MULTIPART_HYDRATOR
            );

            // Else: if the multipart hydrator is invalid
        } elseif (!strlen(trim($config[1])) ||
            !(new \ReflectionClass(trim($config[1])))->isSubclassOf(AbstractMultipartHydrator::class)
        ) {
            throw new InvalidArgumentException(
                sprintf('Invalid multipart hydrator class "%s"', trim($config[1])),
                InvalidArgumentException::INVALID_MULTIPART_HYDRATOR_CLASS
            );

            // Else: Validate the remaining hydrator arguments
        } elseif ((count($config) > 2) &&
            !call_user_func_array(
                array($config[1], 'validateParameters'),
                array_slice($config, 2)
            )
        ) {
            throw new InvalidArgumentException(
                'Invalid multipart hydrator parameters',
                InvalidArgumentException::INVALID_MULTIPART_HYDRATOR_PARAMETERS
            );
        }

        // Run through all multipart subhydrators
        foreach ($config[0] as $multipartHydrator) {
            // If it's neither a multipart nor a valid single part hydrator
            if (!is_array($multipartHydrator) &&
                !(new \ReflectionClass($multipartHydrator))->implementsInterface(HydratorInterface::class)
            ) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid multipart subhydrator class "%s"',
                        $multipartHydrator
                    ),
                    InvalidArgumentException::INVALID_MULTIPART_SUBHYDRATOR_CLASS
                );
            }
        }

        // Instantiate the multipart hydrator
        $multipartHydrator = trim($config[1]);
        $hydratorParameters = array_slice($config, 2);
        array_unshift($hydratorParameters, $config[0]);
        return Kernel::create($multipartHydrator, $hydratorParameters);
    }

    /**
     * Build a single part hydrator
     *
     * @param array $config Hydrator configuration
     * @return HydratorInterface Hydrator
     */
    protected static function buildSingle(array $config)
    {
        reset($config[0]);
        $singlepartName = trim(key($config[0]));
        $singlepartHydrator = trim(current($config[0]));

        // If it's not a valid simple part hydrator
        if (!(new \ReflectionClass($singlepartHydrator))->isSubclassOf(AbstractSinglepartHydrator::class)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid single part hydrator class "%s"',
                    $singlepartHydrator
                ),
                InvalidArgumentException::INVALID_SINGLEPART_HYDRATOR_CLASS
            );
        }

        // Instantiate the simple hydrator
        return new $singlepartHydrator($singlepartName);
    }
}
