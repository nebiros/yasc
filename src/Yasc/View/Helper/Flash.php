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
 * @package Yasc_View
 * @subpackage Yasc_View_Helper
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 *
 * @package Yasc_View
 * @subpackage Yasc_View_Helper
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_View_Helper_Flash extends Yasc_View_Helper_HelperAbstract {
    public function __construct() {}

    /**
     *
     * @return Yasc_View_Helper_Flash
     */
    public function flash() {
        return $this;
    }

    /**
     *
     * @return Yasc_Function_Helper_Flash
     */
    public function messages() {
        /* @var $f Yasc_Function_Helper_Flash */
        $f = Yasc_App::functionHelper("flash");
        return $f;
    }
}