<?php
/**
 * VivaMVC
 *
 * Copyright (c) 2010, Joscha Meyer <schnipseljagd@googlemail.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Joscha Meyer nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Vmvc
 * @author     Joscha Meyer <schnipseljagd@googlemail.com>
 * @copyright  2010 Joscha Meyer <schnipseljagd@googlemail.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since
 */


/**
 * Response
 *
 * @package    Vmvc
 * @author     Joscha Meyer <schnipseljagd@googlemail.com>
 * @copyright  2010 Joscha Meyer <schnipseljagd@googlemail.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version
 * @link
 * @since      Release 0.1
 */
class Vmvc_Response
{
    /**
     * response content types
     */
    const CONTENTTYPE_XML = 'text/xml';
    const CONTENTTYPE_HTML = 'text/html';
    const CONTENTTYPE_JSON = 'application/json';
    /**
     * @var array
     */
    protected $data = array();
    /**
     * @var array
     */
    protected $headers = array();

    protected $contentType = 'text/html';

    protected $charset = 'utf-8';

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = (string) $contentType;
    }

    /**
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = (string) $charset;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getContentSubType()
    {
        $contentType = $this->getContentType();
        list($toplevelType, $subType) = explode('/', $contentType);
        return (string) $subType;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * adds a header
     * @throws InvalidArgumentException
     * @param string $header
     */
    public function addHeader($header)
    {
        if(!is_string($header)) {
            throw new InvalidArgumentException('argument is not a string.');
        }
        $this->headers[] = $header;
    }

    /**
     * Returns an array of header strings
     * @param boolean $with_defaults
     * @return array
     */
    public function getHeaders($with_defaults = true)
    {
        if($with_defaults===true) {
            $defaults = $this->getDefaultHeaders();
            return array_merge($defaults, $this->headers);
        }
        return $this->headers;
    }

    /**
     * delete all headers
     */
    public function clearHeaders()
    {
        $this->headers = array();
    }

    /**
     * @throws InvalidArgumentException
     * @param string $name
     * @return mixed
     */
    public function getData($name)
    {
        if(!isset($this->data[$name])) {
            return null;
            //throw new InvalidArgumentException('name was not found.');
        }
        return $this->data[$name];
    }

    /**
     * @throws InvalidArgumentException
     * @param string $name
     * @param mixed $value
     */
    public function setData($name, $value)
    {
        if(!is_string($name)) {
            throw new InvalidArgumentException('argument is not a string.');
        }
        $this->data[$name] = $value;
    }

    /**
     * @return string
     */
    protected function getDefaultHeaders()
    {
        return array("Content-Type: {$this->contentType}; " .
                          "charset={$this->charset}");
    }
}
