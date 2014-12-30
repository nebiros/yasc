<?php

// composer autoload.
require_once "../vendor/autoload.php";
// yasc runtime.
require_once "../vendor/nebiros/yasc/src/Yasc.php";

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
