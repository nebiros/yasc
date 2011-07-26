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

        if ( false === isset( $_SESSION[__CLASS__] ) ) {
            $_SESSION[__CLASS__] = array();
            $_SESSION[__CLASS__][self::TYPE_NOTICE] = array();
            $_SESSION[__CLASS__][self::TYPE_MESSAGE] = array();
            $_SESSION[__CLASS__][self::TYPE_WARNING] = array();
            $_SESSION[__CLASS__][self::TYPE_ERROR] = array();
            $_SESSION[__CLASS__];
        }
    }

    /**
     *
     * @param string|array $msg
     * @param string $type
     * @return Yasc_Function_Helper_Flash
     */
    public function add( $msg, $type = self::TYPE_MESSAGE ) {
        if ( true === is_array( $msg ) ) {
            $_SESSION[__CLASS__][$type] = array_merge( $_SESSION[__CLASS__][$type], $msg );
        } else {
            $_SESSION[__CLASS__][$type][] = $msg;
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
        $_SESSION[__CLASS__][$type] = ( array ) $msg;
        return self::getInstance();
    }
    
    /**
     *
     * @param string $type
     * @return Yasc_Function_Helper_Flash
     */
    public function get( $type = self::TYPE_MESSAGE ) {
        return $_SESSION[__CLASS__][$type];
    }
    
    /**
     *
     * @param string $type
     * @return Yasc_Function_Helper_Flash
     */
    public function clear( $type = self::TYPE_MESSAGE ) {
        $_SESSION[__CLASS__][$type] = array();
        return self::getInstance();
    }
    
    /**
     *
     * @param string $type
     * @return Yasc_Function_Helper_Flash
     */    
    public function has( $type = self::TYPE_MESSAGE ) {
        return ( false === empty( $_SESSION[__CLASS__][$type] ) ) ? true : false;
    }
    
    /**
     *
     * @param string $msg
     * @return Yasc_Function_Helper_Flash 
     */
    public function notice( $msg ) {
        $this->add( $msg, self::TYPE_NOTICE );
        return self::getInstance();
    }
    
    /**
     *
     * @param string $msg
     * @return Yasc_Function_Helper_Flash 
     */    
    public function message( $msg ) {
        $this->add( $msg );
        return self::getInstance();
    }
    
    /**
     *
     * @param string $msg
     * @return Yasc_Function_Helper_Flash 
     */    
    public function warning( $msg ) {
        $this->add( $msg, self::TYPE_WARNING );
        return self::getInstance();
    }
    
    /**
     *
     * @param string $msg
     * @return Yasc_Function_Helper_Flash 
     */    
    public function error( $msg ) {
        $this->add( $msg, self::TYPE_ERROR );
        return self::getInstance();
    }

    /**
     *
     * @param string $listClass
     * @param string $type     
     * @return string
     */
    public function draw( $listClass = null, $type = self::TYPE_MESSAGE ) {
		if ( true === empty( $_SESSION[__CLASS__][$type] ) ) {
			return "";
		}

		$this->_xhtml .= "<ul class=\"{$listClass}\">";

		foreach ( $_SESSION[__CLASS__][$type] as $msg ) {
			$this->_xhtml .= "<li>{$msg}</li>";
		}

		$this->_xhtml .= "</ul>";
        $this->clear( $type );
		return $this->_xhtml;
    }

    public function __toString() {
        return $this->draw();
    }
}
