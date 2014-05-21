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
 * Layout.
 *
 * @package Yasc
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_Layout {
    /**
     *
     * @var Yasc_Layout
     */
    protected static $_instance = null;

    /**
     *
     * @var string
     */
    protected $_layout = null;

    /**
     *
     * @var string
     */
    protected $_layoutPath = null;

    /**
     *
     * @var string
     */
    protected $_content = null;

    /**
     *
     * @var bool
     */
    protected $_disabled = false;

    protected function __construct() {}
    protected function __clone() {}

    /**
     *
     * @return Yasc_Layout
     */
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     *
     * @return string
     */
    public function getLayout() {
        return self::$_instance->_layout;
    }

    /**
     *
     * @param string $layout
     * @return Yasc_Layout
     */
    public function setLayout($layout) {
        self::$_instance->_layout = $layout;
        return self::$_instance;
    }

    /**
     *
     * @return string
     */
    public function getLayoutPath() {
        return self::$_instance->_layoutPath;
    }

    /**
     *
     * @param string $layoutPath
     * @return Yasc_Layout
     */
    public function setLayoutPath($layoutPath) {
        self::$_instance->_layoutPath = realpath($layoutPath);

        if (false === is_file(self::$_instance->_layoutPath)) {
            throw new Yasc_Exception("Layout file '{self::$_instance->_layoutPath}' not found");
        }

        self::$_instance->_layout = str_replace(".phtml", "", basename(self::$_instance->_layoutPath));
        return self::$_instance;
    }

    /**
     *
     * @return string
     */
    public function getContent() {
        return self::$_instance->_content;
    }

    /**
     *
     * @param string $content
     * @return Yasc_Layout
     */
    public function setContent($content) {
        self::$_instance->_content = $content;
        return self::$_instance;
    }

    /**
     *
     * @return void
     */
    public function disable() {
        self::$_instance->_disabled = true;
    }

    /**
     *
     * @return bool
     */
    public function isDisabled() {
        return self::$_instance->_disabled;
    }
}
