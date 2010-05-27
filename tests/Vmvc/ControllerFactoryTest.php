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

require_once 'tests/VmvcTestCase.php';
require_once 'Vmvc/ServiceProviderInterface.php';
require_once 'Vmvc/Exception.php';
require_once 'Vmvc/Controller.php';
require_once 'Vmvc/ControllerFactory.php';

require_once 'tests/_files/TestController.php';

/**
 * Test class for Vmvc_ControllerFactory.
 * Generated by PHPUnit on 2010-03-26 at 09:42:31.
 * 
 */
class Vmvc_ControllerFactoryTest extends VmvcTestCase
{
    /**
     * @var Vmvc_ControllerFactory
     */
    protected $object;

    protected $requestMock;
    protected $responseMock;
    protected $serviceProviderMock;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->requestMock = $this->getMock('Vmvc_Request');
        $this->responseMock = $this->getMock('Vmvc_Response');
        $this->object = new Vmvc_ControllerFactory($this->requestMock,
                                                   $this->responseMock);

        $this->serviceProviderMock = $this->getMock('Vmvc_ServiceProviderInterface',
                                                     array('getServiceObject',
                                                           'getRequestService',
                                                           'getResponseService',
                                                           'getArrayObjectService'));
    }

    public function testGetController()
    {
        $controller = $this->object->getController();
        $this->assertTrue($controller instanceof Vmvc_Controller);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetControllerWithWrongArgument()
    {
        $this->object->getController(1);
    }

    public function testGetControllerWithServiceParam()
    {
        $serviceProviderMock = $this->serviceProviderMock;
        $serviceProviderMock->expects($this->at(0))
                             ->method('getServiceObject')
                             ->with($this->equalTo('request'))
                             ->will($this->returnValue(
                                            $this->getMock('Vmvc_Request')));

        $serviceProviderMock->expects($this->at(1))
                             ->method('getServiceObject')
                             ->with($this->equalTo('response'))
                             ->will($this->returnValue(
                                            $this->getMock('Vmvc_Response')));

        $serviceProviderMock->expects($this->at(2))
                             ->method('getServiceObject')
                             ->with($this->equalTo('arrayObject'))
                             ->will($this->returnValue(new ArrayObject()));

        $this->object->setServiceProvider($serviceProviderMock);
        
        $controller = $this->object->getController('test');
        $this->assertTrue($controller instanceof Vmvc_Controller);
    }

    /**
     * @expectedException Vmvc_Exception
     */
    public function testGetControllerWithMissingServiceContainer()
    {
        $this->object->getController('test');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetControllerWithWrongCharactersInArgument()
    {
         $this->object->getController('~');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetControllerWithMissingServiceName()
    {
        $serviceProviderMock = $this->serviceProviderMock;
        $serviceProviderMock->expects($this->at(0))
                            ->method('getServiceObject')
                            ->with($this->equalTo('request'))
                            ->will($this->returnValue(null));

        $this->object->setServiceProvider($serviceProviderMock);

        $controller = $this->object->getController('test');
    }

    public function testGetInstance()
    {
        $controller = $this->object->getInstance('Vmvc_Controller');
        $this->assertType('Vmvc_Controller', $controller);
    }

    public function testGetInstanceWithHelperBroker()
    {
        $helperBrokerMock = $this->getMock('Vmvc_HelperBroker');
        $this->object->setHelperBroker($helperBrokerMock);
        $this->object->getInstance('Vmvc_Controller');
    }

    public function testGetInstanceWithArgs()
    {
        $args = array($this->requestMock, $this->responseMock, new ArrayObject());
        $controller = $this->object->getInstance('TestController', $args);
        $this->assertType('TestController', $controller);
    }
}
?>
