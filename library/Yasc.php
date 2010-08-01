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
 * @copyright Copyright (c) 2010 Juan Felipe Alvarez Sadarriaga. (http://www.jfalvarez.com)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

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
 * @copyright Copyright (c) 2010 Juan Felipe Alvarez Sadarriaga. (http://www.jfalvarez.com)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author jfalvarez
 */
class Yasc {
    const VERSION = '0.1.1';

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
            $this->_app = new Yasc_App();
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