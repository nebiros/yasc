YASC
====
_Yet Another Sinatra Clone_

yasc is a [sinatra](http://www.sinatrarb.com/) _(kind of)_ clone written in [php](http://en.wikipedia.org/wiki/PHP) 
and highly influenced by [zend framework](http://framework.zend.com/), is a tiny framework that uses _user defined_
functions as actions (like in the MVC pattern) and _annotations_ to route the requested url to a function.

Prerequisites
-------------

yasc requires PHP 5.2.x or later.

Simple Example
--------------

    // simple.php
    <?php

    // Include Yasc.
    require_once '../library/Yasc.php';

    /**
     * @GET( '/' )
     */
    function index( $view, $params ) {
        echo 'Hello World!';
    }

TODO
----

* Support for PUT and DELETE methods.
* Support regex in annotations.
* Add PUT and DELETE annotations.
* Caching.
* Tests.
* Improve documentation.