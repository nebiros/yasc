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
 * @copyright Copyright (c) 2010 Juan Felipe Alvarez Sadarriaga. (http://www.jfalvarez.com)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 * Class to handle routes.
 *
 * @package Yasc
 * @copyright Copyright (c) 2010 Juan Felipe Alvarez Sadarriaga. (http://www.jfalvarez.com)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author jfalvarez
 */
class Yasc_Router {
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const METHOD_PUT = 'put';
    const METHOD_DELETE = 'delete';

    /**
     * App class.
     *
     * @var Yasc_App
     */
    protected $_app = null;

    /**
     *
     * @param Yasc_App $app
     */
    public function __construct( Yasc_App $app = null ) {
        if ( null !== $app ) {
            $this->_app = $app;
        }
    }

    /**
     *
     * @param Yasc_App $app
     * @return Yasc_Router
     */
    public function setApp( Yasc_App $app ) {
        $this->_app = $app;
        return $this;
    }

    /**
     *
     * @return Yasc_App
     */
    public function getApp() {
        return $this->_app;
    }

    /**
     * Route requested url, find script function to be invoked.
     *
     * @return Yasc_Function
     */
    public function route() {
        $route = new Yasc_Router_Route( $this->getApp()->getFunctions() );
        $requestedFunction = $route->match()->getRequestedFunction();

        if ( null === $requestedFunction ) {
            throw new Yasc_Router_Exception( 'Requested function not found' );
        }

        if ( strtolower( $_SERVER['REQUEST_METHOD'] ) != $requestedFunction->getMethod() ) {
            throw new Yasc_Router_Exception( 'Invalid request method' );
        }

        return $requestedFunction;
    }
}