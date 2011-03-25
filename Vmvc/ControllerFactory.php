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
     * @param Vmvc_Request $request
     * @param Vmvc_Response $response
     */
    public function __construct(Vmvc_Request $request, Vmvc_Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @param Vmvc_ServiceProviderInterface $serviceProvider
     */
    public function setServiceProvider(
        Vmvc_ServiceProviderInterface $serviceProvider
    ) {
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
        $controllerName = $this->getControllerName($type);
        $controllerConstructorReflection = $this->getConstructorReflection(
            $controllerName
        );

        $params = $controllerConstructorReflection->getParameters();

        // instantiate controller with params Vmvc_Request and Vmvc_Response
        if (count($params) == 2
            && $params[0]->getClass()->name == 'Vmvc_Request'
            && $params[1]->getClass()->name == 'Vmvc_Response'
        ) {
            return $this->getInstance($controllerName);
        }

        // instantiate controller with more params
        if ($this->serviceProvider !== null) {
            $args = $this->getServiceObjects($controllerConstructorReflection);

            return $this->getInstance($controllerName, $args);
        }

        // care no other options
        throw new Vmvc_Exception(
            'Controller could not instantiated. ' .
            'Perhaps set the ServiceContainer?'
        );
    }

    /**
     * @param string $controllerName
     * @param array|null $args
     * @return Vmvc_Controller
     * @throws InvalidArgumentException
     */
    public function getInstance($controllerName, $args = null)
    {
        if ($args === null) {
            $args = array($this->request, $this->response);
        }

        $controllerReflection = new ReflectionClass($controllerName);
        $controller = $controllerReflection->newInstanceArgs($args);

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

    /**
     * @param string $controllerName
     * @return ReflectionMethod
     */
    protected function getConstructorReflection($controllerName)
    {        
        $controllerReflection = new ReflectionClass($controllerName);
        $constructor = $controllerReflection->getConstructor();
        return $constructor;
    }

    /**
     * Get the service objects needed by the controller
     * @param ReflectionMethod $constructorReflection
     * @return array
     */
    protected function getServiceObjects(
        ReflectionMethod $constructorReflection
    ) {
        $paramServiceObjects = array();
        $parameters = $constructorReflection->getParameters();
        
        foreach ($parameters as $parameter) {
            $serviceObject = $this->serviceProvider
                ->getServiceObject($parameter->name);
            
            if (!is_object($serviceObject)) {
                throw new RuntimeException(
                    'Service has to return an object. id: ' . $parameter->name
                );
            }
            $paramServiceObjects[] = $serviceObject;
        }

        return $paramServiceObjects;
    }
}
