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
 * Test class for Vmvc_Controller.
 * Generated by PHPUnit on 2010-03-26 at 09:42:26.
 *
 */
class Vmvc_LayoutViewTest extends VmvcTestCase
{
    /**
     * @var Vmvc_LayoutView
     */
    protected $object;

    protected $responseMock;

    protected $layoutPath = 'tests/_files/testLayout';
    protected $viewScriptPath = 'tests/_files/test';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->responseMock = $this->getMockWithoutDependencies(
            'Vmvc_Response'
        );
        
        $this->object = new Vmvc_LayoutView($this->responseMock);
    }

    public function testSetLayoutScript()
    {
        $this->object->setLayoutScript($this->layoutPath);
        
        $this->assertEquals(
            $this->layoutPath,
            $this->readAttribute($this->object, 'layoutScriptPath')
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetLayoutScriptInvalidArgument()
    {
        $this->object->setLayoutScript(1);
    }

    public function testRender()
    {
        $this->responseMock->expects($this->any())
                           ->method('getHeaders')
                           ->will($this->returnValue(array()));
        
        $httpResponseMock = $this->getHttpResponseMock();
        $this->object->setHttpResponse($httpResponseMock);

        $this->object->setLayoutScript($this->layoutPath);

        $return = $this->object->render($this->viewScriptPath);
        $this->assertEquals('startLayouttestendLayout', $return);

        return $this->object;
    }

    public function testRenderWithoutLayout()
    {
        $this->responseMock->expects($this->any())
                           ->method('getHeaders')
                           ->will($this->returnValue(array()));

        $httpResponseMock = $this->getHttpResponseMock();
        $this->object->setHttpResponse($httpResponseMock);

        $return = $this->object->render($this->viewScriptPath);
        $this->assertEquals($return, 'test');
    }

    /**
     * @depends testRender
     */
    public function testIncludeContentScript(Vmvc_LayoutView $object)
    {
        $contentScriptPath = $this->readAttribute($object, 'contentScriptPath');
        $this->assertEquals($this->viewScriptPath, $contentScriptPath);

        $return = $object->includeContentScript();
        $this->assertEquals('test', $return);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testIncludeContentScriptNoContentScriptDefined()
    {
        $this->object->includeContentScript();
    }

    protected function getHttpResponseMock()
    {
        return $this->getMockWithoutDependencies('Vmvc_HttpResponse');
    }
}
?>
