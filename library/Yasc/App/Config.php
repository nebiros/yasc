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
 * @copyright Copyright (c) 2010 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 * Configuration.
 *
 * @package Yasc
 * @subpackage Yasc_App
 * @copyright Copyright (c) 2010 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_App_Config {
    const TYPE_PATH_HELPER = 1;
    const TYPE_PATH_MODEL = 2;
    
    /**
     *
     * @var array
     */
    protected $_options = array();
    
    /**
     * Views folders.
     *
     * @var array
     */
    protected $_viewsPaths = array();

    /**
     * Layout.
     * 
     * @var string
     */
    protected $_layoutScript = null;

    /**
     *
     * @var array
     */
    protected $_viewHelperPaths = array();
    
    /**
     *
     * @var bool
     */
    protected $_useViewStream = false;
    
    /**
     *
     * @var array
     */
    protected $_modelPaths = array();

    public function __construct() {
        $this->addDefaultPaths();
    }
    
    /**
     *
     * @param array $options
     * @return Yasc_App_Config 
     */
    public function setOptions( Array $options ) {
        $this->_options = $options;
        return $this;
    }
    
    /**
     *
     * @return array
     */
    public function getOptions() {
        return $this->_options;
    }
    
    /**
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed|null 
     */
    public function getOption( $key, $default = null ) {
        if ( true === isset( $this->_options[$key] ) ) {
            return $this->_options[$key];
        }
        
        return $default;        
    }
    
    /**
     *
     * @param array $options
     * @return App_View_Helper_Payments 
     */
    public function addOptions( Array $options ) {
        $this->_options = array_merge( $this->_options, $options );
        return $this;
    }
    
    /**
     *
     * @param mixed $key
     * @param mixed $value
     * @return App_View_Helper_Payments 
     */
    public function addOption( $key, $value = null ) {
        $this->_options[$key] = $value;
        return $this;
    }    

    /**
     *
     * @return array
     */
    public function getViewsPaths() {
        return $this->_viewsPaths;
    }

    /**
     *
     * @param string $path
     * @return Yasc_App_Config
     */
    public function setViewsPath( $path ) {
        $this->_viewsPaths = array();
        $this->addViewHelpersPath( $path );
        return $this;
    }

    /**
     *
     * @param string $path
     * @return Yasc_App_Config
     */
    public function addViewsPath( $path ) {
        if ( false === is_string( $path ) ) {
            return false;
        }
        
        $path = realpath( $path );
        
        if ( false === is_dir( $path ) ) {
            throw new Yasc_App_Exception( "Views folder '{$path}' not found" );
        }

        $this->_viewsPaths[] = $path;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getLayoutScript() {
        return $this->_layoutScript;
    }

    /**
     *
     * @param string $layout
     * @return Yasc_App_Config
     */
    public function setLayoutScript( $layout ) {
        if ( false === is_string( $layout ) ) {
            return false;
        }
        
        $layout = realpath( $layout );
        
        if ( false === is_file( $layout ) ) {
            throw new Yasc_App_Exception( "Layout file '{$layout}' not found" );
        }

        $this->_layoutScript = $layout;
        // Cause a layout is a view too, we going to add layout script folder
        // to the views folders.
        $this->addViewsPath( dirname( $this->_layoutScript ) );

        return $this;
    }

    /**
     *
     * @return array
     */
    public function getViewHelperPaths() {
        return $this->_viewHelperPaths;
    }

    /**
     *
     * @param string $classPrefix
     * @return string
     */
    public function getViewHelpersPath( $classPrefix = 'Yasc_View_Helper_' ) {
        return $this->_getClassPath( self::TYPE_PATH_HELPER, $classPrefix );
    }

    /**
     *
     * @param string $path
     * @param string $classPrefix
     * @return Yasc_App_Config
     */
    public function setViewHelpersPath( $path, $classPrefix = null ) {
        $this->resetViewHelperPaths()->addViewHelpersPath( $path, $classPrefix );
        return $this;
    }

    /**
     *
     * @param string $path
     * @param string $classPrefix
     * @return Yasc_App_Config
     */
    public function addViewHelpersPath( $path, $classPrefix = null ) {
        $this->_addClassPath( self::TYPE_PATH_HELPER, $path, $classPrefix );
        return $this;
    }

    /**
     *
     * @return Yasc_App_Config
     */
    public function resetViewHelperPaths() {
        $includePaths = explode( PATH_SEPARATOR, get_include_path() );
        $paths = array_diff( $includePaths, $this->_viewHelperPaths );
        $this->_viewHelperPaths = array();
        // Set default view helpers.
        $this->addViewHelpersPath( realpath( dirname( __FILE__ ) . '/../View/Helper' ), 'Yasc_View_Helper' );
        set_include_path( implode( PATH_SEPARATOR, $paths ) );
        return $this;
    }
    
    /**
     *
     * @param bool $flag
     * @return Yasc_App_Config 
     */
    public function setViewStream( $flag = false ) {
        $this->_useViewStream = $flag;
        return $this;
    }
    
    /**
     *
     * @return bool
     */
    public function useViewStream() {
        return $this->_useViewStream;
    }
    
    /**
     *
     * @return array
     */
    public function getModelPaths() {
        return $this->_modelPaths;
    }

    public function setModelPaths( $path ) {
        $this->_modelPaths = ( array ) $path;
        return $this;
    }
        
    /**
     *
     * @param string $path
     * @param string $classPrefix
     * @return Yasc_App_Config
     */    
    public function addModelPath( $path, $classPrefix = null ) {
        $this->_addClassPath( self::TYPE_PATH_MODEL, $path, $classPrefix );
        return $this;
    }
    
    /**
     *
     * @param int $type
     * @param string $path
     * @param string $classPrefix 
     */
    protected function _addClassPath( $type, $path, $classPrefix = null ) {
        if ( false === is_string( $path ) ) {
            return false;
        }
        
        $path = realpath( $path );

        if ( null !== $classPrefix ) {
            $prefixFolder = str_replace( '_', DIRECTORY_SEPARATOR, $classPrefix );
            $path = realpath( str_replace( $prefixFolder, '', $path ) );
        }
        
        if ( false === is_dir( $path ) ) {
            throw new Yasc_App_Exception( "Class path folder '{$path}' not found" );
        }

        if ( '_' != substr( $classPrefix, -1 ) && false === empty( $classPrefix ) ) {
            $classPrefix .= '_';
        }        
        
        switch ( ( int ) $type ) {
            case self::TYPE_PATH_HELPER:
                $this->_viewHelperPaths[$classPrefix] = $path;
                break;
            
            case self::TYPE_PATH_MODEL:
                $this->_modelPaths[$classPrefix] = $path;
                break;
        }

        if ( false === array_search( $path, explode( PATH_SEPARATOR, get_include_path() ) ) ) {
            set_include_path( implode( PATH_SEPARATOR, array(
                $path,
                get_include_path()
            ) ) );
        }        
    }
    
    /**
     *
     * @param int $type
     * @param string $classPrefix
     * @return string 
     */
    public function _getClassPath( $type, $classPrefix = null ) {
        if ( '_' != substr( $classPrefix, -1 ) && false === empty( $classPrefix ) ) {
            $classPrefix .= '_';
        }
        
        switch ( ( int ) $type ) {
            case self::TYPE_PATH_HELPER:
                return $this->_viewHelperPaths[$classPrefix];
                break;
            
            case self::TYPE_PATH_MODEL:
                return $this->_modelPaths[$classPrefix];
                break;
        }
    }
    
    /**
     * Add default folders if they exist. This is the current structure:
     * 
     * app.php
     * views/
     *   helpers/
     * models/
     * 
     */
    public function addDefaultPaths() {
        // default views folder.
        $this->addViewsPath( realpath( APPLICATION_PATH . '/views' ) );
        // default view helpers path.
        $this->addViewHelpersPath( realpath( APPLICATION_PATH . '/views/helpers' ) );
        // default models folder.
        $this->addModelPath( realpath( APPLICATION_PATH . '/models' ) );        
        // built in helpers.
        $this->addViewHelpersPath( realpath( dirname( __FILE__ ) . '/../View/Helper' ), 'Yasc_View_Helper' );
    }
}
