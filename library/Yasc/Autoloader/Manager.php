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
 * @subpackage Yasc_Autoloader
 * @copyright Copyright (c) 2010 - 2011 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 *
 * @package Yasc
 * @subpackage Yasc_Autoloader
 * @copyright Copyright (c) 2010 - 2011 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_Autoloader_Manager {
    const PATH_TYPE_VIEW = 1;
    const PATH_TYPE_VIEW_HELPER = 2;
    const PATH_TYPE_FUNCTION_HELPER = 3;
    const PATH_TYPE_MODEL = 4;
    const PATH_TYPE_NS = 5;
    
    /**
     *
     * @var Yasc_Autoloader_Manager
     */
    protected static $_instance = null;
    
    /**
     *
     * @var array
     */
    protected $_paths = array(
        self::PATH_TYPE_VIEW => array(),
        self::PATH_TYPE_VIEW_HELPER => array(),
        self::PATH_TYPE_FUNCTION_HELPER => array(),
        self::PATH_TYPE_MODEL => array(),        
        self::PATH_TYPE_NS => array()
    );
    
    /**
     *
     * @return Yasc_Autoloader_Manager
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
     */
    protected function _initialize() {
        $this->_addDefaultPaths();
    }
    
    protected function __construct() {}
    
    /**
     *
     * @param int $type
     * @param string $path
     * @param string $classPrefix
     * @return Yasc_Autoloader_Manager 
     */
    public function setClassPath( $type, $path, $classPrefix = null ) {
        if ( false === is_numeric( $type ) ) {
            throw new Yasc_App_Exception( 'Path type must be a number' );
        }
        
        if ( false === ( $path = $this->_setupPath( $path, $classPrefix ) ) ) {
            return false;
        }
        
        $this->clearClassPaths( $type )->addClassPath( $type, $path, $classPrefix );
        return $this;
    }

    /**
     *
     * @param int $type
     * @param string $path
     * @param string|null $classPrefix
     * @return Yasc_Autoloader_Manager 
     */
    public function addClassPath( $type, $path, $classPrefix = null ) {
        if ( false === is_numeric( $type ) ) {
            throw new Yasc_App_Exception( 'Path type must be a number' );
        }
        
        $classPrefix = $this->_setupClassPrefix( $classPrefix );
        
        if ( false === ( $path = $this->_setupPath( $path, $classPrefix ) ) ) {
            return false;
        }

        $this->_paths[$type][$classPrefix] = $path;

        if ( false === array_search( $path, explode( PATH_SEPARATOR, get_include_path() ) ) ) {
            set_include_path( implode( PATH_SEPARATOR, array(
                $path,
                get_include_path()
            ) ) );
        }
        
        return $this;
    }   
    
    /**
     *
     * @param int $type
     * @param string|null $classPrefix
     * @return Yasc_Autoloader_Manager 
     */
    public function clearClassPath( $type, $classPrefix = null ) {
        if ( false === is_numeric( $type ) ) {
            throw new Yasc_App_Exception( 'Path type must be a number' );
        }
        
        $classPrefix = $this->_setupClassPrefix( $classPrefix );        
        $includePaths = array_flip( explode( PATH_SEPARATOR, get_include_path() ) );        
        unset( $includePaths[$this->_paths[$type][$classPrefix]], $this->_paths[$type][$classPrefix] );
        set_include_path( implode( PATH_SEPARATOR, array_keys( $includePaths ) ) );
        
        switch ( ( int ) $type ) {
            case self::PATH_TYPE_FUNCTION_HELPER:
                $this->addPath( 
                    self::PATH_TYPE_FUNCTION_HELPER, 
                    realpath( dirname( __FILE__ ) . '/../Function/Helper' ), 
                    'Yasc_Function_Helper' 
                );
                
                break;
            
            case self::PATH_TYPE_VIEW_HELPER:
            default:
                $this->addPath( 
                    self::PATH_TYPE_VIEW_HELPER, 
                    realpath( dirname( __FILE__ ) . '/../View/Helper' ), 
                    'Yasc_View_Helper' 
                );
                
                break;
        }
        
        return $this;
    }
    
    /**
     *
     * @param type $type
     * @return Yasc_Autoloader_Manager 
     */
    public function clearClassPaths( $type ) {
        if ( false === is_numeric( $type ) ) {
            throw new Yasc_App_Exception( 'Path type must be a number' );
        }
        
        $includePaths = explode( PATH_SEPARATOR, get_include_path() );
        $paths = array_diff( $includePaths, $this->_paths[$type] );
        $this->_paths[$type] = array();
        set_include_path( implode( PATH_SEPARATOR, $paths ) );              
        return $this;
    }
    
    /**
     *
     * @param int $type
     * @param string|null $classPrefix
     * @return string 
     */
    public function getPath( $type, $classPrefix = null ) {
        if ( false === is_numeric( $type ) ) {
            throw new Yasc_App_Exception( 'Path type must be a number' );
        }
        
        $classPrefix = $this->_setupClassPrefix( $classPrefix );        
        return $this->_paths[$type][$classPrefix];
    }   
    
    /**
     *
     * @param string $type
     * @param string $path
     * @param string|null $classPrefix
     * @return Yasc_Autoloader_Manager 
     */
    public function setPath( $type, $path, $classPrefix = null ) {
        if ( false === is_numeric( $type ) ) {
            throw new Yasc_App_Exception( 'Path type must be a number' );
        }
        
        $this->clearClassPaths( $type );
        $this->addPath( $type, $path, $classPrefix );
        return $this;
    }
    
    /**
     *
     * @param string $type
     * @param string $path
     * @param string|null $classPrefix
     * @return Yasc_Autoloader_Manager 
     */
    public function addPath( $type, $path, $classPrefix = null ) {
        if ( false === is_numeric( $type ) ) {
            throw new Yasc_App_Exception( 'Path type must be a number' );
        }
        
        if ( false === ( $path = $this->_setupPath( $path ) ) ) {
            return false;
        }
        
        if ( null !== $classPrefix ) {
            $classPrefix = $this->_setupClassPrefix( $classPrefix );
            $this->_paths[$type][$classPrefix] = $path;
        } else {
            $this->_paths[$type][] = $path;
        }
        
        return $this;        
    }
    
    /**
     *
     * @param int $type
     * @return array
     */
    public function getPaths( $type ) {
        if ( false === is_numeric( $type ) ) {
            throw new Yasc_App_Exception( 'Path type must be a number' );
        }
        
        return $this->_paths[$type];
    }
    
    /**
     *
     * @return array
     */
    public function getAllPaths() {
        return $this->_paths;
    }
    
    /**
     *
     * @param string $classPrefix
     * @return string 
     */
    protected function _setupClassPrefix( $classPrefix = null ) {
        if ( '_' != substr( $classPrefix, -1 ) && false === empty( $classPrefix ) ) {
            $classPrefix .= '_';
        }
        
        return $classPrefix;
    }
    
    /**
     *
     * @param string $path
     * @return string|bool 
     */
    protected function _setupPath( $path, $classPrefix = null ) {
        if ( false === is_string( $path ) ) {
            return false;
        }
        
        $tmp = $path;
        $path = realpath( $tmp );

        if ( null !== $classPrefix ) {
            $prefixFolder = str_replace( '_', DIRECTORY_SEPARATOR, $classPrefix );
            $tmp = str_replace( $prefixFolder, '', $path );
            $path = realpath( $tmp );
        }
        
        if ( false === is_dir( $path ) ) {
            throw new Yasc_App_Exception( "Path '{$tmp}' not found" );
        }
        
        return $path;
    }
    
    /**
     * 
     */
    protected function _addDefaultPaths() {
        // default paths, if they exist.
        $this->addPath( self::PATH_TYPE_VIEW, realpath( APPLICATION_PATH . '/views' ) );
        $this->addPath( self::PATH_TYPE_VIEW_HELPER, realpath( APPLICATION_PATH . '/views/helpers' ), 'Helper' );
        $this->addPath( self::PATH_TYPE_MODEL, realpath( APPLICATION_PATH . '/models' ), 'Model' );
        
        // built in helpers.
        $this->addPath( 
            self::PATH_TYPE_FUNCTION_HELPER, 
            realpath( dirname( __FILE__ ) . '/../Function/Helper' ), 
            'Yasc_Function_Helper' 
        );
        $this->addPath( 
            self::PATH_TYPE_VIEW_HELPER, 
            realpath( dirname( __FILE__ ) . '/../View/Helper' ), 
            'Yasc_View_Helper' 
        );
    }
    
    /**
     *
     * @param mixed $class
     * @return string 
     */
    public function getNs( $class ) {
        return $this->_normalizePrefix( $class, true );
    }
    
    /**
     *
     * @param mixed $class
     * @return string 
     */
    public function getPrefix( $class ) {
        return $this->_normalizePrefix( $class );
    }
    
    /**
     *
     * @param mixed $class
     * @param bool $ns
     * @return string 
     */
    protected function _normalizePrefix( $class, $ns = false ) {
        if ( false === is_string( $class ) ) {
            $class = get_class( $class );
        }
        
        $parts = explode( '.', $class );

        if ( false === empty( $parts ) ) {
            array_pop( $parts );
            $class = $parts[0];
        }

        if ( strpos( $class, DIRECTORY_SEPARATOR ) ) {
            $class = str_replace( DIRECTORY_SEPARATOR, '_', $class );
        }
        
        $tmp = array();
        if ( strpos( $class, '_' ) ) {
            $tmp = explode( '_', $class );
        }        
        
        if ( true === empty( $tmp ) ) {
            return  '';
        }
        
        if ( true === $ns ) {
            $prefix = array_shift( $tmp );
        } else {
            array_pop( $tmp );
            $prefix = implode( '_', $tmp );
        }
        
        if ( '_' != substr( $prefix, -1 ) && false === empty( $prefix ) ) {
            $prefix .= '_';
        }
        
        return $prefix;        
    }
}
