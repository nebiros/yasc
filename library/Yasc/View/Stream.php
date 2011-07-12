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
 * @subpackage Yasc_View
 * @copyright Copyright (c) 2010 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @version $Id$
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 */

/**
 * Stream wrapper to convert markup of mostly-PHP templates into PHP prior to include().
 *
 * Based in large part on the example at
 * http://www.php.net/manual/en/function.stream-wrapper-register.php
 *
 * Based on the original post from Mike Naberezny (@link http://mikenaberezny.com/2006/02/19/symphony-templates-ruby-erb/)
 * and Zend Framework's Zend_View (@link http://framework.zend.com/manual/en/zend.view.html) component.
 *
 * Original authors of this script:
 * Mike Naberezny (@link http://mikenaberezny.com)
 * Paul M. Jones (@link http://paul-m-jones.com)
 *
 * @package Yasc
 * @subpackage Yasc_View
 * @copyright Copyright (c) 2010 Juan Felipe Alvarez Sadarriaga. (http://juan.im)
 * @license http://github.com/nebiros/yasc/raw/master/LICENSE New BSD License
 * @author nebiros
 */
class Yasc_View_Stream {
    /**
     * Current stream position.
     *
     * @var int
     */
    protected $_pos = 0;

    /**
     * Data for streaming.
     *
     * @var string
     */
    protected $_data;

    /**
     * Stream stats.
     *
     * @var array
     */
    protected $_stat;

    /**
     * Opens the script file and converts markup.
     */
    public function stream_open( $path, $mode, $options, &$opened_path ) {
        // get the view script source
        $path = str_replace( 'view://', '', $path );
        $this->_data = file_get_contents( $path );

        /**
         * If reading the file failed, update our local stat store
         * to reflect the real stat of the file, then return on failure
         */
        if ( false === $this->_data ) {
            $this->_stat = stat( $path );
            return false;
        }

        /**
         * Convert <?= ?> to long-form <?php echo ?> and <? ?> to <?php ?>
         *
         */
        $this->_data = preg_replace( '/\<\?\=/', '<?php echo ', $this->_data );
        $this->_data = preg_replace( '/<\?(?!xml|php)/s', '<?php ', $this->_data );

        /**
         * file_get_contents() won't update PHP's stat cache, so we grab a stat
         * of the file to prevent additional reads should the script be
         * requested again, which will make include() happy.
         */
        $this->_stat = stat( $path );

        return true;
    }

    /**
     * Reads from the stream.
     */
    public function stream_read( $count ) {
        $ret = substr( $this->_data, $this->_pos, $count );
        $this->_pos += strlen( $ret );
        return $ret;
    }

    /**
     * Tells the current position in the stream.
     */
    public function stream_tell() {
        return $this->_pos;
    }

    /**
     * Tells if we are at the end of the stream.
     */
    public function stream_eof() {
        return $this->_pos >= strlen( $this->_data );
    }

    /**
     * Stream statistics.
     */
    public function stream_stat() {
        return $this->_stat;
    }

    /**
     * Seek to a specific point in the stream.
     */
    public function stream_seek( $offset, $whence ) {
        switch ( $whence ) {
            case SEEK_SET:
                if ( $offset < strlen( $this->_data ) && $offset >= 0 ) {
                    $this->_pos = $offset;
                    return true;
                } else {
                    return false;
                }

                break;

            case SEEK_CUR:
                if ( $offset >= 0 ) {
                    $this->_pos += $offset;
                    return true;
                } else {
                    return false;
                }

                break;

            case SEEK_END:
                if ( strlen( $this->_data ) + $offset >= 0 ) {
                    $this->_pos = strlen( $this->_data ) + $offset;
                    return true;
                } else {
                    return false;
                }

                break;

            default:
                return false;
        }
    }

    /**
     * Retrieve information about a file.
     */
    public function url_stat() {
        return $this->_stat;
    }
}
