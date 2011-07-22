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
 * @copyright Copyright (c) 2010 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 * App.
 *
 * @package Yasc
 * @copyright Copyright (c) 2010 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_App {
    const CONFIGURATION_FUNCTION_NAME = 'configure';

    /**
     * Yasc configuration.
     * 
     * @var Yasc_App_Config
     */
    protected $_config = null;

    /**
     * Script mapped functions.
     *
     * @var array
     */
    protected $_functions = array();

    /**
     *
     * @var Yasc_Function
     */
    protected $_function = null;

    /**
     *
     * @var Yasc_View
     */
    protected $_view = null;

    /**
     *
     * @var Yasc_Layout
     */
    protected $_layout = null;

    public function  __construct() {}

    /**
     *
     * @return Yasc_App_Config
     */
    public function getConfig() {
        return $this->_config;
    }

    /**
     *
     * @param Yasc_App_Config $config
     * @return Yasc_App
     */
    public function setConfig( Yasc_App_Config $config ) {
        $this->_config = $config;
        return $this;
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
     * @param array $functions
     * @return Yasc_App 
     */
    public function setFunctions( Array $functions ) {
        $this->_functions = $_functions;
        return $this;
    }

    /**
     *
     * @return Yasc_Function
     */
    public function getFunction() {
        return $this->_function;
    }

    /**
     *
     * @param Yasc_Function $function
     * @return Yasc_App
     */
    public function setFunction( Yasc_Function $function ) {
        $this->_function = $function;
        return $this;
    }

    /**
     *
     * @return Yasc_View
     */
    public function getView() {
        return $this->_view;
    }

    /**
     *
     * @param Yasc_View $view
     * @return Yasc_App
     */
    public function setView( Yasc_View $view ) {
        $this->_view = $view;
        return $this;
    }

    /**
     *
     * @return Yasc_Layout
     */
    public function getLayout() {
        return $this->_layout;
    }

    /**
     *
     * @param Yasc_Layout $layout
     * @return Yasc_App 
     */
    public function setLayout( Yasc_Layout $layout ) {
        $this->_layout = $layout;
        return $this;
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
        $this->_dispatch();
    }

    /**
     * Configure yasc.
     *
     * @return void
     */
    protected function _configure() {
        $this->_config = new Yasc_App_Config();
        
        if ( false === function_exists( self::CONFIGURATION_FUNCTION_NAME ) ) {
            return;
        }

        $configure = new ReflectionFunction( self::CONFIGURATION_FUNCTION_NAME );        
        $configure->invoke( $this->_config );

        if ( null !== $this->_config->getLayoutScript() ) {
            $this->_layout = Yasc_Layout::getInstance()->setLayoutPath( $this->_config->getLayoutScript() );
        }
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
            if ( $name == self::CONFIGURATION_FUNCTION_NAME ) {
                continue;
            }
            
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
        $this->_function = $router->route();
    }

    /**
     * Dispatch.
     *
     * @return void
     */
    protected function _dispatch() {
        if ( null === $this->_view ) {
            $this->_view = new Yasc_View( $this );
        }        
        
        $this->_execute();

        $buffer = $this->_view->getBuffer();

        if ( null !== $this->_layout && false === $this->_layout->isDisabled() ) {
            $this->_layout->setContent( $buffer );
            $this->_view->render( $this->_layout->getLayout() );            
            $buffer = $this->_view->getBuffer();
        }

        if ( null !== $buffer ) {
            echo $buffer;
        }        
    }

    /**
     * Execute requested function.
     *
     * @return void
     */
    protected function _execute() {
        $this->_function->invoke( $this->_view, $this->_function->getParams(), $this->_config );
    }
}
