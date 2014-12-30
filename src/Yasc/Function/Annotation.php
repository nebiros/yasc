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
 * @subpackage Yasc_Function
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 * Class to handle annotations.
 *
 * @package Yasc
 * @subpackage Yasc_Function
 * @copyright Copyright (c) 2010 - 2014 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_Function_Annotation {
    /**
     * GET annotation regex.
     */
    const GET = "/(@GET\((.*)\))/i";

    /**
     * POST annotation regex.
     */
    const POST = "/(@POST\((.*)\))/i";
    
    /**
     * PUT annotation regex.
     */
    const PUT = "/(@PUT\((.*)\))/i";
    
    /**
     * DELETE annotation regex.
     */
    const DELETE = "/(@DELETE\((.*)\))/i";

    const ANNOTATION = 1;
    const PATTERN = 2;

    /**
     * Annotation string.
     *
     * @var string
     */
    protected $_string = null;

    /**
     * Annotation pattern.
     * 
     * @var string
     */
    protected $_pattern = null;
    
    /**
     *
     * @var bool
     */
    protected $_hasAnnotation = false;

    /**
     *
     * @param Yasc_Function $function 
     */
    public function  __construct(Yasc_Function $function) {
        $this->_match($function);
    }

    /**
     *
     * @return string
     */
    public function getString() {
        return $this->_string;
    }

    /**
     *
     * @return string
     */
    public function getPattern() {
        return $this->_pattern;
    }
    
    /**
     *
     * @return bool
     */
    public function hasAnnotation() {
        return $this->_hasAnnotation;
    }

    /**
     * Match function annotation.
     *
     * @param Yasc_Function $function
     * @return bool
     */
    protected function _match(Yasc_Function $function) {
        if (preg_match(self::GET, $function->getDocComment(), $matches)) {
            $function->setMethod(Yasc_Router::METHOD_GET);
        } else if (preg_match(self::POST, $function->getDocComment(), $matches)) {
            $function->setMethod(Yasc_Router::METHOD_POST);
        } else if (preg_match(self::PUT, $function->getDocComment(), $matches)) {
            $function->setMethod(Yasc_Router::METHOD_PUT);
        } else if (preg_match(self::DELETE, $function->getDocComment(), $matches)) {
            $function->setMethod(Yasc_Router::METHOD_DELETE);
        } else {
            return false;
        }
        
        $this->_hasAnnotation = true;
        $this->_string = trim($matches[self::ANNOTATION]);
        $this->_pattern = preg_replace("/'|\"/", "", trim($matches[self::PATTERN]));
        return true;
    }

    public function  __toString() {
        return $this->_string;
    }
}
