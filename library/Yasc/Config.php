<?php

/**
 * Configuration.
 *
 * @author jfalvarez
 */
class Yasc_Config {
    protected $_viewsPath = null;

    public function __construct() {}

    public function getViewsPath() {
        return $this->_viewsPath;
    }

    public function setViewsPath( $viewsPath ) {
        if ( false === is_dir( $viewsPath ) ) {
            throw new Yasc_Exception( 'Views folder doesn\'t exists' );
        }

        $this->_viewsPath = $viewsPath;
        return $this;
    }
}
