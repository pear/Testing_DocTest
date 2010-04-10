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
 * DocTest Reporter interface.
 * All reporters must implement this interface.
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
interface Testing_DocTest_ReporterInterface
{
    // onBegin() {{{

    /**
     * Called when a doctest session begins.
     *
     * @param array $suites an array of Testing_DocTest_TestSuite instances
     *
     * @access public
     * @return void
     */
    public function onBegin(array $suites);

    // }}}
    // onTestSuiteBegin() {{{

    /**
     * Called before the runner starts running a suite.
     *
     * @param object $suite an instance of Testing_DocTest_TestSuite
     *
     * @access public
     * @return void
     */
    public function onTestSuiteBegin(Testing_DocTest_TestSuite $suite);

    // }}}
    // onTestCaseBegin() {{{

    /**
     * Called before the runner run a test case.
     *
     * @param object $case a test case instance
     *
     * @access public
     * @return void
     */
    public function onTestCaseBegin(Testing_DocTest_TestCase $case);

    // }}}
    // onTestCasePass() {{{

    /**
     * Called when a test passed.
     *
     * @param object $case a test case instance
     *
     * @access public
     * @return void
     */
    public function onTestCasePass(Testing_DocTest_TestCase $case);

    // }}}
    // onTestCaseSkip() {{{

    /**
     * Called when a test was skipped by the runner.
     *
     * @param object $case a test case instance
     *
     * @access public
     * @return void
     */
    public function onTestCaseSkip(Testing_DocTest_TestCase $case);

    // }}}
    // onTestCaseFail() {{{

    /**
     * Called when a test failed.
     *
     * @param object $case a test case instance
     *
     * @access public
     * @return void
     */
    public function onTestCaseFail(Testing_DocTest_TestCase $case);

    // }}}
    // onTestCaseError() {{{

    /**
     * Called when a test has errors.
     *
     * @param object $case a test case instance
     *
     * @access public
     * @return void
     */
    public function onTestCaseError(Testing_DocTest_TestCase $case);

    // }}}
    // onTestCaseEnd() {{{

    /**
     * Called when the runner finished a test case.
     *
     * @param object $case a test case instance
     *
     * @access public
     * @return void
     */
    public function onTestCaseEnd(Testing_DocTest_TestCase $case);

    // }}}
    // onTestSuiteEnd() {{{

    /**
     * Called when the runner finished running the suite.
     *
     * @param object $suite an instance of Testing_DocTest_TestSuite
     *
     * @access public
     * @return void
     */
    public function onTestSuiteEnd(Testing_DocTest_TestSuite $suite);

    // }}}
    // onEnd() {{{

    /**
     * Called at the end of the DocTest session.
     *
     * @param array $suites an array of Testing_DocTest_TestSuite instances
     *
     * @access public
     * @return void
     */
    public function onEnd(array $suites);

    // }}}
}
