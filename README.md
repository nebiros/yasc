YASC
====
_Yet Another Sinatra Clone_

`yasc` is a [sinatra](http://www.sinatrarb.com/) _(kind of)_ clone written in [php](http://en.wikipedia.org/wiki/PHP) 
and highly influenced by [zend framework 1.x](http://framework.zend.com/), the routing system is based on 
[limonade's](http://www.limonade-php.net/) code, is a tiny framework that uses _user defined_ functions as actions 
(like in the MVC pattern) and _annotations_ to route the requested url to a function.

Features
--------
* Functions based.
* RESTful.
* Tiny ^_^.
* Routing system based on regex patterns, named params, pairs param (like [zend framework 1.x](http://framework.zend.com/)).
* View helpers.
* Function helpers.
* Layouts support.
* Class autoloading based on the [Framework Interop Group](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md).
* Models support.

Default project structure
-------------------------
`yasc` uses this project structure:

    app.php
    views/
        helpers/
    models/

If you create these folders in your project they are auto-loaded in your application. Default *namespaces*:

    helpers/Helper_*
    models/Model_*

Accessors
-----------
I saw that invoking the requested function with a lot of arguments is too ugly and a little 
bit verbose (just a little?), so I add some *accessors* to be used in each function.

```php
<?php

Yasc_App::view() // Access the view object inside your function.
Yasc_App::params() // Get all route params.
Yasc_App::params("key") // Get param "key" value, also, you can specify a default value as the second argument of this static method.
Yasc_App::config() // App config.
Yasc_App::viewHelper("url") // Get a view helper, like Layout, Url, or some of your own.
Yasc_App::functionHelper("flash") // Get a function helper, like Flash, this helper is a stack of messages, errors, notices, etc.
```

Prerequisites
-------------

`yasc` requires PHP 5.2.4 or later.

Installation
------------

Easiest way to install `yasc` is through [composer](https://getcomposer.org), 

```bash
php composer.phar require nebiros/yasc "~0.1.18"
```

or add it as a `git submodule`.

Create a **index.php** or a **app.php** file and include `yasc`, like:

```php
namespace My\App;

// composer autoload.
require_once "vendor/autoload.php";
// yasc runtime.
require_once "vendor/nebiros/yasc/src/Yasc.php";

$yasc = new \Yasc();
$yasc->run(__NAMESPACE__);
unset($yasc);
```

Add a function and a pattern using an annotation for your application index, 
your script will be something like this:

```php
<?php
namespace My\App;

// composer autoload.
require_once "vendor/autoload.php";
// yasc runtime.
require_once "vendor/nebiros/yasc/src/Yasc.php";

$yasc = new \Yasc();
$yasc->run(__NAMESPACE__);
unset($yasc);

/**
 * @GET("/")
 */
function index() {
    echo "Hello world!";
}
```

Now, go to your favorite browser and run your script:

    http://localhost/app.php

Follow the examples and you'r done :).

Setup
-----

Maybe you want to *hide* your script file from the URL, http://app.com/app.php/some/thing (I know, pretty URLs, SEO shut, blah, blah) 
and get something fancier like: http://app.com/some/thing, ok, well, create a [VirtualHost](http://httpd.apache.org/docs/2.0/vhosts/) 
and add a [.htaccess](http://corz.org/serv/tricks/htaccess2.php) file to your application folder like this:

### Virtual host configuration

    <VirtualHost *:80>
       DocumentRoot "/path/to/your/application/public"
       ServerName app.com
       <Directory "/path/to/your/application/public">
           Options -Indexes MultiViews FollowSymLinks
           AllowOverride All 
           Order allow,deny
           Allow from all 
       </Directory>
    </VirtualHost>

### .htaccess file

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} -s [OR]
    RewriteCond %{REQUEST_FILENAME} -l [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^.*$ - [NC,L]
    RewriteRule ^.*$ index.php [NC,L]

**NOTE:** Your app file must be named index.php in order to work with this `.htaccess` file.

Simple Example
--------------

```php
<?php
namespace My\App;

// composer autoload.
require_once "vendor/autoload.php";
// yasc runtime.
require_once "vendor/nebiros/yasc/src/Yasc.php";

$yasc = new \Yasc();
$yasc->run(__NAMESPACE__);
unset($yasc);

/**
 * @GET("/")
 */
function index() {
    echo "Hello world!";
}

/**
 * @POST("/")
 */
function create() {
    // save something.
}

/**
 * @PUT("/")
 */
function update() {
    // update something.
}

/**
 * @DELETE("/")
 */
function destroy() {
    // delete something.
}
```

Configuration
-------------

```php
<?php

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
```

Pre- and Post-Dispatch Hooks
----------------------------

We provide two functions, `pre_dispatch` and `post_dispatch`, that may be called before and after an action is
invoked.

Advanced Examples
-----------------

```php
<?php

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
    
    echo "<pre>";
    echo "post: ";
    var_dump($_POST);
    echo "</pre>";
}

/**
 * @GET("/tales")
 */
function tales() {    
    // You can add variables to the view object and get his value on
    // the view script using the variable $this, like: $this->tales.
    Yasc_App::view()->tales = "oh! I'm a view variable!";

    // Render a view script, a view script is a .phtml file where you can mix
    // php and html, the V in the MVC model, in this example the view files
    // are stored in views/ folder.
    // 
    // This view calls a view helper 'Tales', so check views/helpers/Tales.php 
    // to see what it does.
    Yasc_App::view()->render("tales");
}

/**
 * @GET("/tales/:lol")
 * @POST("/woot") // Ignored, yasc only uses the first annotation found.
 * 
 * Named params, you can access those via Yasc_App::params() method.
 * 
 * Matches: /tales/foo and /tales/bar
 */
function tales1() {
    Yasc_App::view()->layout()->disable();
    
    echo "<pre>";
    echo "lol value: " . Yasc_App::params("lol");
    echo "</pre>";
    Yasc_App::view()->tales = "oh! I'm a view variable!";
    
    // instance of a model.
    $foo = new Model_Foo();
    Yasc_App::view()->helloModel = $foo->doSomething();
    
    // Render a view without the layout.
    Yasc_App::view()->render("tales");
}

/**
 * @GET("/tales/:lol/id/:id")
 */
function tales2() {
    Yasc_App::view()->layout()->disable();
    
    echo "<pre>";
    echo "lol value: " . Yasc_App::params("lol");
    echo "id value: " . Yasc_App::params("id");
    echo "</pre>";
}

/**
 * @POST("/tales3")
 */
function tales3() {
    Yasc_App::view()->layout()->disable();
    
    echo "<pre>";
    echo "post: ";
    var_dump($_POST);
    echo "</pre>";
}

/**
 * @GET("/foo")
 */
function foo() {
    // Render view script foo, this view script calls the view helper class 'Foo',
    // this view helper render a view helper script inside and return his content
    // to this view, a view helper script is just another .phtml file, if you don't
    // want to create a whole html string inside the helper ;).
    Yasc_App::view()->render("foo");
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
    
    echo "<pre>";
    echo "params: ";
    var_dump(Yasc_App::params());
    echo "</pre>";
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
    
    echo "<pre>";
    echo "params: ";
    var_dump(Yasc_App::params());
    echo "hello: ";
    var_dump(Yasc_App::params(0)); // hello
    echo "world: ";
    var_dump(Yasc_App::params(1)); // world
    echo "</pre>";    
}

/**
 * @GET("/download/*.*")
 * 
 * Matches: /download/file.xml
 */
function download() {
    Yasc_App::view()->layout()->disable();
    
    echo "<pre>";
    echo "params: ";
    var_dump(Yasc_App::params());
    echo "filename: ";
    var_dump(Yasc_App::params(0)); // file
    echo "ext: ";
    var_dump(Yasc_App::params(1)); // xml
    echo "</pre>";    
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
    
    echo "<pre>";
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
    echo "</pre>";    
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
    
    header("Location: " . Yasc_App::viewHelper("url")->url(array("uri" => "/update")));
}

/**
 * @DELETE("/delete")
 */
function destroy() {
    Yasc_App::view()->layout()->disable();
    
    // $mysql = Yasc_App::config()->getOption("db");
    // $mysql->delete("table1", "id = {$_POST["id"]}");
    
    header("Location: " . Yasc_App::viewHelper("url")->url(array("uri" => "/update")));
}
```

TODO
----

* <del>Support for PUT and DELETE methods.</del>
* <del>Support regex in annotations.</del>
* <del>Add PUT and DELETE annotations.</del>
* <del>Add layouts support.</del>
* <del>Add view helpers support.</del>
* Caching.
* Tests.
* <del>Improve documentation.</del>
