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
require_once 'tests/_files/TestHelper.php';

/**
 * Test class for Vmvc_View.
 * Generated by PHPUnit on 2010-03-26 at 09:44:46.
 *
 * CARE:
 * This Test needs a test.php which prints test for testing
 * @link test.php
 * @TODO fix Render Test, its very ugly
 *
 *
 */
class Vmvc_ViewScriptTest extends VmvcTestCase
{
    /**
     * @var Vmvc_ViewScript
     */
    protected $object;

    protected $viewScriptPath = 'tests/_files/test';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Vmvc_ViewScript(array('blub' => 'blaa'));
    }

    public function testSetData()
    {
        $this->object->setData('test', 'testValue');
        return $this->object;
    }

    /**
     * @depends testSetData
     */
    public function testGetData(Vmvc_ViewScript $object)
    {
        $data = $object->getData('test');
        $this->assertEquals($data, 'testValue');
    }

    public function testGetDataWasNotFound()
    {
        $data = $this->object->getData('test');
        $this->assertNull($data);
    }

    public function testRegisterHelper()
    {
        $helperMock = $this->getMock(
            'Vmvc_ViewHelperInterface', array(), array(), 'TestRHelper'
        );

        $this->object->registerHelper($helperMock);

        $viewHelpers = $this->readAttribute($this->object, 'viewHelpers');

        $this->assertInstanceOf('TestRHelper', $viewHelpers['TestRHelper']);
    }

    public function testGetHelper()
    {
        $helperMock = $this->getMock(
            'Vmvc_ViewHelperInterface', array(), array(), 'TestStringHelper'
        );
        $helperMock->expects($this->any())
                   ->method('execute')
                   ->will($this->returnValue('returnTest'));

        $this->object->registerHelper($helperMock);

        $helperReturn = $this->object->getHelper('testString');
        
        $this->assertEquals('returnTest', $helperReturn);
    }

    /**
     * @expectedException Vmvc_Exception
     */
    public function testGetHelperNotRegistered()
    {
        $helperReturn = $this->object->getHelper('test');

        $this->assertEquals('returnTest', $helperReturn);
    }

    public function testMagicMethodCall()
    {
        $helperMock = $this->getMock(
            'Vmvc_ViewHelperInterface', array(), array(), 'TestString2Helper'
        );
        $helperMock->expects($this->any())
                   ->method('execute')
                   ->will($this->returnValue('returnTest'));

        $this->object->registerHelper($helperMock);

        $helperReturn = $this->object->testString2();

        $this->assertEquals('returnTest', $helperReturn);
    }

    public function testRender()
    {
        $return = $this->object->render($this->viewScriptPath);
        $this->assertEquals($return, 'test');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRenderArgument()
    {
        $this->object->render('~');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRenderArgumentHasToBeAString()
    {
        $this->object->render(1);
    }
}
?>
