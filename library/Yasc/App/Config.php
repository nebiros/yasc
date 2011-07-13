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
    protected $_viewHelpersPaths = array();
    
    /**
     *
     * @var bool
     */
    protected $_useViewStream = false;

    public function __construct() {
        // Built in helpers.
        $this->addViewHelpersPath( realpath( dirname( __FILE__ ) . '/../View/Helper' ), 'Yasc_View_Helper' );
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
    public function getViewHelpersPaths() {
        return $this->_viewHelpersPaths;
    }

    /**
     *
     * @param string $classPrefix
     * @return string
     */
    public function getViewHelpersPath( $classPrefix = 'Yasc_View_Helper_' ) {
        if ( '_' != substr( $classPrefix, -1 ) && false === empty( $classPrefix ) ) {
            $classPrefix .= '_';
        }

        return $this->_viewHelpersPaths[$classPrefix];
    }

    /**
     *
     * @param string $path
     * @param string $classPrefix
     * @return Yasc_App_Config
     */
    public function setViewHelpersPath( $path, $classPrefix = null ) {
        $this->resetViewHelpersPaths()->addViewHelpersPath( $path, $classPrefix );
        return $this;
    }

    /**
     *
     * @param string $path
     * @param string $classPrefix
     * @return Yasc_App_Config
     */
    public function addViewHelpersPath( $path, $classPrefix = null ) {
        $path = realpath( $path );

        if ( null !== $classPrefix ) {
            $prefixFolder = str_replace( '_', DIRECTORY_SEPARATOR, $classPrefix );
            $path = realpath( str_replace( $prefixFolder, '', $path ) );
        }

        if ( false === is_dir( $path ) ) {
            throw new Yasc_App_Exception( "View helpers folder '{$path}' not found" );
        }

        if ( '_' != substr( $classPrefix, -1 ) && false === empty( $classPrefix ) ) {
            $classPrefix .= '_';
        }

        $this->_viewHelpersPaths[$classPrefix] = $path;

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
     * @return Yasc_App_Config
     */
    public function resetViewHelpersPaths() {
        $includePaths = explode( PATH_SEPARATOR, get_include_path() );
        $paths = array_diff( $includePaths, $this->_viewHelpersPaths );
        $this->_viewHelpersPaths = array();
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
}
