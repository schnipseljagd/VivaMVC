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
 * HelperBroker
 *
 * @package    Vmvc
 * @author     Joscha Meyer <schnipseljagd@googlemail.com>
 * @copyright  2010 Joscha Meyer <schnipseljagd@googlemail.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version
 * @link
 * @since      Release 0.1
 */
class Vmvc_HelperBroker
{
    protected $helperMap = array();
    protected $aliasMap = array();

    /**
     * @param string $name
     * @return object
     */
    public function getHelper($name)
    {
        if(!isset($this->helperMap[strtolower($name)])) {
            throw new Vmvc_Exception('helper is not defined.');
        }
        return $this->helperMap[strtolower($name)];
    }

    /**
     * @param string $name
     * @param object $helper
     */
    public function setHelper($name, $helper)
    {
        $this->helperMap[strtolower($name)] = $helper;

        return $this;
    }

    /**
     * @param string $aliasName
     * @param string $helperName
     * @param string $methodName
     */
    public function registerAlias($aliasName, $helperName, $methodName = null)
    {
        // if not methodName given, alias should
        if($methodName===null) {
            $methodName = $aliasName;
        }
        // get helper instance
        $helper = $this->getHelper($helperName);

        // add to map
        $this->aliasMap[$aliasName] = array('helper' => $helper,
                                            'method' => $methodName);

        return $this;
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function callAlias($name, array $args)
    {
        $alias = $this->getAlias($name);
        $helper = $alias['helper'];
        $methodName = $alias['method'];
        
        return call_user_func_array(array($helper, $methodName), $args);
    }

    /**
     * @param string $name
     * @return array
     */
    protected function getAlias($name)
    {
        if(!is_string($name) && $name=='') {
            throw new InvalidArgumentException('name has to be a string and ' .
                                               'should not be empty.');
        }
        if(!isset($this->aliasMap[$name])) {
            throw new Vmvc_Exception('alias is not defined.');
        }

        return $this->aliasMap[$name];
    }
}