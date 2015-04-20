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
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 * Class to handle routes.
 *
 * @package Yasc
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_Router {
    /**
     * @var Yasc_App
     */
    protected $_app = null;
	
	/**
	 * @var Yasc_Http_Request
	 */
	protected $_request = null;

    public function __construct() {
        $this->_app = Yasc_App::getInstance();
		$this->_request = $this->_app->getRequest();
    }

    /**
     * Route requested url, find script function to be invoked.
     *
     * @return Yasc_Function
     */
    public function route() {
        $route = new Yasc_Router_Route($this->_app->getFunctions());
        $requestedFunction = $route->match()->getRequestedFunction();

        return $requestedFunction;
    }
	
	/**
	 * @param string $serverName
	 * @param string $path
	 * @return string
	 */
	public function url($serverName, $path) {
		$result = $this->_request->buildUrl($serverName, $path);
		return $result["url"];
	}
		
	/**
	 * @param string $path
	 * @return string
	 */
	public function urlFor($path) {
		$result = $this->_request->buildUrl(null, $path);
		return $result["url"];
	}
}
