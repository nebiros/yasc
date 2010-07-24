<?php

/**
 * Route.
 *
 * @author jfalvarez
 */
class Yasc_Router_Route {
    /**
     * Default scheme.
     *
     * @var string
     */
    protected $_scheme = 'http';

    /**
     * Default port.
     *
     * @var int
     */
    protected $_port = 80;

    protected $_urlVariable = ':';
    protected $_urlDelimiter = '/';

    /**
     * Requested url, normalized.
     *
     * @var string
     */
    protected $_url = null;

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

    public function __construct( Array $functions = null ) {
        if ( null !== $functions ) {
            $this->_functions = $functions;
        }
    }

    public function getUrl() {
        return $this->_url;
    }

    public function getUrlPattern() {
        return $this->_urlPattern;
    }

    public function getUrlComponents() {
        return $this->_urlComponents;
    }

    public function getFunctions() {
        return $this->_functions;
    }

    public function getRequestedFunction() {
        return $this->_requestedFunction;
    }

    /**
     * Process url requested.
     *
     * @return Yasc_Router_Route
     */
    protected function _processUrl() {
        $url = $this->_scheme;

        if ( $_SERVER['HTTPS'] == 'on' ) {
            $url .= 's';
        }

        $url .= '://';

        if ( $_SERVER['SERVER_PORT'] != $this->_port ) {
            $url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
        } else {
            $url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        }

        $this->_url = trim( $url, $this->_urlDelimiter );
        $this->_urlComponents = parse_url( $this->_url );

        $urlPattern = str_replace( $_SERVER['SCRIPT_NAME'], '', $this->_urlComponents['path'] );
        $this->_urlPattern = ( $urlPattern ) ? $urlPattern : '/';

        return $this;
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
            throw new Yasc_Exception( 'No user defined functions' );
        }

        $this->_processUrl();

        $urlPath = explode( $this->_urlDelimiter, $this->_urlPattern );

        foreach ( $this->_functions AS $function ) {
            if ( false === ( $function instanceof Yasc_Function ) ) {
                throw new Yasc_Exception( 'Function is not a instance of Yasc_Function' );
            }

            $annotationPath = explode( $this->_urlDelimiter, $function->getAnnotation()->getPattern() );

            // If it's not the same path size, jump next function.
            if ( count( $urlPath ) != count( $annotationPath ) ) {
                continue;
            }

            $partsMatch = true;

            foreach ( $urlPath as $pos => $part ) {
                $var = $annotationPath[$pos];

                // Assign url part value to match the entire annotation path.
                if ( substr( $annotationPath[$pos], 0, 1 ) === $this->_urlVariable ) {
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

            // So everything seems to be fine, execute fuction first occurrence.
            $this->_requestedFunction = $function;

            break;
        }

        return $this;
    }
}
