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

/**
 *
 * @package Yasc
 * @copyright Copyright (c) 2010 - 2011 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
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
        
        while ( $n-- > 0 ) { 
            $array += func_get_arg( $n );
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
    public static function inArrayRecursive( $needle, $haystack, $strict = false ) {
        $it = new RecursiveIteratorIterator( new RecursiveArrayIterator( $haystack ) );

        foreach( $it as $element ) {
            if ( ( false === $strict && $element == $needle ) || ( true === $strict && $element === $needle ) ) {
                return true;
            }
        }

        return false;
    }    
}