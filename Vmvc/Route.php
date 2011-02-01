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
 * Route
 *
 * @package    Vmvc
 * @author     Joscha Meyer <schnipseljagd@googlemail.com>
 * @copyright  2010 Joscha Meyer <schnipseljagd@googlemail.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version
 * @link
 * @since      Release 0.1
 */
class Vmvc_Route
{
    protected $routeUri;
    protected $options = array();

    public function __construct($routeUri, array $options = array())
    {
        $this->routeUri = $routeUri;
        $this->options = $options;
    }

    public function matches($requestUri)
    {
        $routeParams = $this->getParamsFromUri($this->routeUri);
        $requestParams = $this->getParamsFromUri($requestUri);

        return $this->compareParams($routeParams, $requestParams);
    }

    protected function compareParams($routeParams, $requestParams)
    {
        if(!$this->isParamLengthValid($routeParams, $requestParams)) {
            return false;
        }
        
        $routeLength = count($routeParams);
        for($i=0; $i<$routeLength; $i++)
        {
            $routeParam = $routeParams[$i];
            if(isset($requestParams[$i])) {
                $requestParam = $requestParams[$i];
            } else {
                $requestParam = '';
            }
            

            if(!$this->validate($routeParam, $requestParam)) {
                return false;
            }
        }

        return true;
    }

    protected function validate($routeParam, $requestParam)
    {        
        // static
        if($routeParam==$requestParam) {
            return true;
        }

        // dynamic default
        if($requestParam=='' && strpos($routeParam, ':')!==false) {
            list($regex, $routeParam) = explode(':', $routeParam);
            $option = $this->getOption($routeParam);
            if($option!==null) {
                $this->setOption($routeParam, $option);
                return true;
            }
        }

        // dynamic
        if(strpos($routeParam, ':') === 0 && preg_match('/^[\w\-]+$/', $requestParam)==1) {
            $routeParam = substr($routeParam, 1);
            $this->setOption($routeParam, $requestParam);
            return true;
        }

        // regex
        if(strpos($routeParam, ':')!==false) {
            list($regex, $routeParam) = explode(':', $routeParam);
            
            if($regex!='' && preg_match('/' . $regex . '/', $requestParam)==1) {
                $this->setOption($routeParam, $requestParam);
                return true;
            }
        }
        
        return false;
    }


    public function getOptions()
    {
        return $this->options;
    }

    public function getOption($name) {
        if(!isset($this->options[$name])) {
            return null;
        }

        return $this->options[$name];
    }

    protected function setOption($name, $value) {
        $this->options[$name] = $value;
    }

    protected function isParamLengthValid(&$routeParams, &$requestParams)
    {
        $routeLength = count($routeParams);
        $requestLength = count($requestParams);

        // if wildcard given remove it
        if($routeParams[$routeLength-1]=='*') {
            array_pop($routeParams);
            return true;
        }
        
        // if not given, parameter lengths should be equal
        if($routeLength!=$requestLength) {
            return false;
        }
        return true;
    }

    protected function getParamsFromUri($uri)
    {
        $uri = urldecode($uri);
        // remove the ? seperator and parameters behind him
        $paramsExt = explode('?', $uri);

        // get the parameters seperated with /
        $params = explode('/', $paramsExt[0]);

        // delete beginning and end if empty
        if($params[0]=='') {
            array_shift($params);
        }
        if($params[count($params)-1]=='') {
            array_pop($params);
        }
        
        return $params;
    }
}