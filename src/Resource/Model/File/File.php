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

namespace Apparat\Resource\Model\File;

use Apparat\Resource\Model\Content\ContentModel;
use Apparat\Resource\Model\Hydrator\Hydrator;
use Apparat\Resource\Model\Resource;

/**
 * File
 *
 * @package Apparat\Resource\Model\File
 */
class File extends Resource
{
    /**
     * Reader instance
     *
     * @var FileReader
     */
    protected $_reader = null;
    /**
     * Writer instance
     *
     * @var FileWriter
     */
    protected $_writer = null;
    /**
     * Content model
     *
     * @var ContentModel
     */
    private $_contentModel;
    /**
     * File hydrator
     *
     * @var Hydrator
     */
    private $_hydrator;

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Private constructor
     *
     * @param ContentModel $contentModel Content model
     * @param Hydrator $hydrator File hydrator
     * @param FileReader $reader File reader instance
     * @param FileWriter $writer File writer instance
     */
    public function __construct(ContentModel $contentModel, Hydrator $hydrator, FileReader $reader = null, FileWriter $writer = null)
    {
        $this->_contentModel = $contentModel;
        $this->_hydrator = $hydrator;
        $this->setReader($reader);
        $this->setWriter($writer);
    }

    /**
     * Set the file reader instance
     *
     * @param FileReader|null $reader File reader instance
     */
    public function setReader(FileReader $reader = null)
    {
        $this->_reader = $reader;
    }

    /**
     * Set the file writer instance
     *
     * @param FileWriter|null $writer File writer instance
     */
    public function setWriter(FileWriter $writer = null)
    {
        $this->_writer = $writer;
    }
}