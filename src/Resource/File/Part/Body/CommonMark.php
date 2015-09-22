<?php

/**
 * bauwerk-resource
 *
 * @category    Jkphl
 * @package     Jkphl_Bauwerk
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

namespace Bauwerk\Resource\File\Part\Body;

use Bauwerk\Resource\File\Part\Body;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use League\CommonMark\Block\Element\Document;

/**
 * Markdown file part
 *
 * @package Bauwerk\Resource\File\Part\Body
 */
class CommonMark extends Generic
{
    /**
     * CommonMark document parser
     *
     * @var DocParser
     */
    protected $_parser = null;
    /**
     * CommonMark HTML renderer
     *
     * @var HtmlRenderer
     */
    protected $_renderer = null;
    /**
     * Markdown AST
     *
     * @var Document
     */
    protected $_ast = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // Obtain a pre-configured Environment with all the CommonMark parsers/renderers ready-to-go
        $environment = Environment::createCommonMarkEnvironment();

        // Custom environment initialization
        $this->_initializeEnvironment($environment);

        // Create the document parser and HTML renderer engines
        $this->_parser = new DocParser($environment);
        $this->_renderer = new HtmlRenderer($environment);
    }

    /**
     * Parse a content string and bring the part model to live
     *
     * @param string $content Content string
     * @return CommonMark                Self reference
     */
    public function parse($content)
    {
        parent::parse($content);
        $this->_ast = $this->_parser->parse($this->_content);
        return $this;
    }

    /**
     * Reset the part to its default state
     *
     * @return CommonMark                Self reference
     */
    public function reset()
    {
        $this->_ast = null;
        return parent::reset();
    }

    /**
     * Convert the Markdown source to HTML
     *
     * @return string                       CommonMark HTML
     */
    public function toHTML() {
        return ($this->_ast instanceof Document) ? $this->_renderer->renderBlock($this->_ast) : '';
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Custom environment initialization
     *
     * Overwrite this method in subclasses to register your own parsers/renderers.
     *
     * @param Environment $environment
     */
    protected function _initializeEnvironment(Environment $environment)
    {
        // Optional: Add your own parsers/renderers here, if desired
        // For example:  $environment->addInlineParser(new TwitterHandleParser());
    }
}