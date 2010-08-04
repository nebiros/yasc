<?php

// Include Yasc.
require_once '../library/Yasc.php';

/**
 * Function to configure some yasc options.
 * 
 * @param Yasc_App_Config $config
 */
function configure( $config ) {
    // You can add a layout, a layout is just a .phtml file that represents
    // the site template.
    $config->setLayoutScript( dirname( __FILE__ ) . '/layouts/default.phtml' )
        // You can add more than one folder to store views, each view script
        // is a .phtml file.
        ->addViewsPath( dirname( __FILE__ ) . '/views' )
        ->addViewHelpersPath( dirname( __FILE__ ) . '/views/helpers' );
        // You can add more than one path of view helpers and set a
        // class prefix for the path added.
        // ->addViewHelpersPath( dirname( __FILE__ ) . '/../library/My/View/Helper', 'My_View_Helper' );
}

/**
 * @GET( '/' )
 *
 * Yasc pass to each script function two arguments, $view and $params,
 * $view argument is a Yasc_View object, a simple class to render .phtml scripts
 * and other stuff to handle views, $params is an associative array with
 * the url parameters.
 *
 * @param Yasc_View $view
 * @param array $params
 */
function index( $view, $params ) {
    echo 'Hello world!';
}

/**
 * @GET( '/tales' )
 *
 * @param Yasc_View $view
 * @param array $params
 */
function tales( $view, $params ) {
    // You can add variables to the view object and get his value on
    // the view script using the variable $this, like: $this->tales.
    $view->tales = 'hi! I\'m a view variable!';

    // Render a view script, a view script is a .phtml file where you can mix
    // php and html, the V in the MVC model.
    $view->render( 'tales' );
}

/**
 * @GET( '/tales/:lol' )
 * @POST( '/woot' ) // Ignored, yasc only uses the first annotation found.
 *
 * @param Yasc_View $view
 * @param array $params
 */
function tales1( $view, $params ) {
    // You can get params from $_GET or via $params argument.
    echo '<hr>lol value: ' . $_GET[':lol'] . ' -- ' . $params[':lol'];

    $view->tales = 'hi! I\'m a view variable!';
    // Disable layout.
    Yasc_Layout::getInstance()->disable();
    // Render a view without the layout.
    $view->render( 'tales' );
}

/**
 * @GET( '/tales/:lol/id/:id' )
 *
 * @param Yasc_View $view
 * @param array $params
 */
function tales2( $view, $params ) {
    echo '<hr>lol value: ' . $_GET[':lol'] . ' -- ' . $params[':lol'];
    echo '<hr>id value: ' . $_GET[':id'] . ' -- ' . $params[':id'];
}

/**
 * @POST( '/tales3' )
 */
function tales3() {
    echo '<pre>';
    echo '<hr>post: ';
    var_dump( $_POST );
    echo '</pre>';
}
