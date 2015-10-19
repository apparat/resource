<?php

/**
 * resource
 *
 * @category    Apparat
 * @package     Apparat_Resource
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

namespace Apparat\Resource\File\Frontmatter\Yaml;

use Apparat\Resource\File\Frontmatter\Yaml\Exception\InvalidArgument;
use Apparat\Resource\File\FrontmatterInterface;
use Apparat\Resource\File\PartInterface;
use Apparat\Resource\File\Part\Body\Yaml;
use Apparat\Resource\Utility;

/**
 * CommonMark file
 *
 * @package     Apparat_Resource
 * @see http://commonmark.org/
 */
class CommonMark extends \Apparat\Resource\File\CommonMark implements FrontmatterInterface
{

    /**
     * Constructor
     *
     * @param string $source Source file
     */
    public function __construct($source = null)
    {
        $this->_setPartModel(array(
            FrontmatterInterface::FRONTMATTER_NAME => \Apparat\Resource\File\Part\Body\Yaml::class,
            PartInterface::DEFAULT_NAME => $this->_defaultBodyPartClass
        ), 1, 1);

        parent::__construct();
    }

    public function __toString()
    {
        return '---'.PHP_EOL.trim($this->getMeta()).PHP_EOL.'...'.PHP_EOL.strval($this->getBody());
    }

    /**
     * Parse a content string and bring the part model to live
     *
     * @param string $content Content string
     * @return CommonMark       Self reference
     * @throws InvalidArgument  If the file doesn't start with a valid YAML document
     */
    public function parse($content)
    {
        $content = Utility::stripBom($content);

        // Check if the file starts with a YAML document
        if (strncmp($content, '---', 3)) {
            throw new InvalidArgument('Frontmatter file content must start with a YAML document part (---)',
                InvalidArgument::INVALID_YAML_FRONTMATTER_DOCUMENT);
        }

        // Extract the leading YAML document
        $partContent = preg_split("%\R((\.\.\.)|(---.*?))\R%", substr($content, 3), 2);

        // Parse the YAML part
        if (strlen(trim($partContent[0]))) {
            $this->getMeta()->parse($partContent[0]);
        }

        // Parse the CommonMark part
        if (count($partContent) > 1) {
            $this->getBody()->parse($partContent[1]);
        }

        return $this;
    }

    /**
     * Return the meta data part
     *
     * @return Yaml         YAML metadata part
     */
    public function getMeta()
    {
        return $this->getPart(FrontmatterInterface::FRONTMATTER_NAME);
    }
}