<?php

/**
 * Tales.
 *
 * @author jfalvarez
 */
class Tales extends Yasc_View_Helper_AbstractHelper {
    public function tales() {
        return "I'm a view helper!, class: " . __CLASS__ . " -- " . __METHOD__;
    }
}
