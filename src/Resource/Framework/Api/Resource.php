<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Framework
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

namespace Apparat\Resource\Framework\Api;

use Apparat\Resource\Domain\Contract\ReaderInterface;
use Apparat\Resource\Domain\Model\Resource\AbstractResource;
use Apparat\Resource\Framework\Model\Resource\CommonMarkResource;
use Apparat\Resource\Framework\Model\Resource\FrontMarkResource;
use Apparat\Resource\Framework\Model\Resource\JsonResource;
use Apparat\Resource\Framework\Model\Resource\TextResource;
use Apparat\Resource\Framework\Model\Resource\YamlResource;

/**
 * Resource factory
 *
 * @package Apparat\Resource
 * @subpackage Apparat\Resource\Framework
 */
class Resource
{

    /**
     * Create and return a text resource instance
     *
     * @param string $src Stream-wrapped source
     * @param array $parameters Reader parameters
     * @return TextResource Text resource instance
     * @api
     */
    public static function text($src, ...$parameters)
    {
        return self::fromSource($src, TextResource::class, ...$parameters);
    }

    /**
     * Create and return a YAML resource instance
     *
     * @param string $src Stream-wrapped source
     * @param array $parameters Reader parameters
     * @return YamlResource YAML resource instance
     * @api
     */
    public static function yaml($src, ...$parameters)
    {
        return self::fromSource($src, YamlResource::class, ...$parameters);
    }

    /**
     * Create and return a JSON resource instance
     *
     * @param string $src Stream-wrapped source
     * @param array $parameters Reader parameters
     * @return JsonResource JSON resource instance
     * @api
     */
    public static function json($src, ...$parameters)
    {
        return self::fromSource($src, JsonResource::class, ...$parameters);
    }

    /**
     * Create and return a CommonMark resource instance
     *
     * @param string $src Stream-wrapped source
     * @param array $parameters Reader parameters
     * @return CommonMarkResource CommonMark resource instance
     * @api
     */
    public static function commonMark($src, ...$parameters)
    {
        return self::fromSource($src, CommonMarkResource::class, ...$parameters);
    }

    /**
     * Create and return a FrontMark resource instance
     *
     * @param string $src Stream-wrapped source
     * @param array $parameters Reader parameters
     * @return FrontMarkResource FrontMark resource instance
     * @api
     */
    public static function frontMark($src, ...$parameters)
    {
        return self::fromSource($src, FrontMarkResource::class, ...$parameters);
    }

    /*******************************************************************************
     * STATIC METHODS
     *******************************************************************************/

    /**
     * Create a reader instance from a stream-wrapped source
     *
     * @param string $src Stream-wrapped source
     * @param string $resourceClass Resource class name
     * @param array $parameters Reader parameters
     * @return AbstractResource Resource instance
     * @throws InvalidArgumentException If an invalid reader stream wrapper is given
     */
    protected static function fromSource($src, $resourceClass, ...$parameters)
    {
        $reader = Tools::reader($src, $parameters);
        if ($reader instanceof ReaderInterface) {
            return new $resourceClass($reader);
        }

        throw new InvalidArgumentException(
            'Invalid reader stream wrapper',
            InvalidArgumentException::INVALID_READER_STREAM_WRAPPER
        );
    }
}
