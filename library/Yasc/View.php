<?php

/**
 * View object.
 *
 * @author jfalvarez
 */
class Yasc_View {
    /**
     * Bootstrap class.
     *
     * @var Yasc_Bootstrap
     */
    protected $_bootstrap = null;

    public function __construct( Yasc_Bootstrap $bootstrap = null ) {
        if ( null !== $bootstrap ) {
            $this->_bootstrap = $bootstrap;
        }
    }

    public function setBootstrap( Yasc_Bootstrap $bootstrap ) {
        $this->_bootstrap = $bootstrap;
        return $this;
    }

    public function getBoostrap() {
        return $this->_bootstrap;
    }

    /**
     * Get view output buffer.
     *
     * @param string $viewScript
     * @return string
     */
    public function getViewScript( $viewScript ) {
        $viewContent = null;
        
        ob_start();
        require_once $this->getBoostrap()->getConfig()->getViewsPath() . '/' . strtolower( $viewScript ) . ".phtml";
        $viewContent = ob_get_contents();
        ob_end_clean();

        return $viewContent;
    }

    /**
     * Render view.
     *
     * @param string $viewScript
     * @return void
     */
    public function render( $viewScript ) {
        $view = $this->getViewScript( $viewScript );
        echo $view;
    }

    /**
     * If the current request is a XMLHttpRequest.
     *
     * @return bool
     */
    public function isXhr() {
        if ( $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest" ) {
            return true;
        }

        return false;
    }
}
