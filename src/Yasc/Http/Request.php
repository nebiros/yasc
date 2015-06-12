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
class Yasc_Http_Request {
    const METHOD_HEAD = "head";
    const METHOD_GET = "get";
    const METHOD_POST = "post";
    const METHOD_PUT = "put";
    const METHOD_DELETE = "delete";
    const METHOD_PATCH = "patch";
    const METHOD_OPTIONS = "options";

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
     * @var Yasc_Http_Header
     */
    protected $_headers = null;

    public function __construct() {
        $this->_headers = new Yasc_Http_Header(Yasc_Http_Header::extract());

        $this->setCurrentUrl();
    }

    /**
     * @return array
     */
    public function getHeaders() {
        return $this->_headers;
    }

    /**
     *
     * @return string
     */
    public function getMethod() {
        return strtolower($_SERVER["REQUEST_METHOD"]);
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
     * @return Yasc_Http_Request
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
     * @return Yasc_Http_Request
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
     * @return Yasc_Http_Request
     */
    public function setCurrentUrl() {
        $result = $this->buildUrl();

        $this->_serverUrl = $result["server_url"];
        $this->_url = $result["url"];
        $this->_urlComponents = $result["url_components"];
        $this->_urlPattern = $result["url_pattern"];

        return $this;
    }

    /**
     *
     * @return array
     */
    public function getUrlComponents() {
        return $this->_urlComponents;
    }

    /**
     *
     * @return bool
     */
    public function isGet() {
        return $this->getMethod() === self::METHOD_GET;
    }

    /**
     *
     * @return bool
     */
    public function isPost() {
        return $this->getMethod() === self::METHOD_POST;
    }

    /**
     *
     * @return bool
     */
    public function isPut() {
        return $this->getMethod() === self::METHOD_PUT;
    }

    /**
     *
     * @return bool
     */
    public function isPatch() {
        return $this->getMethod() === self::METHOD_PATCH;
    }

    /**
     *
     * @return bool
     */
    public function isDelete() {
        return $this->getMethod() === self::METHOD_DELETE;
    }

    /**
     *
     * @return bool
     */
    public function isHead() {
        return $this->getMethod() === self::METHOD_HEAD;
    }

    /**
     *
     * @return bool
     */
    public function isOptions() {
        return $this->getMethod() === self::METHOD_OPTIONS;
    }

    /**
     *
     * @return bool
     */
    public function isXhr() {
        if (isset($this->_headers["HTTP_X_REQUESTED_WITH"]) && $this->_headers["HTTP_X_REQUESTED_WITH"] === "XMLHttpRequest") {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isSecureHttp() {
        switch (true) {
            case (true === isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on" || $_SERVER["HTTPS"] === true)):
            case (true === isset($_SERVER["HTTP_SCHEME"]) && ($_SERVER["HTTP_SCHEME"] == "https")):
            case (true === isset($_SERVER["SERVER_PORT"]) && ($_SERVER["SERVER_PORT"] == 443)):
                return true;
                break;
        }

        return false;
    }

    /**
     * Build URL.
     *
     * @param string $serverName
     * @param string $path
     * @return array
     */
    public function buildUrl($serverName = null, $path = null) {
        if (null === $serverName || $serverName == $this->getUrlDelimiter()) {
            $httpHost = $serverName = $_SERVER["HTTP_HOST"];
        }

        if (false !== ($port = strstr($serverName, $this->getUrlVariable()))) {
            $serverName = str_replace($port, "", $serverName);
            $port = str_replace($this->_urlVariable, "", $port);
        } else {
            $port = $_SERVER["SERVER_PORT"];
        }

        $url = $this->getScheme();

        if ($this->isSecureHttp()) {
            $url .= "s";
        }

        if (substr($url, -1) != "s") {
            if ($port == $this->getSslPort()) {
                $url .= "s";
            }
        }

        $url .= "://";

        if (isset($httpHost)) {
            if (false !== strstr($httpHost, $port)) {
                $url .= $serverName . ":" . $port;
            } else {
                $url .= $serverName;
            }
        } else if (($port != $this->getDefaultPort())
            && $port != $this->getSslPort()) {
            $url .= $serverName . ":" . $port;
        } else {
            $url .= $serverName;
        }

        $result = array("server_url" => $url);

        if (false === is_string($path)) {
            $path = $_SERVER["REQUEST_URI"];
        }

        if (isset($path[0]) && $path[0] != $this->getUrlDelimiter()) {
            $path = $this->getUrlDelimiter() . $path;
        }

        $url .= $path;

        $result["url"] = trim($url, $this->getUrlDelimiter());
        $result["url_components"] = parse_url($result["url"]);

        $path = (isset($result["url_components"]["path"])) ? $result["url_components"]["path"] : "";
        $urlPattern = str_replace($_SERVER["SCRIPT_NAME"], "", rtrim($path, "/"));
        $result["url_pattern"] = ($urlPattern) ? $urlPattern : $this->getUrlDelimiter();

        return $result;
    }
}
