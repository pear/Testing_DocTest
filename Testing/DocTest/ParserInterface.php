<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of the PEAR Testing_DocTest package.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to the MIT license that is available
 * through the world-wide-web at the following URI:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Testing 
 * @package   Testing_DocTest
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @copyright 2008 David JEAN LOUIS
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Testing_DocTest
 * @since     File available since release 0.1.0
 * @filesource
 */

/**
 * DocTest Parser interface.
 * All parsers must implement this interface.
 *
 * @category  Testing
 * @package   Testing_DocTest
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @copyright 2008 David JEAN LOUIS
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Testing_DocTest
 * @since     Class available since release 0.1.0
 */
interface Testing_DocTest_ParserInterface
{
    // parse() {{{

    /**
     * Parse the files passed and return an array as follows:
     *
     * <code>
     * array(
     *     'file1' => array($testCase1, $testCase2),
     *     'file2' => array($testCase1, $testCase2),
     * )
     * </code>
     *
     * @param array $files an array of file pathes
     *
     * @access public
     * @return array
     * @throws Testing_DocTest_Exception
     */
    public function parse(array $files);

    // }}}
}
