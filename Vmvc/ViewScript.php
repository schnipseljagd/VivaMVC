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
 * @package   Vmvc
 * @author    Joscha Meyer <schnipseljagd@googlemail.com>
 * @copyright 2010 Joscha Meyer <schnipseljagd@googlemail.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://vivamvc.schnipseljagd.org/
 * @since     0.1
 */


/**
 * ViewScript
 *
 * @category  MVC
 * @package   Vmvc
 * @author    Joscha Meyer <schnipseljagd@googlemail.com>
 * @copyright 2010 Joscha Meyer <schnipseljagd@googlemail.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: 0.3.2
 * @link      http://vivamvc.schnipseljagd.org/
 * @since     Release: 0.1
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
        $this->path = rtrim($path, '/') . '/';
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
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
     * @param string $name
     * @param string|array $arguments
     * @return mixed
     * @throws Vmvc_Exception
     */
    public function  __call($name,  $arguments)
    {
        return $this->getHelper($name, $arguments);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setData($name, $value)
    {
        $this->data[(string) $name] = $value;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getData($name)
    {
        if (!isset($this->data[$name])) {
            return null;
        }
        return $this->data[$name];
    }


    /**
     * @param Vmvc_ViewHelperInterface $helper
     * @param string $helperName
     */
    public function registerHelper(
        Vmvc_ViewHelperInterface $helper, $helperName = ''
    ) {
        if ($helperName == '') {
            $helperReflection = new ReflectionObject($helper);
            $helperName = $helperReflection->getName();
        }
        $this->viewHelpers[$helperName] = $helper;
    }

    /**
     * Call a ViewHelper
     * @param string $helperName
     * @param array $args
     * @return mixed
     * @throws Vmvc_Exception
     */
    public function getHelper($helperName, $args = array())
    {
        if (isset($this->viewHelpers[$helperName])) {
            $helper = $this->viewHelpers[$helperName];
        } else {
            $helperName = 'View_Helper_' . ucfirst($helperName);
            $helper = new $helperName();
        }

        if (!($helper instanceof Vmvc_ViewHelperInterface)) {
            throw new Vmvc_Exception(
                'View Helper needs to implement ViewHelperInterface.'
            );
        }
        return $helper->execute($args, $this->getPath());
    }

    /**
     * @param string $viewScriptPath
     * @return string
     * @throws InvalidArgumentException
     */
    public function render($viewScriptPath)
    {
        $this->validateScriptPath($viewScriptPath);

        return $this->doRender($viewScriptPath);
    }

    /**
     * @param string $scriptPath
     * @throws InvalidArgumentException
     */
    protected function validateScriptPath($scriptPath)
    {
        if (!is_string($scriptPath)
            || preg_match('/^[a-zA-Z0-9\.\/\_]+$/', $scriptPath)==0
        ) {
            throw new InvalidArgumentException(
                'argument has to be a string and can only ' .
                'contain letters and digits, ., _ or /'
            );
        }
    }

    /**
     * @param string $scriptPath
     * @param string $scriptHeader
     * @param string $scriptFooter
     * @return string
     */
    protected function doRender(
        $scriptPath, $scriptHeader = '', $scriptFooter = ''
    ) {
        $script = $scriptHeader;
        ob_start();
        include $this->getPath() . lcfirst($scriptPath) . '.php';
        $script .= ob_get_clean();
        $script .= $scriptFooter;
        return $script;
    }
}