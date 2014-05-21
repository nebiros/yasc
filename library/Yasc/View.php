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
 * View.
 *
 * @package Yasc
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_View {
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
     * @var bool
     */
    protected $_useViewStream = false; 

    /**
     *
     */
    public function __construct() {
        $this->_useViewStream = Yasc_App::getInstance()->getConfig()->useViewStream();

        if (true === $this->_useViewStream) {
            if (false === in_array("view", stream_get_wrappers())) {
                stream_wrapper_register("view", "Yasc_View_Stream");
            }
        }
    }

    /**
     *
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        if ("_" != substr($name, 0, 1) ) {
            $this->$name = $value;
            return;
        }

        throw new Yasc_View_Exception("Private or protected attributes are not allowed");
    }

    /**
     *
     * @param mixed $name
     * @return bool
     */
    public function __isset($name) {
        if ("_" != substr($name, 0, 1) ) {
            return isset($this->$name);
        }

        return false;
    }

    /**
     *
     * @param mixed $name
     * @return mixed|null
     */
    public function __get($name) {
        if (true === isset($this->$name)) {
            return $this->$name;
        }

        trigger_error("Undefined property '{$name}'", E_USER_NOTICE);        
        return null;
    }

    /**
     *
     * @param mixed $name
     * @return void
     */
    public function __unset($name) {
        if ("_" != substr($name, 0, 1) && true === isset($this->$name)) {
            unset($this->$name);
        }
    }

    /**
     *
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments) {
        $helper = Yasc_App::getInstance()->getHelperManager()->getHelper($name);
        
        if (false === is_callable(array($helper, $name))) {
            throw new Yasc_View_Exception("Helper '{$name}' isn't callable");
        }
        
        return call_user_func_array(array($helper, $name), $arguments);
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
    public function setViewScript($filename) {
        $this->_viewScript = null;
        
        $folders = Yasc_App::getInstance()->getConfig()->getViewsPaths();

        foreach ($folders as $path) {
            if (true === is_file(realpath($path . "/" . $filename . ".phtml"))) {
                $this->_viewScript = realpath($path . "/" . $filename . ".phtml");
                break;
            }
        }

        if (null === $this->_viewScript) {
            throw new Yasc_View_Exception("View file '{$filename}' not found in this paths: " . implode(", ", $folders));
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
    public function setBuffer($buffer) {
        $this->_buffer = $buffer;
        return $this;
    }

    /**
     * Process view script.
     *
     * @param string $filename
     * @return string
     */
    protected function _processViewScript($filename) {
        $this->setViewScript($filename);
        unset($filename);
        
        ob_start();
        
        if (true === $this->_useViewStream) {
            include "view://" . $this->_viewScript;            
        } else {
            include $this->_viewScript;
        }
        
        $this->_buffer = ob_get_clean();

        return $this->_buffer;
    }

    /**
     * Render view.
     *
     * @param string $filename
     * @return string
     */
    public function render($filename) {
        return $this->_processViewScript($filename);
    }

    /**
     *
     * @return string
     */
    public function __toString() {
        return $this->_buffer;
    }
}
