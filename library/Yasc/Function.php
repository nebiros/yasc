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
 * @copyright Copyright (c) 2010 - 2011 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 * Class to handle a user defined function as an action.
 *
 * @package Yasc
 * @copyright Copyright (c) 2010 - 2011 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_Function extends ReflectionFunction {
    /**
     * Annotation.
     *
     * @var Yasc_Function_Annotation
     */
    protected $_annotation = null;

    /**
     * Default request method.
     *
     * @var string
     */
    protected $_method = Yasc_Router::METHOD_GET;

    /**
     * Function params.
     *
     * @var array
     */
    protected $_params = array();

    /**
     *
     * @param string $name
     */
    public function __construct( $name ) {
        parent::__construct( $name );        
        $this->process();
    }

    /**
     *
     * @return Yasc_Function_Annotation
     */
    public function getAnnotation() {
        return $this->_annotation;
    }

    /**
     *
     * @param string $method
     * @return Yasc_Function
     */
    public function setMethod( $method ) {
        $this->_method = $method;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getMethod() {
        return $this->_method;
    }

    /**
     *
     * @return array
     */
    public function getParams() {
        return $this->_params;
    }

    /**
     *
     * @param array $params
     * @return Yasc_Function
     */
    public function setParams( Array $params ) {
        $this->_params = $params;
        return $this;
    }

    /**
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function getParam( $key, $default = null ) {
        if ( true === isset( $this->_params[$key] ) ) {
            return $this->_params[$key];
        }

        return $default;
    }

    /**
     *
     * @param mixed $key
     * @param mixed $value
     * @return Yasc_Function
     */
    public function setParam( $key, $value = null ) {
        $this->_params[$key] = $value;
        return $this;
    }

    /**
     * Process function.
     *
     * @return Yasc_Function
     */
    public function process() {
        $this->_annotation = new Yasc_Function_Annotation( $this );        
        return $this;
    }
}