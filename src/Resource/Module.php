<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource
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

namespace Apparat\Resource;

use Apparat\Kernel\Ports\AbstractModule;
use Apparat\Kernel\Ports\Contract\DependencyInjectionContainerInterface;
use Dotenv\Dotenv;

/**
 * Resource module
 *
 * @package Apparat\Resource
 * @subpackage Apparat\Resource
 */
class Module extends AbstractModule
{
    /**
     * Module name
     *
     * @var string
     */
    const NAME = 'resource';

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Configure the dependency injection container
     *
     * @param DependencyInjectionContainerInterface $diContainer Dependency injection container
     * @return void
     * @see https://r.je/dice.html#example3-6
     */
    public function configureDependencyInjection(DependencyInjectionContainerInterface $diContainer)
    {
        parent::configureDependencyInjection($diContainer);
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Validate the environment
     *
     * @param Dotenv $environment Environment
     */
    protected static function validateEnvironment(Dotenv $environment)
    {
        parent::validateEnvironment($environment);
    }
}
