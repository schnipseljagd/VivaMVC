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
 * @category  MVC
 * @package   Vmvc
 * @author    Joscha Meyer <schnipseljagd@googlemail.com>
 * @copyright 2010 Joscha Meyer <schnipseljagd@googlemail.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://vivamvc.schnipseljagd.org/
 * @since     0.1
 */


/**
 * ErrorHandler
 *
 * @category  MVC
 * @package   Vmvc
 * @author    Joscha Meyer <schnipseljagd@googlemail.com>
 * @copyright 2010 Joscha Meyer <schnipseljagd@googlemail.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: 0.3.1
 * @link      http://vivamvc.schnipseljagd.org/
 * @since     0.1
 */
class Vmvc_ErrorHandler
{
    /**
     * @var Vmvc_Response
     */
    protected $response;
    /**
     * @var boolean
     */
    protected $isError = false;
    /**
     * @var string
     */
    protected $error;
    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @param Vmvc_Response $response
     */
    public function __construct(Vmvc_Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return boolean
     */
    public function isError()
    {
        return $this->isError;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param string $name
     * @param string $message
     * @param string $header
     */
    public function setError($name, $message, $header = null)
    {
        $this->error = $name;
        $this->errorMessage = $message;
        $this->isError = true;
        
        if ($header !== null && is_string($header)) {
            $this->setResponseHeader($header);
        }
    }

    /**
     * @param string $message
     */
    public function set404Error($message = null)
    {
        if ($message === null) {
            $message = 'Page Was Not Found';
        }
        $this->setError('404', $message);

        $this->setResponseHeader("HTTP/1.1 404 {$message}");
    }

    /**
     * @param string $header
     * @throws Exception
     */
    protected function setResponseHeader($header)
    {
        $this->response->clearHeaders();
        $this->response->addHeader($header);
    }
}
