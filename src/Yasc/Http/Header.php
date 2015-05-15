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
 * @subpackage Yasc_Http
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 *
 * @package Yasc
 * @subpackage Yasc_Http
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_Http_Header extends \ArrayObject {
    /**
     * @var array
     */
    protected static $_special = array(
        "CONTENT_TYPE",
        "CONTENT_LENGTH",
        "PHP_AUTH_USER",
        "PHP_AUTH_PW",
        "PHP_AUTH_DIGEST",
        "AUTH_TYPE"
    );
    
    /**
     * @return array
     */
    public static function extract() {
        $results = array();
        foreach ($_SERVER as $key => $value) {
            $key = strtoupper($key);
            if (strpos($key, "X_") === 0 || strpos($key, "HTTP_") === 0 || in_array($key, static::$_special)) {
                if ($key === "HTTP_CONTENT_LENGTH") {
                    continue;
                }
                $results[$key] = $value;
            }
        }
        return $results;
    }
}
