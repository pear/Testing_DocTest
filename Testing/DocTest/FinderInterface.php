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
 * DocTest Finder interface.
 * All finders must implement this interface.
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
interface Testing_DocTest_FinderInterface
{
    // find() {{{

    /**
     * Must return an array that have this structure:
     * <code>
     * array(
     *     '/absolute/path/to/file1.php',
     *     '/absolute/path/to/file2.php',
     * )
     * </code>
     *
     * @param array $pathes an array of files and/or directories, glob syntax 
     *                      is supported, ie. you can pass arrays like this:
     *                      array('file1.php', '{dir2,dir3}/*.php')
     *
     * @access public
     * @return array an array of files with their realpathes
     */
    public function find(array $pathes);

    // }}}
}
