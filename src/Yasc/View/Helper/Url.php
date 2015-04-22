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
class Yasc_View_Helper_Url extends Yasc_View_Helper_HelperAbstract {
    public function __construct() {}
        
    public function __call($name, $arguments) {
        switch ($name) {
            case "url":
                if (count($arguments) === 1 && is_array($arguments[0])) {
                    return $this->urlServerNamePath($arguments[0]);
                } else if (count($arguments) === 1 && is_string($arguments[0])) {
                    return $this->urlFor($arguments[0]);
                } else {
                    return $this->urlFor(null);
                }
                
                break;
                
            default:
                return $this->urlFor(null);
                break;
        }
    }   

    /**
     *
     * @param array $options
     * @return string 
     */
    protected function urlServerNamePath(Array $options = null) {
        $serverName = isset($options["server_name"]) ? $options["server_name"] : null;
        $path = isset($options["path"]) ? $options["path"] : null;

        return Yasc_App::getInstance()->getRouter()->url($serverName, $path);
    }
	
    /**
     *
     * @param string $path
     * @return string 
     */
    protected function urlFor($path) {
        return Yasc_App::getInstance()->getRouter()->urlFor($path);
    }
}
