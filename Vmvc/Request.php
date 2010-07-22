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
 * Request
 *
 * @package    Vmvc
 * @author     Joscha Meyer <schnipseljagd@googlemail.com>
 * @copyright  2010 Joscha Meyer <schnipseljagd@googlemail.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version
 * @link
 * @since      Release 0.1
 */
class Vmvc_Request
{
    /**
     * @var array
     */
    protected $vars;
    /**
     * @var array
     */
    protected $postVars;
    /**
     * @var array
     */
    protected $serverVars;

    /**
     * @param array $vars
     * @param array $postVars
     * @param array $serverVars
     */
    public function __construct(array $vars, array $postVars, array $serverVars)
    {
        $this->vars       = $vars;
        $this->postVars   = $postVars;
        $this->serverVars = $serverVars;
    }

    /**
     * @deprecated use getUri instead!
     * @return array
     */
    public function getUriParams()
    {
        $uri = $this->getUri();
        list($urlPath) = explode('?', $uri);
        $params = explode('/', $urlPath);
        array_shift($params);
        return $params;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        if(!isset($this->serverVars['REQUEST_URI'])) {
            return null;
        }
        return $this->serverVars['REQUEST_URI'];
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setVar($name, $value)
    {
        $this->vars[$name] = $value;
    }

    /**
     * @throws InvalidArgumentException
     * @param string $name
     * @return mixed
     */
    public function getVar($name)
    {
        if(!isset($this->vars[$name])) {
            return null;
        }
        return $this->vars[$name];
    }

    /**
     * @return array
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * @throws InvalidArgumentException
     * @param string $name
     * @return mixed
     */
    public function getPostVar($name)
    {
        if(!isset($this->postVars[$name])) {
            return null;
        }
        return $this->postVars[$name];
    }

    /**
     * @return array
     */
    public function getPostVars()
    {
        return $this->postVars;
    }

    /**
     * @return boolean
     */
    public function isXmlHttpRequest()
    {
        if(isset($this->serverVars['HTTP_X_REQUESTED_WITH'])
           && $this->serverVars['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
        {
            return true;
        }
        return false;
    }
}
