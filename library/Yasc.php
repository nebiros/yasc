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

defined( 'APPLICATION_PATH' )
    || define( 'APPLICATION_PATH', realpath( getcwd() ) );

defined( 'APPLICATION_SCRIPT' )
    || define( 'APPLICATION_SCRIPT', $_SERVER['SCRIPT_NAME'] );

set_include_path( implode( PATH_SEPARATOR, array(
    realpath( dirname( __FILE__ ) ),
    get_include_path()
) ) );

require_once 'Yasc/Autoloader.php';
Yasc_Autoloader::register();

/**
 * Yet Another Sinatra Clone.
 *
 * @package Yasc
 * @copyright Copyright (c) 2010 - 2011 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc {
    const VERSION = '0.1.15';

    /**
     * App.
     *
     * @var Yasc_App
     */
    protected $_app = null;

    public function __construct() {}

    /**
     *
     * @return Yasc_App
     */
    public function getApp() {
        if ( null === $this->_app ) {
            $this->_app = Yasc_App::getInstance();
        }

        return $this->_app;
    }

    /**
     * Run.
     *
     * @return void
     */
    public function run() {
        $this->getApp()->run();
    }
}

$yasc = new Yasc();
$yasc->run();
unset( $yasc );
