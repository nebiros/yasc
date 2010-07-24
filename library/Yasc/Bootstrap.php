<?php

/**
 * Bootstrap.
 *
 * @author jfalvarez
 */
class Yasc_Bootstrap {
    const CONFIGURATION_FUNCTION_NAME = 'configure';

    /**
     * Yasc configuration.
     * 
     * @var Yasc_Config
     */
    protected $_config = null;

    /**
     * Script mapped functions.
     *
     * @var array
     */
    protected $_functions = array();

    public function  __construct() {}

    public function getConfig() {
        return $this->_config;
    }

    public function setConfig( $config ) {
        $this->_config = $config;
    }

    public function setFunctions( Array $functions ) {
        $this->_functions = $_functions;
    }

    public function getFunctions() {
        return $this->_functions;
    }

    /**
     * Start yasc.
     *
     * @return void
     */
    public function run() {
        $this->_configure();
        $this->_processFunctions();
        $this->_processRoutes();
    }

    /**
     * Configure yasc.
     *
     * @return void
     */
    public function _configure() {
        if ( false === function_exists( self::CONFIGURATION_FUNCTION_NAME ) ) {
            return;
        }

        $configure = new ReflectionFunction( self::CONFIGURATION_FUNCTION_NAME );
        $this->_config = new Yasc_Config();
        $configure->invoke( $this->_config );
    }

    /**
     * Process application functions.
     *
     * @return void
     */
    protected function _processFunctions() {
        $functions = get_defined_functions();

        if ( true === empty( $functions['user'] ) ) {
            throw new Yasc_Exception( 'No user defined functions' );
        }

        foreach ( $functions['user'] as $name ) {
            $this->_functions[] = new Yasc_Function( $name );
        }
    }

    /**
     * Process routes.
     *
     * @return void
     */
    protected function _processRoutes() {
        $router = new Yasc_Router( $this );
        $router->route();
    }
}
