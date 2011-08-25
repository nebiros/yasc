<?php

// Include Yasc.
require_once '../library/Yasc.php';

/**
 * @GET( '/' )
 */
function index() {
    echo 'Hello world!';
}

/**
 * @POST( '/' )
 */
function create() {
    // save something.
}

/**
 * @PUT( '/' )
 */
function update() {
    // update something.
}

/**
 * @DELETE( '/' )
 */
function destroy() {
    // delete something.
}
