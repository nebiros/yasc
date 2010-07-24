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
 */
function index( $view, $params ) {
    echo '<pre>';
    echo '<hr>Hello world!';
    echo '<hr>v: ';
    var_dump($view);
    echo '<hr>params:';
    var_dump($params);
    echo '</pre>';

    $view->render( 'index' );
}

/**
 * @GET( '/tales/:lol' )
 * @POST( '/woot' ) // Ignored
 */
function tales() {
    echo '<pre>';
    echo '<hr>lol value: ' . $_GET[':lol'];
    echo '<hr>req: ';
    var_dump($_GET);
    echo '</pre>';
}

/**
 * @GET( '/tales/:lol/id/:id' )
 */
function tales2( $view, $params ) {
    echo '<pre>';
    echo '<hr>lol value: ' . $_GET[':lol'];
    echo '<hr>id value: ' . $_GET[':id'];
    echo '<hr>v: ';
    var_dump($view);
    echo '<hr>params:';
    var_dump($params);
    echo '<hr>get: ';
    var_dump($_GET);
    echo '</pre>';
}

/**
 * @POST( "/tales3" )
 */
function tales3() {
    echo '<pre>';
    echo '<hr>post: ';
    var_dump($_POST);
    echo '</pre>';
}