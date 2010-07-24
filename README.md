YASC
====
_Yet Another Sinatra Clone_

yasc is a [sinatra](http://www.sinatrarb.com/) _(kind of)_ clone, is a tiny framework
that uses php _user defined_ functions as actions (like in the MVC pattern) and
_annotations_ to route the requested url to a function.

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
        echo '<h1>Hello world!</h1>';
    }

TODO
----

* Support for PUT and DELETE methods.
* Support regex in annotations.
* Add PUT and DELETE annotations.
* Add layouts support.
* Added view helpers support.
* Improve documentation.