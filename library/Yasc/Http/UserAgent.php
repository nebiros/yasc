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
 * @package Yasc
 * @subpackage Yasc_Http
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 *
 * @package Yasc
 * @subpackage Yasc_Http
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_Http_UserAgent {
    const BB_TYPE = 0;
    const BB_PROFILE = 1;
    const BB_CONF = 2;
    const BB_VENDOR = 3;

    const ANDROID_BROWSER = 1;
    const ANDROID_TYPE = 2;
    const ANDROID_TYPE_OS = 1;
    const ANDROID_TYPE_VERSION = 2;
    const ANDROID_TYPE_TYPE = 4;
    const ANDROID_WEBKIT = 3;

    /**
     *
     * @var string
     */
    protected $_agent = null;

    /**
     *
     * @var string
     */
    protected $_type = null;

    /**
     *
     * @var string
     */
    protected $_version = null;

    /**
     * 
     */
    public function __construct() {
        $this->_agent = (true === isset($_SERVER["HTTP_USER_AGENT"])) ? strtolower($_SERVER["HTTP_USER_AGENT"]) : null;

        if (null === $this->_agent) {
            throw new Yasc_Http_Exception("User agent is empty ._.");
        }
    }

    /**
     *
     * @return string|null
     */
    public function getAgent() {
        return $this->_agent;
    }

    /**
     *
     * @return string|null
     */
    public function getType() {
        return $this->_type;
    }

    /**
     *
     * @return string|null
     */
    public function getVersion() {
        return $this->_version;
    }

    /**
     *
     * @return array
     */
    public function getProperties() {
        return array(
            "agent" => $this->_agent,
            "type" => $this->_type,
            "version" => $this->_version,
        );
    }
    
    /**
     *
     * @return bool
     */
    public function isMobile() {
        // @see http://detectmobilebrowser.com/
        if (preg_match("/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm(os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i",$useragent)||preg_match("/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp(i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac(|\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt(|\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg(g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i",substr($this->_agent,0,4))) {
            return true;
        }
        
        return false;        
    }

    /**
     * 
     * @return bool
     */
    public function isBlackBerry() {
        if (false === stripos($this->_agent, "blackberry")) {
            return false;
        }

        $parts = explode(" ", $this->_agent);
        $bb = $parts[self::BB_TYPE];
        list($name, $this->_version) = explode("/", $bb);
        $this->_type = preg_replace("/[^\d+]/", "", $name);

        return true;
    }

    /**
     * 
     * @return bool
     */
    public function isAndroid() {
        if (false === stripos($this->_agent, "android")) {
            return false;
        }

        if (false === preg_match("/(.+) \((.+)\) (.+) \((.+)\) (.+) (.+)/", $this->_agent, $matches)) {
            return false;
        }

        $parts = explode("; ", $matches[self::ANDROID_TYPE]);

        $this->_type = $parts[self::ANDROID_TYPE_TYPE];
        list($name, $this->_version) = explode(" ", $parts[self::ANDROID_TYPE_VERSION]);

        return true;
    }

    /**
     *
     * @return bool
     */
    public function isSmartphone() {
        if (false !== stripos($this->_agent, "android")) {
            return true;
        }

        if (false !== stripos($this->_agent, "iphone")) {
            return true;
        }

        if (false !== stripos($this->_agent, "ipod")) {
            return true;
        }

        return false;
    }
}
