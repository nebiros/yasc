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
        // 
        // Add extra options to the configuration object.
        // 
        // Some $mysql connection resource ...
        // ->addOption( "db", $mysql );
}

/**
 * @GET( '/' )
 *
 * Yasc pass to each script function three arguments, $view, $params and $config,
 * $view argument is a Yasc_View object, a simple class to render .phtml scripts
 * and other stuff to handle views, $params is an associative array with
 * the url parameters and the $config argument is a Yasc_App_Config object, you
 * can get options from the configuration function.
 * 
 * @param Yasc_View $view
 * @param array $params
 * @param Yasc_App_Config $config
 */
function index( $view, $params, $config ) {
    // Use layout view helper to disable the layout or use Yasc_Layout object
    // Yasc_Layout::getInstance()->disable(), Yasc_Layout uses singleton pattern.    
    $view->layout()->disable();
    
    // Get the mysql resource from this app configuration option.
    // 
    // $mysql = $config->getOption( "db" );
    // 
    // ... do some sql operation.
    
    echo 'Hello world!';
}

/**
 * @POST( '/' )
 * 
 * You can route the same url to another function using a different request
 * method.
 * 
 */
function save_index( $view, $params ) {
    $view->layout()->disable();
    
    echo '<pre>';
    echo 'post: ';
    var_dump( $_POST );
    echo 'params: ';
    var_dump( $params );
    echo '</pre>';
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
    $view->tales = 'oh! I\'m a view variable!';

    // Render a view script, a view script is a .phtml file where you can mix
    // php and html, the V in the MVC model, in this example the view files
    // are stored in views/ folder.
    // 
    // This view calls a view helper (Tales), so check views/helpers/Tales.php 
    // to see what it does.
    $view->render( 'tales' );
}

/**
 * @GET( '/tales/:lol' )
 * @POST( '/woot' ) // Ignored, yasc only uses the first annotation found.
 * 
 * Named params, you can access those via $params argument.
 * 
 * Matches: /tales/foo" and /tales/bar
 *
 * @param Yasc_View $view
 * @param array $params
 */
function tales1( $view, $params ) {
    $view->layout()->disable();
    
    // You can get params from $_GET or via $params argument.
    echo '<hr>lol value: ' . $_GET['lol'] . ' -- ' . $params['lol'];
    $view->tales = 'oh! I\'m a view variable!';    
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
    $view->layout()->disable();
    
    echo '<hr>lol value: ' . $_GET['lol'] . ' -- ' . $params['lol'];
    echo '<hr>id value: ' . $_GET['id'] . ' -- ' . $params['id'];
}

/**
 * @POST( '/tales3' )
 */
function tales3() {
    $view->layout()->disable();
    
    echo '<pre>';
    echo 'post: ';
    var_dump( $_POST );
    echo '</pre>';
}

/**
 * @GET( '/foo' )
 * 
 * @param Yasc_View $view
 */
function foo( $view ) {
    // Render view script foo, this view script calls the view helper class Foo,
    // this view helper render a view helper script inside and return his content
    // to this view, a view helper script is just another .phtml file, if you don't
    // want to create a whole html string inside the helper ;).
    $view->render( 'foo' );
}

/**
 * @GET( '^/regex/id/(\d+)/name/([a-z]+)' )
 * 
 * You can create patterns using a regular expression, this kind of route must
 * begin with a '^'. Check limonade docs and code if you want more information, or
 * just use limonade framework :), it's a more robust framework.
 * 
 * http://www.limonade-php.net/README.htm
 * https://github.com/sofadesign/limonade/blob/master/lib/limonade.php
 * 
 * Matches: /regex/id/26/name/juan
 * 
 * @param Yasc_View $view
 * @param array $params
 */
function regex( $view, $params ) {
    $view->layout()->disable();
    
    echo '<pre>';
    echo 'params: ';
    var_dump( $params );
    echo '</pre>';
}

/**
 * @GET( '/say/*\/to\/*' )
 * 
 * Patterns may also include wildcard parameters. Each value is associted 
 * through numeric indexes, in the same order as in the pattern.
 * 
 * Matches: /say/hello/to/world
 * 
 * @param Yasc_View $view
 * @param array $params
 */
function single_asterisk( $view, $params ) {
    $view->layout()->disable();
    
    echo '<pre>';
    echo 'params: ';
    var_dump( $params );
    echo 'hello: ';
    var_dump( $params[0] ); // hello
    echo 'world: ';
    var_dump( $params[1] ); // world
    echo '</pre>';    
}

/**
 * @GET( '/download/*.*' )
 * 
 * Matches: /download/file.xml
 * 
 * @param Yasc_View $view
 * @param array $params
 */
function download( $view, $params ) {
    $view->layout()->disable();
    
    echo '<pre>';
    echo 'params: ';
    var_dump( $params );
    echo 'filename: ';
    var_dump( $params[0] ); // file
    echo 'ext: ';
    var_dump( $params[1] ); // xml
    echo '</pre>';    
}

/**
 * @GET( '/writing/*\/to\/**' )
 * 
 * The double wildcard '**' matches a string like this one: my/friend/juan/badass,
 * this string is treated as pairs, this way: param1/value1/param2/value2 etc, like
 * Zend Framework does, so, via $params argument you can get those values using
 * each key.
 * 
 * Matches: /writing/hello/to/my/friends/from/limonade/you_guys/roxor
 * 
 * @param Yasc_View $view
 * @param array $params
 */
function pairs( $view, $params ) {
    $view->layout()->disable();
    
    echo '<pre>';
    echo 'params: ';
    var_dump( $params );
    echo 'single wildcard: ';
    var_dump( $params[0] ); // hello
    echo 'param my: ';
    var_dump( $params["my"] ); // friend
    echo 'param from: ';
    var_dump( $params["from"] ); // limonade
    echo 'param you_guys: ';
    var_dump( $params["you_guys"] ); // roxor
    echo '</pre>';    
}
