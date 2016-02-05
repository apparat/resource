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

namespace Apparat\Resource\Infrastructure\Model\Resource;

use Apparat\Resource\Domain\Contract\ReaderInterface;
use Apparat\Resource\Domain\Contract\WriterInterface;
use Apparat\Resource\Domain\Model\Resource\AbstractSinglePartResource;
use Apparat\Resource\Infrastructure\Model\Hydrator\YamlHydrator;

/**
 * YAML resource
 *
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Infrastructure
 * @method YamlResource set(array $data) set(array $data) Set the content of the resource
 * @method YamlResource setPart(array $data, string $part = '/') setPart(array $data, string $part = '/') Set the
 *     content of the resource
 * @method array getDataPart(string $part = '/') getDataPart(string $part = '/') Get the YAML data of the resource
 * @method YamlResource setData(array $data) setData(array $data) Set the YAML data of the resource
 * @method YamlResource setDataPart(array $data, string $part = '/') setDataPart(array $data, string $part = '/') Set
 *     the YAML data of the resource
 * @method YamlResource from($src, ...$parameters) static from($src, ...$parameters) Instantiate from source
 * @method WriterInterface to($target, ...$parameters) to($target, ...$parameters) Write to target
 */
class YamlResource extends AbstractSinglePartResource
{
    /**
     * Use resource factory and data resource convenience methods and properties
     */
    use ResourceTrait, DataResourceTrait;

    /**
     * YAML resource constructor
     *
     * @param ReaderInterface $reader Reader instance
     */
    public function __construct(ReaderInterface $reader = null)
    {
        parent::__construct(YamlHydrator::class, $reader);
    }
}
