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
 * ControllerFactory
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
class Vmvc_ControllerFactory
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
     * @var Vmvc_ServiceProviderInterface
     */
    protected $serviceProvider;
    /**
     * @var Vmvc_HelperBroker
     */
    protected $helperBroker;

    /**
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
     * @param Vmvc_HelperBroker $helperBroker
     */
    public function setHelperBroker(Vmvc_HelperBroker $helperBroker)
    {
        $this->helperBroker = $helperBroker;
    }

    /**
     * Call a controller
     * @param string $type
     * @return Vmvc_Controller
     * @throws Vmvc_Exception
     */
    public function getController($type = '')
    {
        $controllerClass = $this->getControllerName($type);
        $controller = new $controllerClass(
            $this->request, $this->response, $this->serviceProvider
        );
        if ($this->helperBroker !== null) {
            $controller->setHelperBroker($this->helperBroker);
        }
        return $controller;
    }

    /**
     * @param string $type
     * @return string
     * @throws InvalidArgumentException
     */
    public function getControllerName($type = '')
    {
        if (!is_string($type) || preg_match('/^[a-zA-Z0-9]*$/', $type) == 0) {
            throw new InvalidArgumentException(
                'argument hast to be a string and has to care the ' .
                'php classname convention.'
            );
        }

        if ($type != '') {
            $controllerName = 'Controller_' . ucfirst($type);
        } else {
            $controllerName = 'Vmvc_Controller';
        }

        return $controllerName;
    }
}
