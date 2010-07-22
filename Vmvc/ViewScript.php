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
 * ViewScript
 *
 * @package    Vmvc
 * @author     Joscha Meyer <schnipseljagd@googlemail.com>
 * @copyright  2010 Joscha Meyer <schnipseljagd@googlemail.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version
 * @link
 * @since      Release 0.1
 */
class Vmvc_ViewScript
{
    /**
     * @var array
     */
    protected $data;
    /**
     * @var array
     */
    protected $viewHelpers = array();
    /**
     * @var string
     */
    protected $path = '';

    /**
     * @param array|null $data
     */
    public function  __construct(array $data = null)
    {
        $this->data = $data;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        if(!is_string($path)) {
            throw new InvalidArgumentException('path has to be a string.');
        }
        $this->path = $path . '/';
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getData($name);
    }

    /**
     * Call a ViewHelper
     * @throws Vmvc_Exception
     * @param string $name
     * @param string|array $arguments
     * @return mixed
     */
    public function  __call($name,  $arguments)
    {
        return $this->getHelper($name, $arguments);
    }

	/**
     * @param mixed $value
     */
    public function setData($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getData($name)
    {
        if(!isset($this->data[$name])) {
            return null;
        }
        return $this->data[$name];
    }


    /**
     * @param Vmvc_ViewHelperInterface $helper
     */
    public function registerHelper(Vmvc_ViewHelperInterface $helper)
    {
        $helperReflection = new ReflectionObject($helper);
        $helperName = $helperReflection->getName();
        $this->viewHelpers[$helperName] = $helper;
    }

    /**
     * Call a ViewHelper
     * @throws Vmvc_Exception
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function getHelper($name, $args = array())
    {
        $helperName = ucfirst($name) . 'Helper';

        if(isset($this->viewHelpers[$helperName])) {
            $helper = $this->viewHelpers[$helperName];
        } else {
            $helper = new $helperName();
        }

        if($helper instanceof Vmvc_ViewHelperInterface) {
            return $helper->execute($args);
        }

        throw new Vmvc_Exception('View Helper needs to implement ' .
                                 'ViewHelperInterface.');
    }

    /**
     * @throws InvalidArgumentException
     * @param string $viewScriptPath
     * @return string
     */
    public function render($viewScriptPath)
    {
        $this->validateScriptPath($viewScriptPath);

        return $this->doRender($viewScriptPath);
    }

    /**
     * @throws InvalidArgumentException
     * @param string $scriptPath
     */
    protected function validateScriptPath($scriptPath)
    {
        if(!is_string($scriptPath)
           || preg_match('/^[a-zA-Z0-9\.\/\_]+$/', $scriptPath)==0)
        {
            throw new InvalidArgumentException(
                          'argument has to be a string and can only ' .
                          'contain letters and digits, ., _ or /');
        }
    }

    /**
     * @param string $scriptPath
     * @param string $scriptHeader
     * @param string $scriptFooter
     * @return string
     */
    protected function doRender($scriptPath, $scriptHeader = '', $scriptFooter = '')
    {
        $script = $scriptHeader;
        ob_start();
        include $this->path . lcfirst($scriptPath) . '.php';
        $script .= ob_get_clean();
        $script .= $scriptFooter;
        return $script;
    }
}