<?php

// application env.
defined("APPLICATION_ENV")
    || define("APPLICATION_ENV", (getenv("APPLICATION_ENV") ? getenv("APPLICATION_ENV") : "production"));

// composer autoload.
require_once "../vendor/autoload.php";

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\ArrayLoader;

// yasc runtime.
require_once "../vendor/nebiros/yasc/src/Yasc.php";

function configure() {
    $translator = new Translator("fr_FR", new MessageSelector());
    $translator->setFallbackLocales(array("fr"));
    $translator->addLoader("array", new ArrayLoader());
    $translator->addResource("array", array(
        "Hello World!" => "Bonjour",
    ), "fr");

    Yasc_App::config()->addOption("translator", $translator);
}

function pre_dispatch() {
    echo __FUNCTION__ . "\n\n";
}

function post_dispatch() {
    echo __FUNCTION__ . "\n\n";
}

/**
 * @GET("/")
 */
function index() {
    $translator = Yasc_App::config()->getOption("translator");
    echo "Hello world!" . "\n\n";
    echo $translator->trans("Hello World!") . "\n\n";
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
