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
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 * App.
 *
 * @package Yasc
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_App {
    const CONFIGURATION_FUNCTION_NAME = "configure";
    const PRE_DISPATCH_FUNCTION_NAME = "pre_dispatch";
    const POST_DISPATCH_FUNCTION_NAME = "post_dispatch";
    
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
        if (null === self::$_instance) {
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
        self::$_instance->_autoloaderManager = Yasc_Autoloader_Manager::getInstance();
        self::$_instance->_helperManager = new Yasc_App_HelperManager();
    }

    protected function __construct() {}
    protected function __clone() {}
    
    /**
     *
     * @return string
     */
    public function getScriptName() {
        return (null !== self::$_instance->_function) ? 
            self::$_instance->_function->getFileName() : APPLICATION_PATH;
    }
    
    /**
     *
     * @return Yasc_Autoloader_Manager
     */
    public function getAutoloaderManager() {
        return self::$_instance->_autoloaderManager;
    }
    
    
    /**
     *
     * @return Yasc_App_HelperManager
     */
    public function getHelperManager() {
        return self::$_instance->_helperManager;
    }
    
    /**
     *
     * @return Yasc_App_Config
     */
    public function getConfig() {
        return self::$_instance->_config;
    }

    /**
     *
     * @return array
     */
    public function getFunctions() {
        return self::$_instance->_functions;
    }

    /**
     *
     * @return Yasc_Function
     */
    public function getFunction() {
        return self::$_instance->_function;
    }

    /**
     *
     * @return Yasc_App
     */
    protected function _setView() {
        if (null === self::$_instance->_view) {
            self::$_instance->_view = new Yasc_View();
        }

        return self::$_instance;
    }

    /**
     *
     * @return Yasc_View
     */
    public function getView() {
        return self::$_instance->_view;
    }

    /**
     *
     * @return Yasc_Layout
     */
    public function getLayout() {
        return self::$_instance->_layout;
    }

    /**
     * Start yasc.
     *
     * @return void
     */
    public function run() {
        self::$_instance->_configure();
        self::$_instance->_processFunctions();
        self::$_instance->_processRoutes();

        self::$_instance->_setView();

        self::$_instance->_preDispatch();
        self::$_instance->_dispatch();
        self::$_instance->_postDispatch();
    }

    /**
     * Configure yasc.
     *
     * @return bool
     */
    protected function _configure() {
        self::$_instance->_config = new Yasc_App_Config();
        
        if (false === function_exists(self::CONFIGURATION_FUNCTION_NAME)) {
            return false;
        }

        $configure = new ReflectionFunction(self::CONFIGURATION_FUNCTION_NAME);
        $configure->invoke();

        if (null !== self::$_instance->_config->getLayoutScript()) {
            self::$_instance->_layout = Yasc_Layout::getInstance()->setLayoutPath(self::$_instance->_config->getLayoutScript());
        }
    }

    /**
     * Call "pre_dispatch" function.
     * 
     * @return bool
     */
    protected function _preDispatch() {
        if (false === function_exists(self::PRE_DISPATCH_FUNCTION_NAME)) {
            return false;
        }

        $preDispatch = new ReflectionFunction(self::PRE_DISPATCH_FUNCTION_NAME);
        $preDispatch->invoke();
    }

    /**
     * Call "post_dispatch" function.
     * 
     * @return bool
     */
    protected function _postDispatch() {
        if (false === function_exists(self::POST_DISPATCH_FUNCTION_NAME)) {
            return false;
        }

        $postDispatch = new ReflectionFunction(self::POST_DISPATCH_FUNCTION_NAME);
        $postDispatch->invoke();
    }

    /**
     * Process application functions.
     *
     * @return void
     */
    protected function _processFunctions() {
        $functions = get_defined_functions();

        if (true === empty($functions["user"])) {
            throw new Yasc_Exception("No user defined functions");
        }

        foreach ($functions["user"] as $name) {
            $func = new Yasc_Function($name);
            
            if (false === $func->getAnnotation()->hasAnnotation()) {
                continue;
            }
            
            self::$_instance->_functions[] = $func;
        }
    }

    /**
     * Process routes.
     *
     * @return void
     */
    protected function _processRoutes() {
        $router = new Yasc_Router();
        self::$_instance->_function = $router->route();
    }

    /**
     * Dispatch.
     *
     * @return void
     */
    protected function _dispatch() {        
        self::$_instance->_execute();

        $buffer = self::$_instance->_view->getBuffer();

        if (null !== self::$_instance->_layout 
            && false === self::$_instance->_layout->isDisabled() 
           ) {
            self::$_instance->_layout->setContent($buffer);
            self::$_instance->_view->render(self::$_instance->_layout->getLayout());            
            $buffer = self::$_instance->_view->getBuffer();
        }

        if (null !== $buffer) {
            echo $buffer; return;
        }        
    }

    /**
     * Execute requested function.
     *
     * @return void
     */
    protected function _execute() {
        self::$_instance->_function->invoke();
    }
    
    /**
     *
     * @return Yasc_View
     */
    public static function view() {
        return self::$_instance->getView();
    }
    
    /**
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed 
     */
    public static function params($key = null, $default = null) {
        if (null === $key) {
            return self::$_instance->getFunction()->getParams();
        }
        
        return self::$_instance->getFunction()->getParam($key, $default);
    }
    
    /**
     *
     * @return Yasc_App_Config
     */
    public static function config() {
        return self::$_instance->getConfig();
    }
    
    /**
     *
     * @param string $name
     * @return Yasc_View_Helper_HelperAbstract 
     */
    public static function viewHelper($name) {
        return self::$_instance->getHelperManager()->getHelper($name, 
            Yasc_App_HelperManager::HELPER_TYPE_VIEW);
    }
    
    /**
     *
     * @param string $name
     * @return mixed 
     */
    public static function functionHelper($name) {
        return self::$_instance->getHelperManager()->getHelper($name, 
            Yasc_App_HelperManager::HELPER_TYPE_FUNCTION);
    }
}