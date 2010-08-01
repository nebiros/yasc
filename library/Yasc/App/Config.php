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
 * @copyright Copyright (c) 2010 Juan Felipe Alvarez Sadarriaga. (http://www.jfalvarez.com)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 * Configuration.
 *
 * @package Yasc
 * @subpackage Yasc_App
 * @copyright Copyright (c) 2010 Juan Felipe Alvarez Sadarriaga. (http://www.jfalvarez.com)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author jfalvarez
 */
class Yasc_App_Config {
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

    public function __construct() {
        // Built in helpers.
        $this->addViewHelpersPath( realpath( dirname( __FILE__ ) . '/../View/Helper' ), 'Yasc_View_Helper' );
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
}
