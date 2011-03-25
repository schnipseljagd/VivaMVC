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
 * Controller
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
class Vmvc_Controller
{
    /**
     * @var Vmvc_Request
     */
    protected $request;
    /**
     * @var Vmvc_Response
     */
    protected $response;
    /**
     * @var Vmvc_ControllerCallObserverInterface
     */
    protected $callObserver;
    /**
     * @var Vmvc_ServiceProviderInterface
     */
    private $serviceProvider;
    /**
     * @var Vmvc_HelperBroker
     */
    private $_helperBroker;

    /**
     * The naming of further parameters should be care the naming convention of
     * the services, that will be called automatically.
     * @param Vmvc_Request                  $request
     * @param Vmvc_Response                 $response
     * @param Vmvc_ServiceProviderInterface $serviceProvider
     */
    public function __construct(
        Vmvc_Request $request,
        Vmvc_Response $response,
        Vmvc_ServiceProviderInterface $serviceProvider
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->serviceProvider = $serviceProvider;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getParam($name)
    {
        return $this->request->getVar($name);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getPostParam($name)
    {
        return $this->request->getPostVar($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setData($name, $value)
    {
        $this->response->setData($name, $value);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getData($name)
    {
        return $this->response->getData($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name,  $value)
    {
        $this->setData($name, $value);
    }

    /**
     * @param string $header
     */
    public function addHeader($header)
    {
        $this->response->addHeader($header);
    }

    public function clearHeaders()
    {
        $this->response->clearHeaders();
    }

    /**
     * @param $serviceId
     * @return object
     */
    public function get($serviceId)
    {
        return $this->serviceProvider->getServiceObject($serviceId);
    }

    /**
     * Set a helper broker
     * @param Vmvc_HelperBroker $helperBroker
     */
    public function setHelperBroker(Vmvc_HelperBroker $helperBroker)
    {
        $this->_helperBroker = $helperBroker;
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if ($this->_helperBroker === null) {
            throw new Vmvc_Exception('HelperBroker is not set.');
        }
        return $this->_helperBroker->callAlias($name, $arguments);
    }

    /**
     * @param Vmvc_ControllerCallObserverInterface $callObserver
     */
    public function setCallObserver(
        Vmvc_ControllerCallObserverInterface $callObserver
    ) {
        $this->callObserver = $callObserver;
    }

    /**
     * @param string $controllerName
     * @param string $controllerAction
     * @throws Vmvc_Exception
     */
    public function callController($controllerName, $controllerAction = null)
    {
        if ($this->callObserver === null) {
            throw new Vmvc_Exception('callObserver is not set.');
        }
        $this->callObserver->doExecute($controllerName, $controllerAction);
    }

    /**
     * @param string $viewScriptPath
     */
    public function setViewScriptPath($viewScriptPath)
    {
        if ($this->callObserver === null) {
            throw new Vmvc_Exception('callObserver is not set.');
        }

        $this->callObserver->setViewScriptPath($viewScriptPath);
    }

    public function getControllerName()
    {
        $controllerClass = get_class($this);
        return str_replace('Controller_', '', $controllerClass);
    }

    // @codeCoverageIgnoreStart
    /**
     * Override this method.
     * Init is called at the end of the constructor
     */
    public function init()
    {
    }

    /**
     * Override this method.
     * @return boolean
     */
    public function execute()
    {
        return false;
    }
    // @codeCoverageIgnoreEnd
}
