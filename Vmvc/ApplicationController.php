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
 * ApplicationController
 *
 * @package    Vmvc
 * @author     Joscha Meyer <schnipseljagd@googlemail.com>
 * @copyright  2010 Joscha Meyer <schnipseljagd@googlemail.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version
 * @link
 * @since      Release 0.1
 */
class Vmvc_ApplicationController
{
    /**
     * @var Vmvc_Router
     */
    protected $router;
    /**
     * @var Vmvc_ErrorHandler
     */
    protected $errorHandler;
    /**
     * @var string
     */
    protected $action;
    /**
     * @var string
     */
    protected $controller;

    /**
     * @param Vmvc_Router $router
     */
    public function __construct(Vmvc_Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param boolean $status
     * @return string
     */
    public function getViewScript($status)
    {
        // error templates
        if($this->isErrorHandler()) {
            $error = $this->errorHandler->getError();

            if($this->isError()) {
                return $this->getErrorViewScript($error);
            }
        }

        //
        $controller = $this->getController();
        $action = $this->getAction();

        // action controller template
        if($status!==false && $controller!='' && $action!='') {
            return $this->getActionControllerViewScript($controller, $action);
        }

        // default controller template
        if($status!==false && $controller!='') {
            return $this->getControllerViewScript($controller);
        }

        //
        return $this->getErrorViewScript();
    }

    /**
     * @return boolean
     */
    public function isError()
    {
        if(!$this->isErrorHandler()) {
            return false;
        }
        return $this->errorHandler->isError();
    }

    public function isErrorHandler()
    {
        if($this->errorHandler!==null) {
            return true;
        }
        return false;
    }

    /**
     * @param Vmvc_ErrorHandler $errorHandler
     */
    public function setErrorHandler(Vmvc_ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * @return string
     */
    public function getController()
    {
        if($this->controller===null) {
            $this->controller = $this->router->getRouteVar('controller');
        }
        if($this->controller!==null) {
            return $this->controller;
        }
        return '';
    }

    /**
     * @return string
     */
    public function getAction()
    {
        if($this->action===null) {
            return $this->router->getRouteVar('action');
        }
        return $this->action;
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        if(!is_string($controller)) {
            throw new InvalidArgumentException('argument has to be a string.');
        }
        $this->controller = $controller;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        if(!is_string($action) && $action!==null) {
            throw new InvalidArgumentException('argument has to be a string.');
        }
        $this->action = $action;
    }

    protected function getErrorViewScript($error = null)
    {
        if($error!==null) {
            return $error;
        }

        return 'error';
    }

    protected function getActionControllerViewScript($controller, $action)
    {
        return $controller . '/' . $action;
    }

    protected function getControllerViewScript($controller)
    {
        return $controller;
    }
}
