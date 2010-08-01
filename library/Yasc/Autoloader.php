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

/**
 * Class autoloader.
 *
 * @package Yasc
 * @copyright Copyright (c) 2010 Juan Felipe Alvarez Sadarriaga. (http://www.jfalvarez.com)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author jfalvarez
 */
class Yasc_Autoloader {
	public static function register() {
        return spl_autoload_register( array( 'Yasc_Autoloader', 'loadClass' ) );
    }

    /**
     *
     * @param string $className
     * @return void
     */
	public static function loadClass( $className ) {
        if ( $className == 'Yasc' ) {
            require 'Yasc.php';
            return;
        }

        if ( true === class_exists( $className ) || true === interface_exists( $className, false ) ) {
            return;
        }

        // Autodiscover the path from the class name
        // Implementation is PHP namespace-aware, and based on
        // Framework Interop Group reference implementation:
        // http://groups.google.com/group/php-standards/web/psr-0-final-proposal
        $className = ltrim( $className, '\\' );
        $fileName  = '';
        $namespace = '';
        if ( $lastNsPos = strripos( $className, '\\' ) ) {
            $namespace = substr( $className, 0, $lastNsPos );
            $className = substr( $className, $lastNsPos + 1 );
            $fileName  = str_replace( '\\', DIRECTORY_SEPARATOR, $namespace ) . DIRECTORY_SEPARATOR;
        }

        $fileName .= str_replace( '_', DIRECTORY_SEPARATOR, $className ) . '.php';

        if ( false === self::_fileExists( $fileName ) ) {
            return;
        }

        require $fileName;
	}

    /**
     * Search for a file into each path from the include path.
     *
     * @param string $fileName
     * @return bool
     */
    protected static function _fileExists( $fileName ) {
        $paths = explode( PATH_SEPARATOR, get_include_path() );

        foreach ( $paths as $path ) {
            if ( true === is_file( realpath( $path . '/' . $fileName ) ) ) {
                return true;
            }
        }

        return false;
    }
}
