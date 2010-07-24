<?php

/**
 * Class to handle routes.
 *
 * @author jfalvarez
 */
class Yasc_Router {
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const METHOD_PUT = 'put';
    const METHOD_DELETE = 'delete';

    /**
     * Bootstrap class.
     *
     * @var Yasc_Bootstrap
     */
    protected $_bootstrap = null;

    public function __construct( Yasc_Bootstrap $bootstrap = null ) {
        if ( null !== $bootstrap ) {
            $this->_bootstrap = $bootstrap;
        }
    }

    public function setBootstrap( Yasc_Bootstrap $bootstrap ) {
        $this->_bootstrap = $bootstrap;
        return $this;
    }

    public function getBoostrap() {
        return $this->_bootstrap;
    }

    /**
     * Route requested url, find script function to be invoked.
     *
     * @return void
     */
    public function route() {
        $route = new Yasc_Router_Route( $this->getBoostrap()->getFunctions() );

        $this->execute( $route->match()->getRequestedFunction() );
    }

    /**
     * Execute requested function.
     *
     * @return void
     */
    public function execute( Yasc_Function $requestedFunction ) {
        if ( null === $requestedFunction ) {
            throw new Yasc_Exception( 'Requested function not found' );
        }

        if ( strtolower( $_SERVER['REQUEST_METHOD'] ) != $requestedFunction->getMethod() ) {
            throw new Yasc_Exception( 'Invalid request method' );
        }

        $view = new Yasc_View( $this->_bootstrap );

        $requestedFunction->invoke( $view, $requestedFunction->getParams() );
    }
}
