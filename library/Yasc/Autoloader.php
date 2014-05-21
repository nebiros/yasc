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

require_once "Autoloader/Manager.php";

/**
 * Class autoloader.
 *
 * @package Yasc
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_Autoloader {
    /**
     *
     * @return bool 
     */
	public static function register() {
        return spl_autoload_register(array("Yasc_Autoloader", "loadClass"));
    }

    /**
     *
     * @param string $className
     * @return void
     */
    public static function loadClass($className) {
        if ($className == "Yasc") {
            require_once "Yasc.php";
            return;
        }

        if (true === class_exists($className) || true === interface_exists($className, false)) {
            return;
        }

        // Autodiscover the path from the class name
        // Implementation is PHP namespace-aware, and based on
        // PHP Framework Interop Group reference implementation:
        // https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md#example-implementation
        $className = ltrim($className, "\\");
        $filename  = "";
        $namespace = "";
        if ($lastNsPos = strripos($className, "\\")) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $filename  = str_replace("\\", DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        $filename .= str_replace("_", DIRECTORY_SEPARATOR, $className) . ".php";

        if (false === self::loadFile($filename)) {
            return;
        }
    }
    
    /**
     *
     * @param string $filename
     * @return false|void
     */
    public static function loadFile($filename) {
        $manager = Yasc_Autoloader_Manager::getInstance();

        // search in namespaces.
        $dirs = $manager->getPaths(Yasc_Autoloader_Manager::PATH_TYPE_MODEL)
            + $manager->getPaths(Yasc_Autoloader_Manager::PATH_TYPE_FUNCTION_HELPER)
            + $manager->getPaths(Yasc_Autoloader_Manager::PATH_TYPE_VIEW_HELPER)
            + $manager->getPaths(Yasc_Autoloader_Manager::PATH_TYPE_NS);        
        if (true === self::_require($filename, $dirs)) {
            return;
        }
        
        // search in the include path.
        $dirs = explode(PATH_SEPARATOR, get_include_path());        
        if (true === self::_require($filename, $dirs)) {
            return;
        }
        
        return false;
    }
    
    /**
     *
     * @param string $filename
     * @param array $dirs
     * @return bool 
     */
    protected static function _require($filename, Array $dirs) {
        $manager = Yasc_Autoloader_Manager::getInstance();        
        $prefix = $manager->getPrefix($filename);
        $prefixPath = $dirs[$prefix];

        if (false === empty($prefixPath)) {
            $file = realpath($prefixPath . "/" . basename($filename));
            if (true === is_file($file)) {
                require_once $file;
                return true;
            }            
        }
        
        $found = false;        
        foreach ($dirs as $ns => $path) {
            $file = realpath($path . "/" . $filename);
            if (true === is_file($file)) {
                require_once $file;
                $found = true;                
                break;
            }
        }
        
        return $found;
    }
}
