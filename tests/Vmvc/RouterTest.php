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

/**
 * Test class for Vmvc_Router.
 * Generated by PHPUnit on 2010-03-26 at 09:44:41.
 * 
 */
class Vmvc_RouterTest extends VmvcTestCase
{
    /**
     * @var Vmvc_Router
     */
    protected $object;
    /**
     * @var Vmvc_Request
     */
    protected $requestMock;

    protected $requestUri = array(
        '/index/index'
    );

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->requestMock = $this->getMockWithoutDependencies('Vmvc_Request');
        
        $this->object = new Vmvc_Router($this->requestMock);
    }

    public function testAddRoute()
    {
        $this->requestMock->expects($this->any())
                          ->method('getUri')
                          ->will($this->returnValue($this->requestUri));

        $routeMock = $this->getMockWithoutDependencies('Vmvc_Route');

        $this->object->addRoute($routeMock);
    }

    public function testGetRoute()
    {
        $this->requestMock->expects($this->any())
                          ->method('getUri')
                          ->will($this->returnValue(null));

        $options = array(
            'lang' => 'en',
            'controller' => 'index',
            'action' => 'index',
        );
        $defaultRoute = new Vmvc_Route(
            '/^[a-z]{2}$:lang/:controller/:action/', $options
        );
        $this->object->addRoute($defaultRoute);
        $this->object->setDefaultRoute($defaultRoute, '/en/index/index/');
        
        $route = $this->object->getRoute();

        $this->assertSame($defaultRoute, $route);
    }
}
?>
