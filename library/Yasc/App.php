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
 * @copyright Copyright (c) 2010 - 2011 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 * App.
 *
 * @package Yasc
 * @copyright Copyright (c) 2010 - 2011 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_App {
    const CONFIGURATION_FUNCTION_NAME = 'configure';
    
    /**
     *
     * @var Yasc_App
     */
    protected static $_instance = null;    
    
    /**
     * 
     * @var Yasc_Autoloader_Manager
     */
    protected $_autoloaderManager = null;
    
    /**
     *
     * @var Yasc_App_HelperManager 
     */
    protected $_helperManager = null;

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
    
    /**
     *
     * @return Yasc_App
     */
    public static function getInstance() {
        if ( null === self::$_instance ) {
            self::$_instance = new self();
            self::$_instance->_initialize();
        }

        return self::$_instance;
    }
    
    /**
     * 
     * @return void
     */
    protected function _initialize() {
        session_start();
        $this->_autoloaderManager = Yasc_Autoloader_Manager::getInstance();
        $this->_helperManager = new Yasc_App_HelperManager( $this->_autoloaderManager );
    }

    protected function __construct() {}
    protected function __clone() {}
    
    /**
     *
     * @return Yasc_Autoloader_Manager
     */
    public function getAutoloaderManager() {
        return $this->_autoloaderManager;
    }
    
    
    /**
     *
     * @return Yasc_App_HelperManager
     */
    public function getHelperManager() {
        return $this->_helperManager;
    }
    
    /**
     *
     * @return Yasc_App_Config
     */
    public function getConfig() {
        return $this->_config;
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
    public function getFunction() {
        return $this->_function;
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
     * @return Yasc_Layout
     */
    public function getLayout() {
        return $this->_layout;
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
        $this->_config = new Yasc_App_Config( $this->_autoloaderManager );
        
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
        $router = new Yasc_Router();
        $this->_function = $router->route();
    }

    /**
     * Dispatch.
     *
     * @return void
     */
    protected function _dispatch() {
        if ( null === $this->_view ) {
            $this->_view = new Yasc_View();
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
            exit();
        }        
    }

    /**
     * Execute requested function.
     *
     * @return void
     */
    protected function _execute() {
        $this->_function->invoke();
    }
    
    /**
     *
     * @return Yasc_View
     */
    public static function view() {
        return self::getInstance()->getView();
    }
    
    /**
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed 
     */
    public static function params( $key = null, $default = null ) {
        if ( null === $key ) {
            return self::getInstance()->getFunction()->getParams();
        }
        
        return self::getInstance()->getFunction()->getParam( $key, $default );
    }
    
    /**
     *
     * @return Yasc_App_Config
     */
    public static function config() {
        return self::getInstance()->getConfig();
    }
    
    /**
     *
     * @param string $name
     * @return Yasc_View_Helper_HelperAbstract 
     */
    public static function viewHelper( $name ) {
        return self::getInstance()->getHelperManager()->getHelper( $name, Yasc_App_HelperManager::HELPER_TYPE_VIEW );
    }
    
    /**
     *
     * @param string $name
     * @return mixed 
     */
    public static function functionHelper( $name ) {
        return self::getInstance()->getHelperManager()->getHelper( $name, Yasc_App_HelperManager::HELPER_TYPE_FUNCTION );
    }
}
