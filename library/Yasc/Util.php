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
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 *
 * @package Yasc
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_Util {
    /**
     * array_replace() for php <= 5.3, original code: 
     * http://us.php.net/manual/en/function.array-replace.php#105280
     * 
     * @see http://us.php.net/manual/en/function.array-replace.php#105280
     * @return array
     */
    public static function arrayReplace() { 
        $array = array();
        $n = func_num_args();
        
        while ($n-- > 0) { 
            $array += func_get_arg($n);
        }
        
        return $array; 
    }
    
    /**
     *
     * @see http://greengaloshes.cc/2007/04/recursive-multidimensional-array-search-in-php/
     * @param mixed $needle
     * @param array $haystack
     * @param bool $strict
     * @return bool 
     */
    public static function inArrayRecursive($needle, $haystack, $strict = false) {
        $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($haystack));

        foreach($it as $element) {
            if ((false === $strict && $element == $needle) || (true === $strict && $element === $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 
     * @see http://www.kerstner.at/en/2011/12/php-array-to-xml-conversion/
     * @param array $array the array to be converted
     * @param string? $rootElement if specified will be taken as root element, otherwise defaults to 
     *                <root>
     * @param SimpleXMLElement? if specified content will be appended, used for recursion
     * @return string XML version of $array
     */
    public static function arrayToXml($array, $rootElement = null, $xml = null) {
        $_xml = $xml;

        if ($_xml === null) {
            $_xml = new SimpleXMLElement($rootElement !== null ? $rootElement : "<root/>");
        }

        foreach ($array as $k => $v) {
            if (is_array($v)) { //nested array
                self::arrayToXml($v, $k, $_xml->addChild($k));
            } else {
                $_xml->addChild($k, $v);
            }
        }

        return $_xml->asXML();
    }    
}