<?php

set_include_path( implode( PATH_SEPARATOR, array(
    realpath( dirname( __FILE__ ) ),
    get_include_path()
) ) );

require_once 'Yasc/Autoloader.php';
Yasc_Autoloader::register();

/**
 * Yet Another Sinatra Clone
 *
 * @author jfalvarez
 */
class Yasc {
    /**
     * Bootstrap.
     *
     * @var Yasc_Bootstrap
     */
    protected $_bootstrap = null;

    public function __construct() {}

    public function getBootstrap() {
        if ( null === $this->_bootstrap ) {
            $this->_bootstrap = new Yasc_Bootstrap();
        }

        return $this->_bootstrap;
    }

    public function run() {
        $this->getBootstrap()->run();
    }
}

$yasc = new Yasc();
$yasc->run();
unset( $yasc );