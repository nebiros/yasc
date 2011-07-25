<?php

/**
 *
 * @author nebiros
 */
class Helper_Foo extends Yasc_View_Helper_HelperAbstract {
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
    
    /**
     *
     * @param array $options 
     */
    public function foo( Array $options = null ) {
        // You can pass params to each view helper and assign those to the view.
        $this->view->param1 = $options["param1"];
        $this->view->param2 = $options["param2"];
        
        // The view object is inected to each view helper, so you can render
        // a view helper script and get this view as a string using:
        // Yasc_View#getBuffer method or just send the view object and print it.
        $this->view->render( "_foo" );
        return $this->view;
    }
}
