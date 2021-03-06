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

require_once 'tests/VmvcTestCase.php';

/**
 * ControllerTest
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
class Vmvc_ControllerTest extends VmvcTestCase
{
    /**
     * @var Vmvc_Controller
     */
    protected $object;

    protected $requestMock;
    protected $responseMock;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->requestMock = $this->getMockWithoutDependencies('Vmvc_Request');
        $this->responseMock = $this->getMockWithoutDependencies(
            'Vmvc_Response'
        );
        $this->object = new Vmvc_Controller(
            $this->requestMock, $this->responseMock
        );
    }

    public function testGetParam()
    {
        $this->requestMock->expects($this->any())
            ->method('getVar')
            ->with($this->equalTo('test'))
            ->will($this->returnValue('testValue'));
        $param = $this->object->getParam('test');
        $this->assertEquals('testValue', $param);
    }

    public function testGetPostParam()
    {
        $this->requestMock->expects($this->any())
            ->method('getPostVar')
            ->with($this->equalTo('test'))
            ->will($this->returnValue('testValue'));
        $param = $this->object->getPostParam('test');
        $this->assertEquals('testValue', $param);
    }

    public function testSetData()
    {
        $this->responseMock->expects($this->any())
            ->method('setData')
            ->with($this->equalTo('test'));
        $this->object->setData('test', 'testValue');
    }

    public function testGetData()
    {
        $this->responseMock->expects($this->any())
            ->method('getData')
            ->with($this->equalTo('test'))
            ->will($this->returnValue('testValue'));
        $data = $this->object->getData('test');
        $this->assertEquals('testValue', $data);
    }

    public function testAddHeader()
    {
        $this->responseMock->expects($this->any())
            ->method('addHeader')
            ->with($this->equalTo('test'));
        $this->object->addHeader('test');
    }

    public function testClearHeaders()
    {
        $this->responseMock->expects($this->any())
            ->method('clearHeaders');
        $this->object->clearHeaders();
    }

    public function testSetHelperBroker()
    {
        $helperBrokerMock = $this->getMock('Vmvc_HelperBroker');
        $this->object->setHelperBroker($helperBrokerMock);

        $helperBrokerAttr = $this->readAttribute(
            $this->object, '_helperBroker'
        );
        $this->assertSame($helperBrokerMock, $helperBrokerAttr);
    }

    public function testCallHelper()
    {
        $helperBrokerMock = $this->getMock(
            'Vmvc_HelperBroker', array('callAlias')
        );
        $helperBrokerMock->expects($this->once())
            ->method('callAlias')
            ->with(
                $this->equalTo('test'),
                $this->equalTo(array())
            );
        $this->object->setHelperBroker($helperBrokerMock);

        $return = $this->object->__call('test', array());
    }

    /**
     * @expectedException Vmvc_Exception
     */
    public function testCallHelperBrokerIsNotSet()
    {
        $this->object->__call('test', array());
    }

    public function testGetControllerWithNamespace()
    {
        $object = new Controller_Test(
            $this->requestMock, $this->responseMock, new ArrayObject()
        );
        $this->assertEquals('Test', $object->getControllerName());
    }

    public function testGetControllerName()
    {
        $this->assertEquals(
            'Vmvc_Controller', $this->object->getControllerName()
        );
    }

    public function testExecute()
    {
    }
}
?>
