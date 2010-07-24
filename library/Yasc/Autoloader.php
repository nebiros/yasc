<?php

/**
 * Class autoloader.
 *
 * @author jfalvarez
 */
class Yasc_Autoloader {
	public static function register() {
        return spl_autoload_register( array( 'Yasc_Autoloader', 'loadClass' ) );
    }

	public static function loadClass( $className ) {
        if ( $className == 'Yasc' ) {
            require 'Yasc.php';
            return;
        }

        if ( true === class_exists( $className ) || true === interface_exists( $className, false ) ) {
            return false;
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

        require $fileName;
	}
}
