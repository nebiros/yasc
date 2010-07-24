<?php

/**
 * Class to handle a function as an action.
 *
 * @author jfalvarez
 */
class Yasc_Function extends ReflectionFunction {
    /**
     * Annotation.
     *
     * @var Yasc_Annotation
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

    public function __construct( $name ) {
        parent::__construct( $name );
        
        $this->process();
    }

    public function getAnnotation() {
        return $this->_annotation;
    }

    public function setMethod( $method ) {
        $this->_method = $method;
        return $this;
    }

    public function getMethod() {
        return $this->_method;
    }

    public function getParams() {
        return $this->_params;
    }

    public function setParams( Array $params ) {
        $this->_params = $params;
        return $this;
    }

    public function getParam( $key, $default = null ) {
        if ( true === isset( $this->_params[$key] ) ) {
            return $this->_params[$key];
        }

        return $default;
    }

    public function setParam( $key, $value = null ) {
        $this->_params[$key] = $value;
        return $this;
    }

    /**
     * Process function annotation.
     *
     * @return Yasc_Function
     */
    public function process() {
        $this->_annotation = new Yasc_Annotation( $this );        
        return $this;
    }
}