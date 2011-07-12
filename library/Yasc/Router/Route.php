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
 * @copyright Copyright (c) 2010 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 * Route.
 *
 * @package Yasc
 * @subpackage Yasc_Router
 * @copyright Copyright (c) 2010 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_Router_Route {
    /**
     * Array of script mapped functions, each element is
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
     * @param array $functions
     */
    public function __construct( Array $functions ) {
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
    public function match( Array $functions = null ) {
        if ( null !== $functions ) {
            $this->_functions = $functions;
        }

        if ( null === $this->_functions ) {
            throw new Yasc_Router_Exception( 'No user defined functions' );
        }

        $urlPath = explode( $this->_http->getUrlDelimiter(), $this->_http->getUrlPattern() );

        foreach ( $this->_functions AS $function ) {
            if ( false === ( $function instanceof Yasc_Function ) ) {
                throw new Yasc_Router_Exception( 'Function is not a instance of Yasc_Function' );
            }

            $annotationPath = explode( $this->_http->getUrlDelimiter(), $function->getAnnotation()->getPattern() );

            // If it's not the same path size, jump next function.
            if ( count( $urlPath ) != count( $annotationPath ) ) {
                continue;
            }

            $partsMatch = true;

            foreach ( $urlPath as $pos => $part ) {
                $var = $annotationPath[$pos];

                // Assign url part value to match the entire annotation path.
                if ( substr( $annotationPath[$pos], 0, 1 ) === $this->_http->getUrlVariable() ) {
                    $var = $part;
                }

                // If one of the parts of the url doesn't match de annotation
                // path then jump to the next function.
                if ( strtolower( $part ) != strtolower( $var ) ) {
                    $partsMatch = false;
                    break;
                }

                // Assign variable value to each method..
                if ( $var ) {
                    switch ( $function->getMethod() ) {
                        case Yasc_Router::METHOD_DELETE:
                            break;

                        case Yasc_Router::METHOD_PUT:
                            break;

                        case Yasc_Router::METHOD_POST:
                            $_POST[$annotationPath[$pos]] = $var;
                            break;

                        case Yasc_Router::METHOD_GET:
                        default:
                            $_GET[$annotationPath[$pos]] = $var;
                            break;
                    }

                    $function->setParam( $annotationPath[$pos], $var );
                }
            }

            // Url parts doesn't match the entire annotation path, go to the
            // next function.
            if ( false === $partsMatch ) {
                continue;
            }
            
            if ( strtolower( $_SERVER["REQUEST_METHOD"] ) != $function->getMethod() ) {
                continue;
            }

            // So everything seems to be fine, execute first occurrence.
            $this->_requestedFunction = $function;

            break;
        }

        return $this;
    }
}
