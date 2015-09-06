<?php

/**
 * Bauwerk
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

namespace Bauwerk\Resource;

/**
 * File part
 *
 * @package Bauwerk\Resource
 */
abstract class Part implements PartInterface {

	/**
	 * Owner file
	 *
	 * @var FileInterface
	 */
	protected $_ownerFile = null;

	/**
	 * Parent part
	 *
	 * @var PartInterface
	 */
	protected $_parentPart = null;

	/**
	 * MIME type
	 *
	 * @var string
	 */
	protected $_mimeType = 'application/octet-stream';

	/**
	 * Default part key
	 *
	 * @var string
	 */
	const DEFAULT_NAME = 'default';

	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * Constructor
	 *
	 * @param string $content   Part content
	 */
	public function __construct($content = '') {
		$this->setContent($content);
	}

	/**
	 * Return the MIME type
	 *
	 * @return string           MIME type
	 */
	public function getMimeType()
	{
		return $this->_mimeType;
	}

	/**
	 * Set the MIME type
	 *
	 * @param string $mimeType MIME type
	 * @return Part             Self reference
	 */
	public function setMimeType($mimeType)
	{
		$this->_mimeType = $mimeType;
		return $this;
	}

	/**
	 * Return the owner file
	 *
	 * @return FileInterface    Owner file
	 */
	public function getOwnerFile()
	{
		return $this->_ownerFile;
	}

	/**
	 * Set the owner file
	 *
	 * @param FileInterface $ownerFile Owner file
	 * @return Part                         Self reference
	 */
	public function setOwnerFile(FileInterface $ownerFile)
	{
		$this->_ownerFile = $ownerFile;
	}

	/**
	 * Return the parent part
	 *
	 * @return PartInterface                Parent part
	 */
	public function getParentPart()
	{
		return $this->_parentPart;
	}

	/**
	 * Set the parent part
	 *
	 * @param PartInterface $part Parent part
	 * @return Part                         Self reference
	 */
	public function setParentPart(PartInterface $part)
	{
		$this->_parentPart = $part;
		$this->setOwnerFile(($this->_parentPart instanceof FileInterface) ? $this->_parentPart : $this->_parentPart->getOwnerFile());
	}
}