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
 * Include interface.
 */
require_once 'Testing/DocTest/FinderInterface.php';

/**
 * DocTest Finder.
 * The finder's job is to find all files given an array of pathes that can 
 * contains shell wildcards.
 *
 * <code>
 * // flags: ELLIPSIS
 * $finder = new Testing_DocTest_Finder_Default();
 * $base   = is_dir('@php_dir@') ? '@php_dir@/Testing' : 'Testing';
 * $files  = $finder->find(array(
 *     $base . '/DocTest/Exception.php',
 *     $base . '/D*.php'
 * ));
 * print_r($files);
 * 
 * // expects:
 * // Array
 * // (
 * //     [0] => [...]Exception.php
 * //     [1] => [...]DocTest.php
 * // )
 * </code>
 *
 * @category  Testing
 * @package   Testing_DocTest
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @copyright 2008 David JEAN LOUIS
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Testing_DocTest
 * @see       Testing_DocTest_FinderInterface
 * @since     Class available since release 0.1.0
 */
class Testing_DocTest_Finder_Default implements Testing_DocTest_FinderInterface
{
    // find() {{{

    /**
     * Find all files matching the given files and/or directories and return an
     * array of file pathes (pathes are absolute, using the realpath function).
     *
     * @param array $pathes an array of files and/or directories, glob syntax 
     *                      is supported, ie. you can pass arrays like this:
     *                      array('file1.php', '{dir2,dir3}/*.php')
     *
     * @access public
     * @return array an array of files with their realpathes
     */
    public function find(array $pathes)
    {
        $files   = array();
        $pattern = '*.{php,php5,inc,class}';
        foreach ($pathes as $path) {
            $flag    = defined('GLOB_BRACE') ? GLOB_BRACE : null;
            $matches = $this->_recursiveGlob($path, $pattern, $flag);
            if (is_array($matches)) {
                foreach ($matches as $file) {
                    $file = realpath($file);
                    if (!in_array($file, $files)) {
                        $files[] = $file;
                    }
                }
            }
        }
        return $files;
    }

    // }}}
    // _recursiveGlob() {{{

    /**
     * Recursive version of glob
     *
     * @param string $path    Directory to start with.
     * @param string $pattern Pattern to glob for.
     * @param int    $flags   Flags sent to glob.
     *
     * @access private
     * @return array containing all pattern-matched files.
     */
    private function _recursiveGlob($path, $pattern='*', $flags=null)
    {
        if (!is_dir($path)) {
            return glob($path, $flags);
        }
        // Get the list of all matching files currently in the directory.
        $files = glob($path . DIRECTORY_SEPARATOR . $pattern, $flags);
        // Then get a list of all directories in this directory, and
        // run ourselves on the resulting array.
        foreach (glob($path.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR) as $sdir) {
            $sfiles = $this->_recursiveGlob($sdir, $pattern, $flags);
            $files  = array_merge($files, $sfiles);
        }
        return $files;
    }
    
    // }}}
}
