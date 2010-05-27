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


/**
 * View
 *
 * @package    Vmvc
 * @author     Joscha Meyer <schnipseljagd@googlemail.com>
 * @copyright  2010 Joscha Meyer <schnipseljagd@googlemail.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version
 * @link
 * @since      Release 0.1
 */
class Vmvc_View extends Vmvc_ViewScript
{
    /**
     * @var Vmvc_Response
     */
    protected $response;
    /**
     * @var Vmvc_HttpResponse
     */
    protected $httpResponse;
    
    /**
     * @param Vmvc_Response $response
     */
	public function __construct(Vmvc_Response $response)
    {
        $this->response = $response;
    }
    
    /**
     * @param mixed $value
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
     * @throws InvalidArgumentException
     * @param string $viewScriptPath
     * @return string
     */
    public function render($viewScriptPath)
    {
        $this->validateScriptPath($viewScriptPath);
        $viewScriptPath .= $this->getViewScriptSuffix();

        $headers = $this->response->getHeaders();

        $scriptHeader = $this->renderHeaders($headers);
        
        return $this->doRender($viewScriptPath, $scriptHeader);
    }

    public function setHttpResponse(Vmvc_HttpResponse $httpResponse)
    {
        $this->httpResponse = $httpResponse;
    }

    /**
     * render an array of strings with the php-header-function
     * @param array $headers
     * @return string
     */
    protected function renderHeaders(array $headers)
    {
        if($this->httpResponse===null) {
            throw new RuntimeException('HttpResponse is not set.');
        }
        ob_start();
        foreach($headers as $header) {
            $this->httpResponse->setHeader($header);
        }
        $headeroutput = ob_get_clean();

        return $headeroutput;
    }

    protected function getViewScriptSuffix()
    {
        $contentSubType = ucfirst($this->response->getContentSubType());
        if($contentSubType=='Html') {
            return '';
        }
        return $contentSubType;
    }
}
?>