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
 * @subpackage Yasc_App
 * @copyright Copyright (c) 2010 - 2011 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 *
 * @package Yasc
 * @subpackage Yasc_App
 * @copyright Copyright (c) 2010 - 2011 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_App_HelperManager {
    const HELPER_TYPE_VIEW = 1;
    const HELPER_TYPE_FUNCTION = 2;
    
    /**
     *
     * @var array
     */
    protected $_helpers = array();
    
    /**
     *
     */
    public function __construct() {
        $this->_addBuiltInHelpers();
    }
    
    /**
     *
     * @return array
     */
    public function getViewHelperPaths() {
        return Yasc_Autoloader_Manager::getInstance()->getPaths( 
            Yasc_Autoloader_Manager::PATH_TYPE_VIEW_HELPER );
    }
    
    /**
     *
     * @param string $classPrefix
     * @return string 
     */
    public function getViewHelpersPath( $classPrefix = null ) {
        return Yasc_Autoloader_Manager::getInstance()->getPath( 
            Yasc_Autoloader_Manager::PATH_TYPE_VIEW_HELPER, $classPrefix );
    }
    
    /**
     *
     * @param string $path
     * @param string $classPrefix
     * @return Yasc_App_HelperManager 
     */
    public function setViewHelpersPath( $path, $classPrefix = null ) {
        Yasc_Autoloader_Manager::getInstance()->setPath( 
            Yasc_Autoloader_Manager::PATH_TYPE_VIEW_HELPER, $path, $classPrefix );
        return $this;
    }    
    
    /**
     *
     * @param string $path
     * @param string $classPrefix
     * @return Yasc_App_HelperManager 
     */
    public function addViewHelpersPath( $path, $classPrefix = null ) {
        Yasc_Autoloader_Manager::getInstance()->addPath( 
            Yasc_Autoloader_Manager::PATH_TYPE_VIEW_HELPER, $path, $classPrefix );
        return $this;
    }
    
    /**
     *
     * @return array
     */
    public function getFunctionHelperPaths() {
        return Yasc_Autoloader_Manager::getInstance()->getPaths( 
            Yasc_Autoloader_Manager::PATH_TYPE_FUNCTION_HELPER );
    }
    
    /**
     *
     * @param string $classPrefix
     * @return string 
     */
    public function getFunctionHelpersPath( $classPrefix = null ) {
        return Yasc_Autoloader_Manager::getInstance()->getPath( 
            Yasc_Autoloader_Manager::PATH_TYPE_FUNCTION_HELPER, $classPrefix );
    }
    
    /**
     *
     * @param string $path
     * @param string $classPrefix
     * @return Yasc_App_HelperManager 
     */    
    public function setFunctionHelpersPath( $path, $classPrefix = null ) {
        Yasc_Autoloader_Manager::getInstance()->setPath( 
            Yasc_Autoloader_Manager::PATH_TYPE_FUNCTION_HELPER, $path, $classPrefix );
        return $this;
    }    
    
    /**
     *
     * @param string $path
     * @param string $classPrefix
     * @return Yasc_App_HelperManager 
     */
    public function addFunctionHelpersPath( $path, $classPrefix = null ) {
        Yasc_Autoloader_Manager::getInstance()->addPath( 
            Yasc_Autoloader_Manager::PATH_TYPE_FUNCTION_HELPER, $path, $classPrefix );
        return $this;
    }  

    /**
     *
     * @return Yasc_App_HelperManager
     */
    public function resetHelpersPaths() {
        Yasc_Autoloader_Manager::getInstance()->clearIncludePaths( 
            Yasc_Autoloader_Manager::PATH_TYPE_VIEW_HELPER )
            ->clearIncludePaths( 
            Yasc_Autoloader_Manager::PATH_TYPE_FUNCTION_HELPER );
        return $this;
    }    
    
    /**
     *
     * @param string $name
     * @param int $type
     * @return mixed 
     */
    public function getHelper( $name, $type = null ) {
        switch ( ( int ) $type ) {
            case self::HELPER_TYPE_FUNCTION:
                $paths = $this->getFunctionHelperPaths();
                break;
             
            case self::HELPER_TYPE_VIEW:
            default:
                $paths = $this->getViewHelperPaths();
                break;
        }
        Zend_Debug::dump(__METHOD__);        
        Zend_Debug::dump($paths);
        $className = trim( ucfirst( ( string ) $name ) );
        Zend_Debug::dump($className);
        foreach ( $paths as $classPrefix => $path ) {
            $class = $classPrefix . $className;
            
            if ( true === class_exists( $class ) && false === isset( $this->_helpers[$name] ) ) {
                if ( true === is_callable( array( $class, 'getInstance' ) ) ) {
                    $this->_helpers[$name] = call_user_func( array( $class, 'getInstance' ) );
                } else {
                    $this->_helpers[$name] = new $class();
                }

                if ( true === is_callable( array( $this->_helpers[$name], 'setView' ) ) ) {
                    $this->_helpers[$name]->setView( Yasc_App::getInstance()->getView() );
                }
                
                break;
            }
        }

        if ( null === $this->_helpers[$name] ) {
            throw new Yasc_App_Exception( "Helper '{$name}' not found in this paths: " . implode( ", ", $paths ) );
        }

        return $this->_helpers[$name];
    }

    /**
     *
     * @return array
     */
    public function getHelpers() {
        return $this->_helpers;
    }
    
    /**
     * 
     * @return void
     */
    protected function _addBuiltInHelpers() {
        // built in helpers.
        Yasc_Autoloader_Manager::getInstance()->addPath( 
            Yasc_Autoloader_Manager::PATH_TYPE_FUNCTION_HELPER, 
            realpath( dirname( __FILE__ ) . '/../Function/Helper' ), 
            'Yasc_Function_Helper' 
        );
        Yasc_Autoloader_Manager::getInstance()->addPath( 
            Yasc_Autoloader_Manager::PATH_TYPE_VIEW_HELPER, 
            realpath( dirname( __FILE__ ) . '/../View/Helper' ), 
            'Yasc_View_Helper' 
        );        
    }
}
