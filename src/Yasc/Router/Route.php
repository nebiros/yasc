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
 * @subpackage Yasc_Router
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 * Route.
 *
 * Based on the original work from Limonade and Fabrice Luraine (@link http://www.limonade-php.net/)
 * and Zend Framework"s Zend_Controller_Router_Route_Module#match method (@link http://framework.zend.com/svn/framework/standard/tags/release-1.11.9/library/Zend/Controller/Router/Route/Module.php).
 * 
 * @package Yasc
 * @subpackage Yasc_Router
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_Router_Route {
    const SINGLE_ASTERISK_SUBPATTERN = "(?:/([^\/]*))?";
    const DOUBLE_ASTERISK_SUBPATTERN = "(?:/(.*))?";
    const OPTIONAL_SLASH_SUBPATTERN = "(?:/*?)";
    const NO_SLASH_ASTERISK_SUBPATTERN = "(?:([^\/]*))?";
    
    /**
     * Array of mapped functions, each element is
     * a Yasc_Function object.
     *
     * @var array
     */
    protected $_functions = array();

    /**
     * Requested function.
     *
     * @var Yasc_Function
     */
    protected $_requestedFunction = null;
    
    /**
     *
     * @var Yasc_Request_Http
     */
    protected $_http = null;
    
    /**
     *
     * @var string
     */
    protected $_pattern = null;
    
    /**
     *
     * @var array
     */
    protected $_matches = array();
    
    /**
     *
     * @var array
     */
    protected $_paramsList = array();

    /**
     *
     * @param array $functions
     */
    public function __construct(Array $functions) {
        $this->_functions = $functions;
        $this->_http = new Yasc_Request_Http();
    }

    /**
     *
     * @return array
     */
    public function getFunctions() {
        return $this->_functions;
    }

    /**
     *
     * @return Yasc_Function
     */
    public function getRequestedFunction() {
        return $this->_requestedFunction;
    }

    /**
     * Match url path, when a path is matched his script function is set.
     *
     * @param array $functions
     * @return Yasc_Router_Route
     */
    public function match(Array $functions = null) {
        if (null !== $functions) {
            $this->_functions = $functions;
        }

        if (null === $this->_functions) {
            throw new Yasc_Router_Exception("No user defined functions");
        }

        foreach ($this->_functions AS $function) {
            if (false === ($function instanceof Yasc_Function)) {
                throw new Yasc_Router_Exception("Function is not a instance of Yasc_Function");
            }

            if (strtolower($_SERVER["REQUEST_METHOD"]) != $function->getMethod()) {
                if (strtolower($_SERVER["REQUEST_METHOD"]) == Yasc_Router::METHOD_POST 
                    && true === array_key_exists("_method", $_POST)) {
                    if (strtolower($_POST["_method"]) == $function->getMethod()) {
                        if (true === $this->_lookup($function)) {
                            $this->_requestedFunction = $function;
                            break;
                        }
                    }

                    continue;
                }

                continue;
            } else {
                if (strtolower($_SERVER["REQUEST_METHOD"]) == Yasc_Router::METHOD_POST 
                    && true === array_key_exists("_method", $_POST)) {
                    if (strtolower($_POST["_method"]) == $function->getMethod()) {
                        if (true === $this->_lookup($function)) {
                            $this->_requestedFunction = $function;
                            break;
                        }
                    }

                    continue;
                } else {                
                    if (true === $this->_lookup($function)) {
                        $this->_requestedFunction = $function;
                        break;
                    }
                }

                continue;
            }
        }

        if (null === $this->_requestedFunction) {
            throw new Yasc_Router_Exception("Requested function not found, 
                request URI: '{$_SERVER["REQUEST_URI"]}', 
                request method: '{$_SERVER["REQUEST_METHOD"]}'");
        }
        
        $this->_requestedFunction->setParams((array) $this->_setupParams());

        return $this;
    }
    
    /**
     *
     * @param Yasc_Function $function
     * @return bool
     */
    protected function _lookup(Yasc_Function $function) {
        $annotationPattern = $function->getAnnotation()->getPattern();
        // stripslashes because we need to scape some of them when we use wildcards
        // like * or **.
        $annotationParts = explode($this->_http->getUrlDelimiter(), stripcslashes($annotationPattern));

        // regex route.
        if ($annotationPattern[0] == "^") {
            if (substr($annotationPattern, -1) != "$") {
                $annotationPattern .= "$";
            }

            $pattern = "#" . $annotationPattern . "#i";
        // slash route.
        } else if ($annotationPattern == $this->_http->getUrlDelimiter()) {
            $pattern = "#^" . self::OPTIONAL_SLASH_SUBPATTERN . "$#";
        } else {
            $parsed = array(); $paramsList = array(); $paramsCounter = 0;

            foreach ($annotationParts as $part) {
                if (true === empty($part)) {
                    continue;
                }

                $param = null;

                // extracting double asterisk **.
                if ($part == "**") {
                    $parsed[] = self::DOUBLE_ASTERISK_SUBPATTERN;
                    $param = $paramsCounter;
                // extracting single asterisk *.
                } else if ($part == "*") {
                    $parsed[] = self::SINGLE_ASTERISK_SUBPATTERN;
                    $param = $paramsCounter;
                // extracting named parameters :my_param.
                } else if ($part[0] == ":") {
                    if (true == preg_match("/^:([^\:]+)$/", $part, $matches)) {
                        $parsed[] = self::SINGLE_ASTERISK_SUBPATTERN;
                        $param = $matches[1];
                    }
                // *.* pattern.
                } else if (false !== strpos($part, "*")) {
                    $subParts = explode("*", $part);
                    $subParsed = array();

                    foreach($subParts as $subPart) {
                        $subParsed[] = preg_quote($subPart, "#");
                    }
                    
                    $parsed[] = "/" . implode(self::NO_SLASH_ASTERISK_SUBPATTERN, $subParsed);
                // everything else.
                } else {
                    $parsed[] = "/" . preg_quote($part, "#");
                }
                
                if (null === $param) {
                    continue;                        
                }
                
                if (false === array_key_exists($paramsCounter, $paramsList) 
                    || true === is_null($paramsList[$paramsCounter]) 
                    ) {
                    $paramsList[$paramsCounter] = $param;
                }

                $paramsCounter++;
            }

            $pattern = "#^" . implode("", $parsed) . self::OPTIONAL_SLASH_SUBPATTERN . "?$#i";
        }
                
        if (true == preg_match($pattern, $this->_http->getUrlPattern(), $matches)) {
            $this->_pattern = $pattern;
            $this->_matches = $matches;
            $this->_paramsList = (isset($paramsList)) ? $paramsList : array();
            
            return true;
        }
        
        return false;
    }
    
    /**
     *
     * @return array
     */
    protected function _setupParams() {
        if (count($this->_matches) < 2) {
            return;
        }
        
        $params = array();        
        array_shift($this->_matches);
        $matchesCount = count($this->_matches);
        $paramValues = array_values((array) $this->_paramsList);
        $namesCount = count($paramValues);

        if ($matchesCount < $namesCount) {
            $tmp = array_fill(0, $namesCount - $matchesCount, null);
            $this->_matches = array_merge($this->_matches, $tmp);
        } else if ($matchesCount > $namesCount) {
            $paramValues = range($namesCount, $matchesCount - 1);
        }

        $combination = array_combine($paramValues, $this->_matches);
        
        if (true === function_exists("array_replace")) {
            $params = array_replace($params, $combination);
        } else {
            $params = Yasc_Util::arrayReplace($params, $combination);
        }
        
        $pairs = array();
        
        foreach ($params as $index => $param) {
            if (false === strpos($param, $this->_http->getUrlDelimiter())) {
                continue;
            }
            
            $dobleAsteriskParam = $param;
            unset($params[$index]);
            $pairs = array_merge($pairs, explode($this->_http->getUrlDelimiter(), $dobleAsteriskParam));
        }
        
        // get params by pairs, like zend framework does, 
        // param1/value1/param2/value2, param1 will be the key of the param,
        // value1 the value of param1, if the value is not present NULL is his
        // value.
        // 
        // Check Zend_Controller_Router_Route_Module#match method and see how 
        // this works.
        if (($n = count($pairs)) > 0) {
            for ($i = 0; $i < $n; $i = $i + 2) {
                $key = urldecode($pairs[$i]);
                $val = isset($pairs[$i + 1]) ? urldecode($pairs[$i + 1]) : null;
                $params[$key] = (isset($params[$key]) ? (array_merge((array) $params[$key], array($val))) : $val);
            }
        }
        
        return $params;
    }
}
