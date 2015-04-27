<?php

/**
 * Yasc.
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/nebiros/yasc/raw/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mail@jfalvarez.com so we can send you a copy immediately.
 *
 * @category Yasc
 * @package Yasc
 * @subpackage Yasc_Http
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 *
 * @package Yasc
 * @subpackage Yasc_Http
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_Http_Response {
    /**
     * @var int
     */
    protected $_status = 200;
    
    /**
     * @var Yasc_Http_Header
     */
    protected $_headers = null;
    
    /**
     * @var Yasc_Http_Header
     */
    protected $_headersRaw = null;
    
    /**
     * @var string
     */
    protected $_body = "";
    
    /**
     * @var int
     */
    protected $_length = 0;
    
    public static $statusCodes = array(
        100 => "Continue", 
        101 => "Switching Protocols", 
        102 => "Processing", 
        200 => "OK", 
        201 => "Created", 
        202 => "Accepted", 
        203 => "Non-Authoritative Information", 
        204 => "No Content", 
        205 => "Reset Content", 
        206 => "Partial Content", 
        207 => "Multi-Status", 
        208 => "Already Reported", 
        226 => "IM Used", 
        300 => "Multiple Choices", 
        301 => "Moved Permanently", 
        302 => "Found", 
        303 => "See Other", 
        304 => "Not Modified", 
        305 => "Use Proxy", 
        306 => "Switch Proxy", 
        307 => "Temporary Redirect", 
        308 => "Permanent Redirect", 
        400 => "Bad Request", 
        401 => "Unauthorized", 
        402 => "Payment Required", 
        403 => "Forbidden", 
        404 => "Not Found", 
        405 => "Method Not Allowed", 
        406 => "Not Acceptable", 
        407 => "Proxy Authentication Required", 
        408 => "Request Timeout", 
        409 => "Conflict", 
        410 => "Gone", 
        411 => "Length Required", 
        412 => "Precondition Failed", 
        413 => "Request Entity Too Large", 
        414 => "Request-URI Too Long", 
        415 => "Unsupported Media Type", 
        416 => "Requested Range Not Satisfiable", 
        417 => "Expectation Failed", 
        418 => "I'm a teapot", 
        419 => "Authentication Timeout", 
        422 => "Unprocessable Entity", 
        423 => "Locked", 
        424 => "Failed Dependency", 
        426 => "Upgrade Required", 
        428 => "Precondition Required", 
        429 => "Too Many Requests", 
        431 => "Request Header Fields Too Large", 
        500 => "Internal Server Error", 
        501 => "Not Implemented", 
        502 => "Bad Gateway", 
        503 => "Service Unavailable", 
        504 => "Gateway Timeout", 
        505 => "HTTP Version Not Supported", 
        506 => "Variant Also Negotiates", 
        507 => "Insufficient Storage", 
        508 => "Loop Detected",  
        510 => "Not Extended", 
        511 => "Network Authentication Required"
    );
    
    public function __construct($body = "", $status = 200, Array $headers = null, Array $headersRaw = null) {
        $this->setBody($body);
        $this->setStatus($status);
        
        $this->_headers = new Yasc_Http_Header(array("Content-Type" => array("value" => "text/html")));
        if (null !== $headers) {
            $this->addHeaders($headers);
        }
        
        $this->_headersRaw = new Yasc_Http_Header();
        if (null !== $headersRaw) {
            $this->addHeadersRaw($headersRaw);
        }
    }
    
    public function getStatus() {
        return $this->_status;
    }

    public function setStatus($status) {
        $this->_status = (int) $status;
        
        return $this;
    }

    public function getHeaders() {
        return $this->_headers;
    }

    public function setHeaders(Array $headers) {
        $this->_headers = $headers;
        
        return $this;
    }
    
    public function addHeaders(Array $headers) {
        $this->_headers = $headers + (array) $this->_headers;
        
        return $this;
    }
    
    public function getHeadersRaw() {
        return $this->_headersRaw;
    }

    public function setHeadersRaw(Array $headersRaw) {
        $this->_headersRaw = $headersRaw;
        
        return $this;
    }
    
    public function addHeadersRaw(Array $headersRaw) {
        $this->_headersRaw = $headersRaw + (array) $this->_headersRaw;
        
        return $this;
    }
    
    public function getBody() {
        return $this->_body;
    }
    
    public function setBody($body) {
        $this->_body = $body;       
        $this->_length = strlen($this->_body);
        
        return $this;
    }
    
    public function addBody($body) {
        $this->_body .= $body;
        
        return $this;
    }
    
    public function outputBody() {
        echo $this->_body;
    }
    
    public function clearBody() {
        $this->setBody("");
        return $this;
    }
    
    public function getLength() {
        return $this->_length;
    }
    
    public function sendHeaders() {
        if (headers_sent()) {
            return $this;
        }
        
        $httpCodeSent = false;

        foreach ($this->getHeadersRaw() as $header) {
            if (!$httpCodeSent && $this->getStatus()) {
                header($header, true, $this->getStatus());
                $httpCodeSent = true;
            } else {
                header($name);
            }
        }
        
        foreach ($this->getHeaders() as $headerName => $headerData) {
            if (!isset($headerData["replace"])) {
                $headerData["replace"] = true;
            }
            
            if (!$httpCodeSent && $this->getStatus()) {
                header("{$headerName}: {$headerData["value"]}", $headerData["replace"], $this->getStatus());
                $httpCodeSent = true;
            } else {
                header("{$headerName}: {$headerData["value"]}", $headerData["replace"]);
            }
        }
        
        if (!$httpCodeSent) {
            header(sprintf("HTTP/1.1 %s", $this->getStatus()));
            $httpCodeSent = true;
        }
        
        return $this;
    }
    
    public function sendResponse() {
        $this->sendHeaders();
        
        if ($this->isRedirection()) {
            exit();
        }
        
        if (!\Yasc_App::request()->isHead()) {
            $this->outputBody();
        }
    }
    
    public function redirect($url, $status = 302) {
        $this->setStatus($status);
        $this->setHeaders(array("Location" => array("value" => $url)));
        $this->stop();
    }
    
    public function redirectTo($path, $status = 302) {
        $this->setStatus($status);
        
        $router = new Yasc_Router();
        $url = $router->urlFor($path);
        
        $this->setHeaders(array("Location" => array("value" => $url)));
        $this->stop();
    }
    
    public function stop() {
        $this->clearBody();
    }
    
    /**
     * @return bool
     */
    public function isEmpty() {
        return in_array($this->_status, array(201, 204, 304));
    }
    
    /**
     * @return bool
     */
    public function isInformational() {
        return $this->_status >= 100 && $this->_status < 200;
    }
    
    /**
     * @return bool
     */
    public function isOk() {
        return $this->_status === 200;
    }
    
    /**
     * @return bool
     */
    public function isSuccessful() {
        return $this->_status >= 200 && $this->_status < 300;
    }
    
    /**
     * @return bool
     */
    public function isRedirect() {
        return in_array($this->_status, array(301, 302, 303, 307));
    }
    
    /**
     * @return bool
     */
    public function isRedirection() {
        return $this->_status >= 300 && $this->_status < 400;
    }
    
    /**
     * @return bool
     */
    public function isForbidden() {
        return $this->_status === 403;
    }
    
    /**
     * @return bool
     */
    public function isNotFound() {
        return $this->_status === 404;
    }
    
    /**
     * @return bool
     */
    public function isClientError() {
        return $this->_status >= 400 && $this->_status < 500;
    }
    
    /**
     * @return bool
     */
    public function isServerError() {
        return $this->_status >= 500 && $this->_status < 600;
    }
    
    public function __toString() {
        ob_start();
        $this->sendResponse();
        return ob_get_clean();
    }
}
