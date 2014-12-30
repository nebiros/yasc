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
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 * Configuration.
 *
 * @package Yasc
 * @subpackage Yasc_App
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
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
     *
     * @var bool
     */
    protected $_useViewStream = false;

    /**
     * Layout.
     * 
     * @var string
     */
    protected $_layoutScript = null;

    /**
     *
     */
    public function __construct() {
        $this->_addDefaultPaths();
    }
    
    /**
     *
     * @param array $options
     * @return Yasc_App_Config 
     */
    public function setOptions(Array $options) {
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
    public function getOption($key, $default = null) {
        if (true === isset($this->_options[$key])) {
            return $this->_options[$key];
        }
        
        return $default;        
    }
    
    /**
     *
     * @param array $options
     * @return Yasc_App_Config 
     */
    public function addOptions(Array $options) {
        $this->_options = array_merge($this->_options, $options);
        return $this;
    }
    
    /**
     *
     * @param mixed $key
     * @param mixed $value
     * @return Yasc_App_Config 
     */
    public function addOption($key, $value = null) {
        $this->_options[$key] = $value;
        return $this;
    }
    
    /**
     *
     * @param bool $flag
     * @return Yasc_App_Config 
     */
    public function setViewStream($flag = false) {
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
    public function getViewsPaths() {
        return Yasc_Autoloader_Manager::getInstance()->getPaths(
            Yasc_Autoloader_Manager::PATH_TYPE_VIEW);
    }

    /**
     *
     * @param string $path
     * @return Yasc_App_Config
     */
    public function setViewsPath($path) {
        Yasc_Autoloader_Manager::getInstance()->addPath(
            Yasc_Autoloader_Manager::PATH_TYPE_VIEW_HELPER, $path);
        return $this;
    }

    /**
     *
     * @param string $path
     * @return Yasc_App_Config
     */
    public function addViewsPath($path) {
        Yasc_Autoloader_Manager::getInstance()->addPath(
            Yasc_Autoloader_Manager::PATH_TYPE_VIEW, $path);
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
    public function setLayoutScript($layout) {
        if (false === is_string($layout)) {
            return false;
        }
        
        $layout = realpath($layout);
        
        if (false === is_file($layout)) {
            throw new Yasc_App_Exception("Layout file '{$layout}' not found");
        }

        $this->_layoutScript = $layout;
        // Cause a layout is a view too, we going to add layout script folder
        // to the views folders.
        $this->addViewsPath(dirname($this->_layoutScript));

        return $this;
    }
    
    /**
     *
     * @return array
     */
    public function getViewHelperPaths() {
        return Yasc_Autoloader_Manager::getInstance()->getPaths(
            Yasc_Autoloader_Manager::PATH_TYPE_VIEW_HELPER);
    }
    
    /**
     *
     * @param string $classPrefix
     * @return string 
     */
    public function getViewHelpersPath($classPrefix = null) {
        return Yasc_Autoloader_Manager::getInstance()->getPath(
            Yasc_Autoloader_Manager::PATH_TYPE_VIEW_HELPER, $classPrefix);
    }
    
    /**
     *
     * @param string $path
     * @param string $classPrefix
     * @return Yasc_App_Config 
     */
    public function setViewHelpersPath($path, $classPrefix = null) {
        Yasc_Autoloader_Manager::getInstance()->setPath(
            Yasc_Autoloader_Manager::PATH_TYPE_VIEW_HELPER, $path, $classPrefix);
        return $this;
    }    
    
    /**
     *
     * @param string $path
     * @param string $classPrefix
     * @return Yasc_App_Config 
     */
    public function addViewHelpersPath($path, $classPrefix = null) {
        Yasc_Autoloader_Manager::getInstance()->addPath(
            Yasc_Autoloader_Manager::PATH_TYPE_VIEW_HELPER, $path, $classPrefix);
        return $this;
    }
    
    /**
     *
     * @return array
     */
    public function getFunctionHelperPaths() {
        return Yasc_Autoloader_Manager::getInstance()->getPaths(
            Yasc_Autoloader_Manager::PATH_TYPE_FUNCTION_HELPER);
    }
    
    /**
     *
     * @param string $classPrefix
     * @return string 
     */
    public function getFunctionHelpersPath($classPrefix = null) {
        return Yasc_Autoloader_Manager::getInstance()->getPath(
            Yasc_Autoloader_Manager::PATH_TYPE_FUNCTION_HELPER, $classPrefix);
    }
    
    /**
     *
     * @param string $path
     * @param string $classPrefix
     * @return Yasc_App_Config 
     */    
    public function setFunctionHelpersPath($path, $classPrefix = null) {
        Yasc_Autoloader_Manager::getInstance()->setPath(
            Yasc_Autoloader_Manager::PATH_TYPE_FUNCTION_HELPER, $path, $classPrefix);
        return $this;
    }    
    
    /**
     *
     * @param string $path
     * @param string $classPrefix
     * @return Yasc_App_Config 
     */
    public function addFunctionHelpersPath($path, $classPrefix = null) {
        Yasc_Autoloader_Manager::getInstance()->addPath(
            Yasc_Autoloader_Manager::PATH_TYPE_FUNCTION_HELPER, $path, $classPrefix);
        return $this;
    }
    
    /**
     *
     * @return array
     */
    public function getModelPaths() {
        return Yasc_Autoloader_Manager::getInstance()->getPaths(
            Yasc_Autoloader_Manager::PATH_TYPE_MODEL);
    }

    /**
     *
     * @param string $path
     * @param string $classPrefix
     * @return Yasc_App_Config 
     */
    public function setModelsPath($path, $classPrefix = null) {
        Yasc_Autoloader_Manager::getInstance()->setPath(
            Yasc_Autoloader_Manager::PATH_TYPE_MODEL, $path, $classPrefix);
        return $this;
    }
        
    /**
     *
     * @param string $path
     * @param string $classPrefix
     * @return Yasc_App_Config
     */    
    public function addModelsPath($path, $classPrefix = null) {
        Yasc_Autoloader_Manager::getInstance()->addPath(
            Yasc_Autoloader_Manager::PATH_TYPE_MODEL, $path, $classPrefix);
        return $this;
    }
    
    
    /**
     * 
     * @return void
     */
    protected function _addDefaultPaths() {
        $views = realpath(APPLICATION_PATH . "/views");
        $helpers = realpath(APPLICATION_PATH . "/views/helpers");
        $models = realpath(APPLICATION_PATH . "/models");
        
        // default paths, if they exist.
        if (true === is_dir($views)) {
            Yasc_Autoloader_Manager::getInstance()->addPath(
                Yasc_Autoloader_Manager::PATH_TYPE_VIEW, $views);
        }
        
        if (true === is_dir($helpers)) {
            Yasc_Autoloader_Manager::getInstance()->addPath(
                Yasc_Autoloader_Manager::PATH_TYPE_VIEW_HELPER, $helpers, "Helper");
        }
        
        if (true === is_dir($models)) {
            Yasc_Autoloader_Manager::getInstance()->addPath(
                Yasc_Autoloader_Manager::PATH_TYPE_MODEL, $models, "Model");
        }
    }
}
