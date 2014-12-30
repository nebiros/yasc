<?php

// composer autoload.
require_once "../../vendor/autoload.php";
// yasc runtime.
require_once "../../vendor/nebiros/yasc/src/Yasc.php";

/**
 * Function to configure some yasc options. This function is optional you don't
 * need to write it in your app script if you don't want.
 */
function configure() {
    // * You can add a layout, a layout is just a .phtml file that represents
    // the site template.
    Yasc_App::config()->setLayoutScript(dirname(__FILE__) . "/layouts/default.phtml");
    
    // * If you want to use a stream wrapper to convert markup of mostly-PHP 
    // templates into PHP prior to include(), seems like is a little bit slow,
    // so by default is off.
    // ->setViewStream(true);
    // 
    // * You can add more than one folder to store views, each view script
    // is a .phtml file.
    // ->addViewsPath(dirname(__FILE__) . "/extra_views");
    // 
    // * You can add more than one path of view helpers and set a
    // class prefix for the path added.
    // ->addViewHelpersPath(dirname(__FILE__) . "/../library/My/View/Helper", "My_View_Helper");
    // 
    // or if you don't want a class prefix just leave it blank.
    // ->addViewHelpersPath(dirname(__FILE__) . "/extra_views/helpers");
    //
    // * Function helpers, second argument is a prefix class.
    // ->addFunctionHelpersPath(dirname(__FILE__) . "/extra_function_helpers");
    // 
    // * Add models folder, second argument is a prefix class.
    // ->addModelsPath(dirname(__FILE__) . "/models");
    // ->addModelsPath(dirname(__FILE__) . "/extra_models/My/Model", "My_Model");
    // 
    // * Add extra options to the configuration object, like some $mysql connection 
    // resource or a global flag, etc.
    // ->addOption("db", $mysql);
}

/**
 * @GET("/")
 */
function index() {
    // Use layout view helper to disable the layout or use Yasc_Layout object
    // Yasc_Layout::getInstance()->disable(), Yasc_Layout uses singleton pattern.    
    Yasc_App::view()->layout()->disable();
    
    // Get the mysql resource from this app configuration option.
    // 
    // $mysql = Yasc_App::config()->getOption("db");
    // 
    // ... do some sql operation.
    
    echo "Hello world!";
}

/**
 * @POST("/")
 * 
 * You can route the same url to another function using a different request
 * method.
 */
function save_index() {
    Yasc_App::view()->layout()->disable();
    
    echo "post: ";
    var_dump($_POST);
}

/**
 * @GET("/bar")
 */
function bar() {    
    // You can add variables to the view object and get his value on
    // the view script using the variable $this, like: $this->var.
    Yasc_App::view()->var = "I\'m a view variable!";

    // Render a view script, a view script is a .phtml file where you can mix
    // php and html, the V in the MVC model, in this example the view files
    // are stored in views/ folder.
    // 
    // This view calls a view helper 'Bar', so check views/helpers/Bar.php 
    // to see what it does.
    return Yasc_App::view()->render("bar");
}

/**
 * @GET("/bar/:var")
 * @POST("/baz") // Ignored, yasc only uses the first annotation found.
 * 
 * Named params, you can access those via Yasc_App::params() method.
 * 
 * Matches: /bar/baz and /bar/bar
 */
function bar_single_param() {
    Yasc_App::view()->layout()->disable();
    
    echo "var: " . Yasc_App::params("var");
    Yasc_App::view()->var = "I\'m a view variable!";
    
    // instance of a model.
    $foo = new Model_Foo();
    Yasc_App::view()->do_something = $foo->doSomething();
    
    // Render a view without the layout.
    return Yasc_App::view()->render("bar");
}

/**
 * @GET("/bar/:var/id/:id")
 */
function bar_multiple_params() {
    Yasc_App::view()->layout()->disable();
    
    echo "var: " . Yasc_App::params("var");
    echo "id: " . Yasc_App::params("id");
}

/**
 * @GET("/foo")
 */
function foo() {
    // Render view script foo, this view script calls the view helper class 'Foo',
    // this view helper render a view helper script inside and return his content
    // to this view, a view helper script is just another .phtml file, if you don't
    // want to create a whole html string inside the helper ;).
    return Yasc_App::view()->render("foo");
}

