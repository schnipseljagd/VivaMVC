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
 * FrontController
 *
 * @package    Vmvc
 * @author     Joscha Meyer <schnipseljagd@googlemail.com>
 * @copyright  2010 Joscha Meyer <schnipseljagd@googlemail.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version
 * @link
 * @since      Release 0.1
 */
class Vmvc_FrontController implements Vmvc_ControllerCallObserverInterface
{
    /**
     * @var Vmvc_ApplicationController
     */
    protected $app;
    /**
     * @var Vmvc_Controller
     */
    protected $controller;
    /**
     * @var Vmvc_View
     */
    protected $view;
    /**
     * @var Vmvc_ControllerFactory
     */
    protected $controllerFactory;
    /**
     * @var string
     */
    protected $viewScriptPath;

    /**
     * @param Vmvc_ApplicationController $app
     */
	public function __construct(Vmvc_ApplicationController $app)
    {
        $this->app = $app;
    }

    /**
     * @param Vmvc_Controller $controller
     * @param Vmvc_View $view
     * @return string
     */
    public function execute(Vmvc_ControllerFactory $controllerFactory,
                            Vmvc_View $view)
    {
        $this->controllerFactory = $controllerFactory;
        $this->view = $view;

        $controllerType = $this->getAppController();
        $action = $this->getAppControllerAction();

        $this->viewScriptPath = $this->doExecute($controllerType, $action);
        return $this->renderView($this->viewScriptPath);
    }

    /**
     * call a controller and/or an action
     * @param string $controllerType
     * @param string $action
     * @return string
     */
    public function doExecute($controllerType, $action)
    {
        //if controllerType or action changed, update the app
        if($controllerType!=$this->getAppController()) {
            $this->app->setController($controllerType);
        }
        if($action!=$this->getAppControllerAction()) {
            $this->app->setAction($action);
        }

        // get controller
        $this->controller = $this->getController($controllerType);
        $this->controller->setCallObserver($this);

        // handle controller action if given
        if($this->controller instanceof Vmvc_ActionController) {
            $this->executeControllerAction($action);
        }
        // execute controller
        $status = $this->executeController();

        // render view
        if($this->viewScriptPath===null) {
            $this->viewScriptPath = $this->getAppViewScript($status);
        }
        return $this->viewScriptPath;
    }

    /**
     * set a new path to a viewscript
     * @param string $viewScriptPath
     */
    public function setViewScriptPath($viewScriptPath)
    {
        if(!is_string($viewScriptPath) || $viewScriptPath=='') {
            throw new InvalidArgumentException('viewScriptPath has to be a string' .
                                               'and should not be empty.');
        }
        $this->viewScriptPath = $viewScriptPath;
    }

    protected function renderView($viewScriptPath)
    {
        return $this->view->render($viewScriptPath);
    }

    protected function getController($controllerType)
    {
        // if no controller defined
        if($this->controller===null) {
            return $this->controllerFactory->getController($controllerType);
        }

        // if active controllerName ne new controllerName
        $controllerName = $this->controllerFactory->getControllerName($controllerType);
        if($controllerName != get_class($this->controller)) {
            return $this->controllerFactory->getController($controllerType);
        }
        
        return $this->controller;
    }

    protected function executeController()
    {
        return $this->controller->execute();
    }

    /**
     * @param string $action
     */
    protected function executeControllerAction($action = null)
    {
        $actionMethod = $this->getControllerActionName($action);
        $this->controller->beforeExecute($action);
        $this->controller->$actionMethod();
        $this->controller->afterExecute();
    }

    /**
     * @throws InvalidArgumentException
     * @param string $action
     * @return string
     */
    protected function getControllerActionName($action)
    {
        if(!is_string($action) || $action=='') {
            throw new InvalidArgumentException('action has to be a string and ' .
                                               'should not be empty.');
        }
        return $action . 'Action';
    }

    protected function getAppController()
    {
        return $this->app->getController();
    }

    /**
     * @return string
     */
    protected function getAppControllerAction()
    {
        return $this->app->getAction();
    }

    /**
     * @param boolean $status
     * @return string
     */
    protected function getAppViewScript($status)
    {
        return $this->app->getViewScript($status);
    }
}
