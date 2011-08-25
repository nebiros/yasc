<?php

/**
 *
 * @author nebiros
 */
class Helper_Bar extends Yasc_View_Helper_HelperAbstract {
    /**
     * From the php manual:
     * 
     * "For backwards compatibility, if PHP 5 cannot find a __construct() function 
     * for a given class, it will search for the old-style constructor function, 
     * by the name of the class. Effectively, it means that the only case that would 
     * have compatibility issues is if the class had a method named __construct() 
     * which was used for different semantics."
     * 
     * So you need to add a __construct method to not call the helper twice.
     */
    public function __construct() {}
    
    public function bar() {
        return 'hi from: ' . __METHOD__ . '!';
    }
}
