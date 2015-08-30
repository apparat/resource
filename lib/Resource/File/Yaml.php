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

namespace Bauwerk\Resource\File;

use Bauwerk\Resource\File;
use Bauwerk\Resource\Part;

/**
 * YAML file
 *
 * @package Bauwerk\Resource\File
 * @see http://yaml.org/spec/1.2/spec.pdf
 */
class Yaml extends File {
	/**
	 * Default part class
	 *
	 * @var string
	 */
	protected $_defaultPartClass = 'Bauwerk\\Resource\\Part\\Yaml';

	/**
	 * Return a file part
	 *
	 * @param string $key                       Part key
	 * @return Part\Yaml                        YAML part
	 * @throws OutOfRangeException              If an invalid part is requested
	 * @throws OutOfRangeException              If the requested part key is empty
	 */
	public function getPart($key) {
		return parent::getPart($key);
	}

	/**
	 * Return the YAML data
	 *
	 * @return array				YAML data
	 */
	public function getData() {
		return $this->getPart(Part::DEFAULT_NAME)->getData();
	}

	/**
	 * Set the YAML data
	 *
	 * @param array $data			YAML data
	 * @return YamlTrait
	 */
	public function setData(array $data) {
		$this->getPart(Part::DEFAULT_NAME)->setData($data);
		return $this;
	}
}