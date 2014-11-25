<?php

/**
 * Yasc.
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/nebiros/yasc/raw/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mail@jfalvarez.com so we can send you a copy immediately.
 *
 * @category Yasc
 * @package Yasc_Function
 * @subpackage Yasc_Function_Helper
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 * Render message as JSON, JavaScript or XML.
 *
 * @package Yasc_Function
 * @subpackage Yasc_Function_Helper
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_Function_Helper_Renderer {
    public function __construct() {}

    /**
     * 
     * @return void
     */
    public function setCommonHeaders() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
    }

    /**
     * 
     * @param string $type
     * @return void
     */
    public function setContentTypeHeaderFor($type) {
        switch ($type) {
            case "json": header("Content-type: application/json"); break;
            case "js": header("Content-type: application/javascript"); break;
        }
    }

    /**
     * 
     * @param string $type "json", "js", "xml"
     * @param mixed $message
     * @param Array $options (bool) "set_common_headers", (bool) "echo"
     * @return string
     */
    public function render($type, $message = "", Array $options = null) {
        if (!isset($options["set_common_headers"])) {
            $options["set_common_headers"] = true;
        }

        if (!isset($options["echo"])) {
            $options["echo"] = true;
        }

        if ((bool) $options["set_common_headers"]) $this->setCommonHeaders();

        $this->setContentTypeHeaderFor($type);

        $return = "";

        switch ($type) {
            case "json":        
                $return = json_encode($message, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_UNESCAPED_UNICODE);
                break;

            case "js":
                break;

            case "xml":
                if (is_array($message)) {
                    $return = Yasc_Util::arrayToXml($message);
                } else if (is_string($message)) {
                    $return = $message;
                }
                break;
            
            default:
                break;
        }

        if ((bool) $options["echo"]) Yasc_App::view()->layout()->disable(); echo $return; exit();

        return $return;
    }
}
