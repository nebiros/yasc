<?php

// Include Yasc.
require_once '../library/Yasc.php';

/**
 * Function to configure some yasc options.
 * 
 * @param Yasc_Config $config
 */
function configure( $config ) {
    $config->setViewsPath( realpath( dirname( __FILE__ ) . "/views" ) );
}

/**
 * @GET( '/' )
 *
 * Yasc pass to each script function two arguments, $view and $params,
 * $view argument is a Yasc_View object, a simple class to render views and
 * other stuff to handle views, $params is an associative array with 
 * the url parameters.
 *
 * @param Yasc_View $view
 * @param array $params
 */
function index( $view, $params ) {
    echo '<pre>';
    echo '<hr>Hello world!';
    echo '</pre>';

    // Render a view, a view file is a .phtml file where you can access to
    // the request method global variables, $_GET, $_POST, etc.
    $view->render( 'index' );
}

/**
 * @GET( '/tales/:lol' )
 * @POST( '/woot' ) // Ignored, yasc uses the first annotation occurence to start working.
 *
 * @param Yasc_View $view
 * @param array $params
 */
function tales( $view, $params ) {
    echo '<pre>';
    // You can get params from $_GET or via $params argument.
    echo '<hr>lol value: ' . $_GET[':lol'] . ' -- ' . $params[':lol'];
    echo '</pre>';

    // Render a view, a view file is a .phtml file where you can access to
    // the request method global variables, $_GET, $_POST, etc.
    $view->render( 'tales' );
}

/**
 * @GET( '/tales/:lol/id/:id' )
 *
 * @param Yasc_View $view
 * @param array $params
 */
function tales2( $view, $params ) {
    echo '<pre>';
    // You can get params from $_GET or via $params argument.
    echo '<hr>lol value: ' . $_GET[':lol'] . ' -- ' . $params[':lol'];
    echo '<hr>id value: ' . $_GET[':id'] . ' -- ' . $params[':id'];
    echo '</pre>';
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