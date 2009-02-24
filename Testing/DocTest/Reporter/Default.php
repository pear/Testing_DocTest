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
 * Required file
 */
require_once 'Testing/DocTest/ReporterInterface.php';

/**
 * DocTest Reporter default class.
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
class Testing_DocTest_Reporter_Default implements Testing_DocTest_ReporterInterface
{
    // properties {{{

    /**
     * Timer beginning time.
     *
     * @var float time
     * @access private
     */
    private $_time = 0;

    /**
     * Opened log file.
     *
     * @var resource $_logfile
     * @access private
     */
    private $_logfile = false;

    // }}}
    // __construct() {{{

    /**
     * Constructor.
     *
     * @access public
     */
    public function __construct()
    {
        $reg = Testing_DocTest_Registry::singleton();
        if ($reg->logfile) {
            $this->_logfile = fopen($reg->logfile, 'wb');
        }
        $time        = explode(' ', microtime());
        $this->_time = $time[1] + $time[0];
    }

    // }}}
    // __destruct() {{{

    /**
     * Destructor.
     * Will eventually close the opened logfile.
     *
     * @access public
     */
    public function __destruct()
    {
        if (false !== $this->_logfile && is_resource($this->_logfile)) {
            fclose($this->_logfile);
        }
    }

    // }}}
    // onBegin() {{{

    /**
     * Called when a doctest session begins.
     *
     * @param array $suites an array of Testing_DocTest_TestSuite instances
     *
     * @access public
     * @return void
     */
    public function onBegin(array $suites)
    {
        if (empty($suites)) {
            $this->_output("Nothing to process.\n");
        }
    }

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
    public function onTestSuiteBegin(Testing_DocTest_TestSuite $suite)
    {
        $this->_output("Processing {$suite->name}", '36');
        $this->_output("\n");
    }

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
    public function onTestCaseBegin(Testing_DocTest_TestCase $case)
    {
    }

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
    public function onTestCasePass(Testing_DocTest_TestCase $case)
    {
        $this->_output('[PASS]  ', 32);
        $this->_output("{$case->name}\n");
    }

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
    public function onTestCaseSkip(Testing_DocTest_TestCase $case)
    {
        $this->_output('[SKIP]  ', 33);
        $this->_output("{$case->name}\n");
    }

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
    public function onTestCaseFail(Testing_DocTest_TestCase $case)
    {
        $this->_output('[FAIL]  ', 31, true);
        $this->_output("{$case->name}\n", true);
        $this->_output(str_pad(" Expected ", 72, "=", STR_PAD_BOTH), 42, true);
        $this->_output("\n" . trim($case->expectedValue) . "\n", false, true);
        $this->_output(str_pad(" Actual ", 72, "=", STR_PAD_BOTH), 41, true);
        $this->_output("\n" . trim($case->actualValue) . "\n", false, true);
        $this->_output("\n", false, true);
    }

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
    public function onTestCaseError(Testing_DocTest_TestCase $case)
    {
        $this->_output('[ERROR] ', 31, true);
        $this->_output("{$case->name} in file \"{$case->suite->name}\"\n", true);
        $bar = str_repeat('-', 31);
        $this->_output(str_pad(" Error ", 72, "=", STR_PAD_BOTH), 41, true);
        $this->_output("\n" . trim($case->actualValue) . "\n", false, true);
    }

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
    public function onTestCaseEnd(Testing_DocTest_TestCase $case)
    {
    }

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
    public function onTestSuiteEnd(Testing_DocTest_TestSuite $suite)
    {
        $this->_output("\n");
    }

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
    public function onEnd(array $suites)
    {
        if (empty($suites)) {
            return;
        }
        $time   = explode(' ', microtime());
        $t      = ($time[1] + $time[0]) - $this->_time;
        $passed = $skipped = $failed = $error = 0;
        foreach ($suites as $suite) {
            foreach ($suite as $tc) {
                if ($tc->state == Testing_DocTest_TestCase::STATE_SKIPPED) {
                    $skipped++;
                } else if (
                    $tc->state == Testing_DocTest_TestCase::STATE_PASSED) {
                    $passed++;
                } else {
                    $failed++;
                }
            }
        }
        $this->_output(sprintf("\nTotal time    : %.4f sec.\n", $t), 36);
        if ($passed > 0) {
            $this->_output("Passed tests  : $passed\n", 32);
        } else {
            $this->_output("Passed tests  : $passed\n");
        }
        if ($skipped > 0) {
            $this->_output("Skipped tests : $skipped\n", 33);
        } else {
            $this->_output("Skipped tests : $skipped\n");
        }
        if ($failed > 0) {
            $this->_output("Failed tests  : $failed\n\n", 31);
        } else {
            $this->_output("Failed tests  : $failed\n\n");
        }
    }

    // }}}
    // _output() {{{

    /**
     * Writes the message $msg to STDOUT or to the logfile.
     *
     * @param string $msg   the message to output
     * @param int    $color the color code (optional)
     * @param bool   $force force output even if quiet mode
     *
     * @return void
     * @access private
     */
    private function _output($msg, $color=false, $force=false)
    {
        $reg = Testing_DocTest_Registry::singleton();
        if ($reg->quiet && !$force) {
            return;
        }
        if ($color && !$reg->no_colors && !$this->_logfile) {
            echo "\033[{$color}m" . $msg . "\033[0;0m";
        } else {
            if ($this->_logfile) {
                fwrite($this->_logfile, $msg);
            } else {
                echo $msg;
            }
        }
    }

    // }}}
}
