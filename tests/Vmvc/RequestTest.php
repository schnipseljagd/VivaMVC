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
 * Test class for Vmvc_Request.
 * Generated by PHPUnit on 2010-03-26 at 09:44:26.
 */
class Vmvc_RequestTest extends VmvcTestCase
{
    /**
     * @var Vmvc_Request
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $vars = array(
            'controller' => 'default',
            '' => 'test',
            'test' => '',
        );
        $postVars = array(
            'action' => 'search',
            '' => 'test',
            'test' => '',
        );
        $serverVars = array(
            'REQUEST_URI' => '/en/index/index?test=testvalue',
        );

        $this->object = new Vmvc_Request($vars, $postVars, $serverVars);
    }

    public function testGetUriParams()
    {
        $params = $this->object->getUriParams();
        $this->assertSame(array('en', 'index', 'index'), $params);
    }

    public function testGetUriWasNotFound()
    {
        $request = new Vmvc_Request(array(), array(), array());
        $uri = $request->getUri();
        $this->assertNull($uri);
    }

    public function testGetVar()
    {
        $var = $this->object->getVar('controller');
        $this->assertEquals($var, 'default');

        $var = $this->object->getVar('');
        $this->assertEquals($var, 'test');

        $var = $this->object->getVar('test');
        $this->assertEquals($var, '');
    }

    public function testGetVarWasNotFound()
    {
        $var = $this->object->getVar('falsetest');
        $this->assertNull($var);
    }

    public function testGetVars()
    {
        $this->assertInternalType('array', $this->object->getVars());
    }

    public function testGetPostVar()
    {
        $var = $this->object->getPostVar('action');
        $this->assertEquals($var, 'search');

        $var = $this->object->getPostVar('');
        $this->assertEquals($var, 'test');

        $var = $this->object->getPostVar('test');
        $this->assertEquals($var, '');
    }

    public function testGetPostVarWasNotFound()
    {
        $var = $this->object->getPostVar('falsetest');
        $this->assertNull($var);
    }

    public function testGetPostVars()
    {
        $this->assertInternalType('array', $this->object->getPostVars());
    }

    public function testIsXmlHttpRequest()
    {
        $request = new Vmvc_Request(array(), array(),
                                    array(
                                        'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
                                    ));
        $result = $request->isXmlHttpRequest();
        $this->assertTrue($result);
    }
}
?>
