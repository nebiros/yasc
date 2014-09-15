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
 * @subpackage Yasc_Request
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 *
 * @package Yasc
 * @subpackage Yasc_Request
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_Request_Http {
    /**
     * Default scheme.
     *
     * @var string
     */
    protected $_scheme = "http";

    /**
     * Default port.
     *
     * @var int
     */
    protected $_defaultPort = 80;
    
    /**
     *
     * @var int
     */
    protected $_sslPort = 443;

    /**
     *
     * @var string
     */
    protected $_urlVariable = ":";
    
    /**
     *
     * @var string
     */
    protected $_urlDelimiter = "/";

    /**
     * Requested url, normalized.
     *
     * @var string
     */
    protected $_url = null;
    
    /**
     *
     * @var string
     */
    protected $_serverUrl = null;

    /**
     * Url pattern.
     *
     * @var string
     */
    protected $_urlPattern = null;

    /**
     * Url components.
     *
     * @var array
     */
    protected $_urlComponents = array();

    /**
     *
     * @param string $serverName 
     * @param string $uri
     */
    public function __construct($serverName = null, $uri = null) {
        $this->processUrl($serverName, $uri);
    }

    /**
     *
     * @return string
     */
    public function getScheme() {
        return $this->_scheme;
    }
    
    /**
     *
     * @param int $port
     * @return Yasc_Request_Http 
     */
    public function setDefaultPort($port) {
        $this->_defaultPort = (int) $port;
        return $this;
    }

    /**
     *
     * @return int
     */    
    public function getDefaultPort() {
        return $this->_defaultPort;
    }
    
    /**
     *
     * @param int $port
     * @return Yasc_Request_Http 
     */
    public function setSslPort($port) {
        $this->_sslPort = (int) $port;
        return $this;
    }    
    
    /**
     *
     * @return int
     */    
    public function getSslPort() {
        return $this->_sslPort;
    }

    /**
     *
     * @return string
     */    
    public function getUrlVariable() {
        return $this->_urlVariable;
    }

    /**
     *
     * @return string
     */    
    public function getUrlDelimiter() {
        return $this->_urlDelimiter;
    }

    /**
     *
     * @return string
     */    
    public function getUrl() {
        return $this->_url;
    }
    
    /**
     *
     * @return string
     */    
    public function getServerUrl() {
        return $this->_serverUrl;
    }

    /**
     *
     * @return string
     */    
    public function getUrlPattern() {
        return $this->_urlPattern;
    }

    /**
     *
     * @return array
     */    
    public function getUrlComponents() {
        return $this->_urlComponents;
    }

    /**
     * Process url requested.
     *
     * @param string $serverName
     * @param string $uri
     * @return Yasc_Request_Http
     */
    public function processUrl($serverName = null, $uri = null) {
        if (null === $serverName || $serverName == $this->_urlDelimiter) {
            $httpHost = $serverName = $_SERVER["HTTP_HOST"];
        }
        
        if (false !== ($port = strstr($serverName, $this->_urlVariable))) {
            $serverName = str_replace($port, "", $serverName);
            $port = str_replace($this->_urlVariable, "", $port);
        } else {
            $port = $_SERVER["SERVER_PORT"];
        }

        $url = $this->_scheme;

        if (gethostbyname($serverName) != $_SERVER["SERVER_ADDR"]) {
            if ($port == $this->_sslPort) {
                $url .= "s";
            }
        } else {
            switch (true) {
                case (true === isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on" || $_SERVER["HTTPS"] === true)):
                case (true === isset($_SERVER["HTTP_SCHEME"]) && ($_SERVER["HTTP_SCHEME"] == "https")):
                case (true === isset($_SERVER["SERVER_PORT"]) && ($_SERVER["SERVER_PORT"] == 443)):
                    $url .= "s";
                    break;
            }
        }

        $url .= "://";

        if (isset($httpHost)) {
            if (false !== strstr($httpHost, $port)) {
                $url .= $serverName . ":" . $port;                
            } else {
                $url .= $serverName;
            }            
        } else if (($port != $this->_defaultPort)
            && $port != $this->_sslPort) {
            $url .= $serverName . ":" . $port;
        } else {
            $url .= $serverName;
        }
        
        $this->_serverUrl = $url;
        
        if (false === is_string($uri)) {
            $uri = $_SERVER["REQUEST_URI"];
        }
        
        if ($uri[0] != $this->_urlDelimiter) {
            $uri = $this->_urlDelimiter . $uri;
        }        
        
        $url .= $uri;

        $this->_url = trim($url, $this->_urlDelimiter);
        $this->_urlComponents = parse_url($this->_url);

        $urlPattern = str_replace($_SERVER["SCRIPT_NAME"], "", rtrim($this->_urlComponents["path"], "/"));
        $this->_urlPattern = ($urlPattern) ? $urlPattern : $this->_urlDelimiter;

        return $this;
    }
    
    public function __toString() {
        return $this->_url;
    }
}
