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
 * View.
 *
 * @package Yasc
 * @copyright Copyright (c) 2010 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_View {
    /**
     * App class.
     *
     * @var Yasc_App
     */
    protected $_app = null;

    /**
     * View script path.
     *
     * @var string
     */
    protected $_viewScript = null;

    /**
     * View string.
     * 
     * @var string
     */
    protected $_buffer = null;

    /**
     *
     * @var array
     */
    protected $_helpers = array();

    /**
     *
     * @param Yasc_App $app 
     */
    public function __construct( Yasc_App $app = null ) {
        if ( null !== $app ) {
            $this->setApp( $app );
        }

		if ( false === in_array( 'view', stream_get_wrappers() ) ) {
            stream_wrapper_register( 'view', 'Yasc_View_Stream' );
		}
    }

    /**
     *
     * @param mixed $name
     * @param mixed $value
     */
    public function __set( $name, $value ) {
        if ( '_' != substr( $name, 0, 1 )  ) {
            $this->$name = $value;
            return;
        }

        throw new Yasc_View_Exception( 'Private or protected attributes are not allowed' );
    }

    /**
     *
     * @param mixed $name
     * @return bool
     */
    public function __isset( $name ) {
        if ( '_' != substr( $name, 0, 1 )  ) {
            return isset( $this->$name );
        }

        return false;
    }

    /**
     *
     * @param mixed $name
     * @return mixed|null
     */
    public function __get( $name ) {
        if ( true === isset( $this->$name ) ) {
            return $this->$name;
        }

        trigger_error( "Undefined property '{$name}'", E_USER_NOTICE );
        
        return null;
    }

    /**
     *
     * @param mixed $name
     * @return void
     */
    public function __unset( $name ) {
        if ( '_' != substr( $name, 0, 1 ) && true === isset( $this->$name ) ) {
            unset( $this->$name );
        }
    }

    /**
     *
     * @param string $name
     * @param array $arguments
     */
    public function __call( $name, $arguments ) {
        $helper = $this->getHelper( $name );
        return call_user_func_array( array( $helper, $name ), $arguments );
    }

    /**
     *
     * @param Yasc_App $app
     * @return Yasc_View
     */
    public function setApp( Yasc_App $app ) {
        $this->_app = $app;
        return $this;
    }

    /**
     *
     * @return Yasc_App
     */
    public function getApp() {
        return $this->_app;
    }

    /**
     *
     * @return string
     */
    public function getViewScript() {
        return $this->_viewScript;
    }

    /**
     *
     * @param string $filename
     * @return Yasc_View
     */
    public function setViewScript( $filename ) {
        $this->_viewScript = null;
        
        $folders = $this->getApp()->getConfig()->getViewsPaths();

        foreach ( $folders as $path ) {
            if ( true === is_file( realpath( $path . '/' . $filename . '.phtml' ) ) ) {
                $this->_viewScript = realpath( $path . '/' . $filename . '.phtml' );
                break;
            }
        }

        if ( null === $this->_viewScript ) {
            throw new Yasc_View_Exception( "View file '{$filename}' not found in this paths: " . implode( ', ', $folders ) );
        }

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getBuffer() {
        return $this->_buffer;
    }

    /**
     *
     * @param string $buffer
     * @return Yasc_View
     */
    public function setBuffer( $buffer ) {
        $this->_buffer = $buffer;
        return $this;
    }

    /**
     *
     * @param string $name
     * @return Yasc_View_Helper_AbstractHelper
     */
    public function getHelper( $name ) {
        $className = ucfirst( ( string ) $name );
        $paths = $this->getApp()->getConfig()->getViewHelpersPaths();

        foreach ( $paths as $classPrefix => $path ) {
            $class = $classPrefix . $className;
            if ( true === class_exists( $class ) && false === isset( $this->_helpers[$name] ) ) {
                $this->_helpers[$name] = new $class();
                if ( true === method_exists( $this->_helpers[$name], 'setView' ) ) {
                    $this->_helpers[$name]->setView( $this );
                }
                break;
            }
        }

        if ( null === $this->_helpers[$name] ) {
            throw new Yasc_View_Exception( "Helper '{$name}' not found" );
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
     * Process view script.
     *
     * @param string $filename
     * @return string
     */
    protected function _processViewScript( $filename ) {
        $this->setViewScript( $filename );
        unset( $filename );

        ob_start();
        include 'view://' . $this->_viewScript;
        $this->_buffer = ob_get_clean();

        return $this->_buffer;
    }

    /**
     * Render view.
     *
     * @param string $filename
     * @return void
     */
    public function render( $filename ) {
        $this->_processViewScript( $filename );
    }

    public function __toString() {
        return $this->_buffer;
    }
}