/**
 * @GET("^/regex/id/(\d+)/name/([a-z]+)")
 * 
 * You can create patterns using a regular expression, this kind of route must
 * begin with a '^'. Check limonade docs and code if you want more information, or
 * just use limonade framework :), it's a more robust framework.
 * 
 * http://www.limonade-php.net/README.htm
 * https://github.com/sofadesign/limonade/blob/master/lib/limonade.php
 * 
 * Matches: /regex/id/26/name/juan
 */
function regex() {
    Yasc_App::view()->layout()->disable();
    
    echo "params: ";
    var_dump(Yasc_App::params());
}

/**
 * @GET("/say/*\/to\/*")
 * 
 * Patterns may also include wildcard parameters. Each value is associted 
 * through numeric indexes, in the same order as in the pattern.
 * 
 * Matches: /say/hello/to/world
 */
function single_asterisk() {
    Yasc_App::view()->layout()->disable();
    
    echo "params: ";
    var_dump(Yasc_App::params());
    echo "hello: ";
    var_dump(Yasc_App::params(0)); // hello
    echo "world: ";
    var_dump(Yasc_App::params(1)); // world
}

/**
 * @GET("/download/*.*")
 * 
 * Matches: /download/file.xml
 */
function download() {
    Yasc_App::view()->layout()->disable();
    
    echo "params: ";
    var_dump(Yasc_App::params());
    echo "filename: ";
    var_dump(Yasc_App::params(0)); // file
    echo "ext: ";
    var_dump(Yasc_App::params(1)); // xml
}

/**
 * @GET("/writing/*\/to\/**")
 * 
 * The double wildcard '**' matches a string like this one: my/friend/juan/badass,
 * this string is treated as pairs, this way: param1/value1/param2/value2 etc, like
 * Zend Framework does, so, via Yasc_App::params() method you can get those 
 * values using each key.
 * 
 * Matches: /writing/hello/to/my/friends/from/limonade/you_guys/roxor
 */
function pairs() {
    Yasc_App::view()->layout()->disable();
    
    echo "params: ";
    var_dump(Yasc_App::params());
    echo "single wildcard: ";
    var_dump(Yasc_App::params(0)); // hello
    echo "param my: ";
    var_dump(Yasc_App::params("my")); // friend
    echo "param from: ";
    var_dump(Yasc_App::params("from")); // limonade
    echo "param you_guys: ";
    var_dump(Yasc_App::params("you_guys")); // roxor
}

/**
 * @GET("/update")
 */
function update() {
    /* @var $flash Yasc_Function_Helper_Flash */
    $flash = Yasc_App::functionHelper("flash");
    Yasc_App::view()->flash = $flash;
    
    return Yasc_App::view()->render("update");
    
    // Use '_method' parameter in POST requests when PUT or DELETE methods 
    // are not supported.
    
    /*
    <form id="put" name="put" action="<?php echo $this->url(array("uri" => "/update")) ?>" method="post">
        <p>First name: <input type="text" name="first_name" /></p>
        <p>Last name: <input type="text" name="last_name" /></p>
        <p><input type="submit" value="Update" /></p>
        <input type="hidden" name="_method" value="PUT" id="_method" />
    </form>
    */
}

/**
 * @PUT("/update")
 */
function save_update() {
    Yasc_App::view()->layout()->disable();
    
    // $mysql = Yasc_App::config()->getOption("db");
    // $mysql->update("table1", array("first_name" => $_POST["first_name"], "last_name" => $_POST["last_name"]));
    
    /* @var $flash Yasc_Function_Helper_Flash */
    $flash = Yasc_App::functionHelper("flash");
    $flash->message("Done.");
    
    return header("Location: " . Yasc_App::viewHelper("url")->url(array("uri" => "/update")));
}

/**
 * @DELETE("/delete")
 */
function destroy() {
    Yasc_App::view()->layout()->disable();
    
    // $mysql = Yasc_App::config()->getOption("db");
    // $mysql->delete("table1", "id = {$_POST["id"]}");
    
    return header("Location: " . Yasc_App::viewHelper("url")->url(array("uri" => "/update")));
}
