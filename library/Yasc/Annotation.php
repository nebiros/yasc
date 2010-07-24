<?php

/**
 * Class to handle annotations.
 *
 * @author jfalvarez
 */
class Yasc_Annotation {
    /**
     * GET annotation regex.
     */
    const GET = '/(@GET\((.*)\))/i';

    /**
     * POST annotation regex.
     */
    const POST = '/(@POST\((.*)\))/i';

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

    public function  __construct( Yasc_Function $function ) {
        $this->_match( $function );
    }

    public function getString() {
        return $this->_string;
    }

    public function getPattern() {
        return $this->_pattern;
    }

    /**
     * Get function annotations.
     *
     * @param string $file
     * @return Yasc_Annotation_Parser
     */
    protected function _match( Yasc_Function $function ) {
        if ( preg_match( self::GET, $function->getDocComment(), $out ) ) {
            $this->_string = trim( $out[self::ANNOTATION] );
            $this->_pattern = preg_replace( '/\'|"/', '', trim( $out[self::PATTERN] ) );
            $function->setMethod( 'get' );
        } else if ( preg_match( self::POST, $function->getDocComment(), $out ) ) {
            $this->_string = trim( $out[self::ANNOTATION] );
            $this->_pattern = preg_replace( '/\'|"/', '', trim( $out[self::PATTERN] ) );
            $function->setMethod( 'post' );
        }

        return $this;
    }

    public function  __toString() {
        return $this->_string;
    }
}
