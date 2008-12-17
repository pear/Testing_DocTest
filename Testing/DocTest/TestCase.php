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
 * This class represents a <code></code> block that contains the doc test.
 *
 * <code>
 * $tb        = new Testing_DocTest_TestCase();
 * $tb->level = 'function';
 * $tb->name  = 'someFunction';
 * echo $tb->name . "\n";
 * $tb->altname = 'Alt name';
 * echo $tb->name;
 * // expects:
 * // function someFunction
 * // Alt name
 * </code>
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
class Testing_DocTest_TestCase
{
    // State constants {{{

    /**
     * State of the test before it is run.
     */
    const STATE_NOT_RUN = 0;

    /**
     * State of a test that have been skipped.
     */
    const STATE_SKIPPED = 1;

    /**
     * State of a test that passed.
     */
    const STATE_PASSED = 2;

    /**
     * State of a test that failed.
     */
    const STATE_FAILED = 3;

    /**
     * State of a test that had a php error.
     */
    const STATE_ERROR = 4;

    // }}}
    // Properties {{{

    /**
     * The registry items array.
     *
     * @var array $_properties
     * @access private
     */
    private $_properties = array(
        'name'          => null,
        'altname'       => null,
        'state'         => self::STATE_NOT_RUN,
        'level'         => null,
        'suite'         => null,
        'docComment'    => null,
        'code'          => null,
        'flags'         => 0,
        'skipIfCode'    => null,
        'cleanCode'     => null,
        'expectedValue' => null,
        'actualValue'   => null,
    );

    /**
     * Array of ini settings to pass to the process of the testcase.
     *
     * @var array $iniSettings
     * @access public
     */
    public $iniSettings = array(
        'output_handler'       => '',
        'output_buffering'     => '0',
        'safe_mode'            => '0',
        'display_errors'       => '1',
        'error_prepend_string' => '',
        'error_append_string'  => '',
        'auto_prepend_file'    => '',
        'auto_append_file'     => '',
    );

    // }}}
    // __toString() {{{

    /**
     * String representation of the test.
     *
     * @return string
     * @access public
     */
    public function __toString()
    {
        return $this->name;
    }

    // }}}
    // __set() {{{

    /**
     * Overloaded setter.
     *
     * @param string $name  name of property
     * @param mixed  $value value of property
     *
     * @return void
     * @access public
     */
    public function __set($name, $value)
    {
        $this->_properties[$name] = $value;
    }

    // }}}
    // __get() {{{

    /**
     * Overloaded getter.
     *
     * @param string $name name of property
     *
     * @return mixed
     * @access public
     */
    public function __get($name)
    {
        if (isset($this->_properties[$name])) {
            if ($name == 'name') {
                if ($this->altname !== null) {
                    return $this->altname;
                } else {
                    return $this->level . ' ' . $this->_properties['name'];
                }
            }
            return $this->_properties[$name];
        }
        return null;
    }

    // }}}
    // hasFlag() {{{

    /**
     * Return true if the test has the flag $flag set or not.
     *
     * <code>
     * require_once "Testing/DocTest.php";
     * $test = new Testing_DocTest_TestCase();
     * $test->flags  = Testing_DocTest::FLAG_ELLIPSIS 
     *               | Testing_DocTest::FLAG_NORMALIZE_WHITESPACE 
     *               | Testing_DocTest::FLAG_CASE_INSENSITIVE;
     * $test->flags &= ~Testing_DocTest::FLAG_CASE_INSENSITIVE;
     * var_dump($test->flags);
     * var_dump($test->hasFlag(Testing_DocTest::FLAG_ELLIPSIS));
     * var_dump($test->hasFlag(Testing_DocTest::FLAG_NORMALIZE_WHITESPACE));
     * var_dump($test->hasFlag(Testing_DocTest::FLAG_CASE_INSENSITIVE));
     * // expects:
     * // int(9)
     * // bool(true)
     * // bool(true)
     * // bool(false)
     * </code>
     *
     * @param int $flag one of the DOCTEST::FLAG_* constants.
     *
     * @return boolean
     * @access public
     */
    public function hasFlag($flag)
    {
        return ($this->flags & $flag) === $flag;
    }

    // }}}
}
