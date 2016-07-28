<?php

/**
 * apparat/resource
 *
 * @category    Apparat
 * @package     Apparat\Resource
 * @subpackage  Apparat\Resource\Application
 * @author      Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
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

namespace Apparat\Resource\Application\Service;

/**
 * JsonUtility
 *
 * @package Apparat\Resource
 * @subpackage Apparat\Resource\Application
 */
class JsonUtility
{
    /**
     * Regular expression for matching ISO 8601 date representations
     *
     * @var string
     */
    const ISO_8601_DATE_REGEX = '~^
        (?P<year>[0-9][0-9][0-9][0-9])
        -(?P<month>[0-9][0-9]?)
        -(?P<day>[0-9][0-9]?)
        (?:(?:[Tt]|[ \t]+)
        (?P<hour>[0-9][0-9]?)
        :(?P<minute>[0-9][0-9])
        :(?P<second>[0-9][0-9])
        (?:\.(?P<fraction>[0-9]*))?
        (?:[ \t]*(?P<tz>Z|(?P<tz_sign>[-+])(?P<tz_hour>[0-9][0-9]?)
        (?::(?P<tz_minute>[0-9][0-9]))?))?)?
        $~x';

    /**
     * JSON encode data
     *
     * @param mixed $data Data
     * @return string JSON string
     */
    public static function encode($data)
    {
        return json_encode(self::encodeDates($data), JSON_PRETTY_PRINT);
    }

    /**
     * Recursively encode DateTime objects to ISO 8601 date strings
     *
     * @param mixed $data Data
     * @return mixed Data with encoded ISO 8601 dates
     */
    protected static function encodeDates($data)
    {
        switch (true) {
            case $data instanceof \DateTimeInterface:
                return $data->format('c');
            case $data instanceof \stdClass:
                foreach (get_object_vars($data) as $key => $value) {
                    $data->$key = self::encodeDates($value);
                }
                break;
            case is_array($data):
                foreach ($data as $key => $value) {
                    $data[$key] = self::encodeDates($value);
                }
        }
        return $data;
    }

    /**
     * JSON decode a string
     *
     * @param string $str JSON string
     * @param bool $assoc Decode objects to associative arrays
     * @return mixed Decoded data
     */
    public static function decode($str, $assoc = false)
    {
        return self::decodeDates(json_decode($str, $assoc));
    }

    /**
     * Recursively decode ISO 8601 dates to DateTimeImmutable objects
     *
     * @param mixed $data Data
     * @return mixed Data with decoded ISO 8601 dates
     */
    protected static function decodeDates($data)
    {
        switch (true) {
            case is_string($data) && preg_match(self::ISO_8601_DATE_REGEX, $data):
                return new \DateTimeImmutable($data);
            case $data instanceof \stdClass:
                foreach (get_object_vars($data) as $key => $value) {
                    $data->$key = self::decodeDates($value);
                }
                break;
            case is_array($data):
                foreach ($data as $key => $value) {
                    $data[$key] = self::decodeDates($value);
                }
        }
        return $data;
    }
}
