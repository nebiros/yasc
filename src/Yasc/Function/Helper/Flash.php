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
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 * Flash messages function helper.
 *
 * @package Yasc_Function
 * @subpackage Yasc_Function_Helper
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_Function_Helper_Flash {
    const TYPE_SUCCESS = "success";
    const TYPE_INFO = "info";
    const TYPE_WARNING = "warning";
    const TYPE_DANGER = "danger";

    /**
     *
     * @var Yasc_Function_Helper_Flash
     */
    protected static $_instance = null;

    protected function __construct() {}
    protected function __clone() {}

    /**
     *
     * @return Yasc_Function_Helper_Flash
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
        if (false === isset($_SESSION[__CLASS__])) {
            $_SESSION[__CLASS__] = array();
            $_SESSION[__CLASS__][self::TYPE_SUCCESS] = array();
            $_SESSION[__CLASS__][self::TYPE_INFO] = array();
            $_SESSION[__CLASS__][self::TYPE_WARNING] = array();
            $_SESSION[__CLASS__][self::TYPE_DANGER] = array();
        }
    }

    /**
     *
     * @param string|array $msg
     * @param string $type
     * @return Yasc_Function_Helper_Flash
     */
    public function add($msg, $type = self::TYPE_INFO) {
        if (true === is_array($msg)) {
            $_SESSION[__CLASS__][$type] = array_merge($_SESSION[__CLASS__][$type], $msg);
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
    public function set($msg, $type = self::TYPE_INFO) {
        $_SESSION[__CLASS__][$type] = (array) $msg;
        return self::getInstance();
    }

    /**
     *
     * @param string $type
     * @return array
     */
    public function get($type = self::TYPE_INFO) {
        return $_SESSION[__CLASS__][$type];
    }

    /**
     *
     * @param string $type
     * @return Yasc_Function_Helper_Flash
     */
    public function clear($type = self::TYPE_INFO) {
        $_SESSION[__CLASS__][$type] = array();
        return self::getInstance();
    }

    /**
     *
     * @param string $type
     * @return bool
     */
    public function has($type = self::TYPE_INFO) {
        return (false === empty($_SESSION[__CLASS__][$type])) ? true : false;
    }

    /**
     *
     * @param string $msg
     * @return Yasc_Function_Helper_Flash
     */
    public function success($msg) {
        $this->add($msg, self::TYPE_SUCCESS);
        return self::getInstance();
    }

    /**
     *
     * @param string $msg
     * @return Yasc_Function_Helper_Flash
     */
    public function info($msg) {
        $this->add($msg);
        return self::getInstance();
    }

    /**
     *
     * @param string $msg
     * @return Yasc_Function_Helper_Flash
     */
    public function warning($msg) {
        $this->add($msg, self::TYPE_WARNING);
        return self::getInstance();
    }

    /**
     *
     * @param string $msg
     * @return Yasc_Function_Helper_Flash
     */
    public function danger($msg) {
        $this->add($msg, self::TYPE_DANGER);
        return self::getInstance();
    }

    /**
     * Draw a <ul> list.
     *
     * @param string $type
     * @param string $className
     * @return string
     */
    public function draw($type = self::TYPE_INFO, $className = null) {
		if (true === empty($_SESSION[__CLASS__][$type])) {
			return "";
		}

		$xhtml = "<ul class=\"{$className}\">";

		foreach ($_SESSION[__CLASS__][$type] as $msg) {
			$xhtml .= "<li>{$msg}</li>";
		}

		$xhtml .= "</ul>";
        $this->clear($type);
		return $xhtml;
    }

    public function __toString() {
        return $this->draw();
    }
}
