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
 * @package Yasc_Function
 * @subpackage Yasc_Function_Helper
 * @copyright Copyright (c) 2010 - 2011 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 * Flash messages function helper.
 *
 * @package Yasc_Function
 * @subpackage Yasc_Function_Helper
 * @copyright Copyright (c) 2010 - 2011 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_Function_Helper_Flash {
    const TYPE_NOTICE = "notice";
    const TYPE_MESSAGE = "message";
    const TYPE_WARNING = "warning";
    const TYPE_ERROR = "error";
    
    /**
     *
     * @var Yasc_Function_Helper_Flash
     */
    protected static $_instance = null;

    /**
     *
     * @var array
     */
    protected $_session = null;

    /**
     *
     * @var string
     */
    protected $_xhtml = null;

    protected function __construct() {}
    protected function __clone() {}

    /**
     *
     * @return Yasc_Function_Helper_Flash
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
        
        self::$_instance->_session = ( array ) $_SESSION['Yasc_Function_Helper_Flash'];
        self::$_instance->_session[self::TYPE_NOTICE] = array();
        self::$_instance->_session[self::TYPE_MESSAGE] = array();
        self::$_instance->_session[self::TYPE_WARNING] = array();
        self::$_instance->_session[self::TYPE_ERROR] = array();
    }

    /**
     *
     * @param string|array $msg
     * @param string $type
     * @return Yasc_Function_Helper_Flash
     */
    public function add( $msg, $type = self::TYPE_MESSAGE ) {
        if ( true === is_array( $msg ) ) {
            self::$_instance->_session[$type] = array_merge( self::$_instance->_session[$type], $msg );
        } else {
            self::$_instance->_session[$type][] = $msg;
        }
        
        return self::getInstance();
    }
    
    /**
     *
     * @param string|array $msg
     * @param string $type
     * @return Yasc_Function_Helper_Flash 
     */
    public function set( $msg, $type = self::TYPE_MESSAGE ) {
        self::$_instance->_session[$type] = ( array ) $msg;
        return self::getInstance();
    }
    
    /**
     *
     * @param string $type
     * @return Yasc_Function_Helper_Flash
     */
    public function get( $type = self::TYPE_MESSAGE ) {
        return self::$_instance->_session[$type];
    }
    
    /**
     *
     * @param string $type
     * @return Yasc_Function_Helper_Flash
     */
    public function clear( $type = self::TYPE_MESSAGE ) {
        self::$_instance->_session[$type] = array();
        return self::getInstance();
    }
    
    /**
     *
     * @param string $type
     * @return Yasc_Function_Helper_Flash
     */    
    public function has( $type = self::TYPE_MESSAGE ) {
        return ( false === empty( self::$_instance->_session[$type] ) ) ? true : false;
    }
    
    /**
     *
     * @param string $msg
     * @return Yasc_Function_Helper_Flash 
     */
    public function notice( $msg ) {
        self::$_instance->add( $msg, self::TYPE_NOTICE );
        return self::getInstance();
    }
    
    /**
     *
     * @param string $msg
     * @return Yasc_Function_Helper_Flash 
     */    
    public function message( $msg ) {
        self::$_instance->add( $msg );
        return self::getInstance();
    }
    
    /**
     *
     * @param string $msg
     * @return Yasc_Function_Helper_Flash 
     */    
    public function warning( $msg ) {
        self::$_instance->add( $msg, self::TYPE_WARNING );
        return self::getInstance();
    }
    
    /**
     *
     * @param string $msg
     * @return Yasc_Function_Helper_Flash 
     */    
    public function error( $msg ) {
        self::$_instance->add( $msg, self::TYPE_ERROR );
        return self::getInstance();
    }

    /**
     *
     * @param string $listClass
     * @param string $type     
     * @return string
     */
    public function draw( $listClass = null, $type = self::TYPE_MESSAGE ) {
		if ( true === empty( self::$_instance->_session[$type] ) ) {
			return "";
		}

		self::$_instance->_xhtml .= "<ul class=\"{$listClass}\">";

		foreach ( self::$_instance->_session[$type] as $msg ) {
			self::$_instance->_xhtml .= "<li>{$msg}</li>";
		}

		self::$_instance->_xhtml .= "</ul>";
        self::$_instance->clear( $type );
		return self::$_instance->_xhtml;
    }

    public function __toString() {
        return self::$_instance->draw();
    }
}
