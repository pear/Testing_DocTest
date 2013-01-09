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
 * Required unconditionally.
 */
require_once 'Testing/DocTest/Registry.php';
require_once 'Testing/DocTest/Exception.php';
require_once 'Testing/DocTest/Finder/Default.php';
require_once 'Testing/DocTest/Reporter/Default.php';
require_once 'Testing/DocTest/Parser/Default.php';
require_once 'Testing/DocTest/Runner/Default.php';

/**
 * DocTest.
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
class Testing_DocTest
{
    // Flags constants {{{

    /**
     * Tell the runner to ignore *all* whitespace differences when comparing
     * expected and actual results.
     */
    const FLAG_NORMALIZE_WHITESPACE = 0x01;

    /**
     * Tell the runner to compare expected and actual results in case
     * insensitive mode.
     */
    const FLAG_CASE_INSENSITIVE = 0x02;

    /**
     * Tell the parser to skip the test.
     */
    const FLAG_SKIP = 0x04;

    /**
     * Allow to pass a wildcard pattern: [...] that will match any string in
     * the actual result.
     */
    const FLAG_ELLIPSIS = 0x08;

    // }}}
    // __construct() {{{

    /**
     * Constructor.
     *
     * The $options array can have the following elements
     * - quiet: tells the reporter to turn on quiet mode, only errors will 
     *   be printed out (the default value is false);
     * - no_colors: tells the reporter to not use colors when outputting 
     *   results (the default value is false);
     * - logfile: tells the reporter to write the results in a logfile 
     *   instead of STDOUT;
     *
     * @param array $options an optional array of options
     *
     * @access public
     * @return void
     */
    public function __construct(array $options=array()) 
    {
        $reg = Testing_DocTest_Registry::singleton();
        // set user options
        foreach ($options as $name => $value) {
            $reg->$name = $value;
        }
        // set default workers
        $reg->finder   = new Testing_DocTest_Finder_Default();
        $reg->parser   = new Testing_DocTest_Parser_Default();
        $reg->runner   = new Testing_DocTest_Runner_Default();
        $reg->reporter = new Testing_DocTest_Reporter_Default();

        $reg->parser->setShellOptions($options);
    }

    // }}}
    // accept() {{{

    /**
     * Method to allow DocTest to accept a custom finder, reporter, parser or 
     * runner instance.
     *
     * <code>
     * class MyRunner implements Testing_DocTest_RunnerInterface {
     *     function run(Testing_DocTest_TestCase $tb) {
     *         // do something here...
     *     }
     * }
     *
     * try {
     *     $goodRunner = new MyRunner();
     *     $badRunner  = new stdclass();
     *     $doctest    = new Testing_DocTest();
     *     $doctest->accept($goodRunner);
     *     echo "Ok !\n";
     *     $doctest->accept($badRunner);
     * } catch (Testing_DocTest_Exception $exc) {
     *     echo "Error !\n";
     * }
     * // expects:
     * // Ok !
     * // Error !
     *
     * </code>
     *
     * @param mixed $instance an instance implementing the finder, reporter, 
     *                        parser or runner interface.
     *
     * @access public
     * @return void
     * @throws Testing_DocTest_Exception if wrong argument passed
     */
    public function accept($instance) 
    {
        $reg = Testing_DocTest_Registry::singleton();
        if ($instance instanceof Testing_DocTest_FinderInterface) {
            $reg->finder = $instance;
        } else if ($instance instanceof Testing_DocTest_ReporterInterface) {
            $reg->reporter = $instance;
        } else if ($instance instanceof Testing_DocTest_ParserInterface) {
            $reg->parser = $instance;
        } else if ($instance instanceof Testing_DocTest_RunnerInterface) {
            $reg->runner = $instance;
        } else {
            throw new Testing_DocTest_Exception('argument 1 of '
                . 'DocTest::accept must implement the finder, reporter, '
                . 'parser or runner interface.');
        }
    }

    // }}}
    // run() {{{

    /**
     * Run the tests contained in the given pathes.
     *
     * @param array $pathes an array of files and/or directories
     *
     * @access public
     * @return void
     */
    public function run(array $pathes)
    {
        $reg    = Testing_DocTest_Registry::singleton();
        $suites = $reg->parser->parse($reg->finder->find($pathes));
        $reg->reporter->onBegin($suites);
        foreach ($suites as $suite) {
            $reg->reporter->onTestSuiteBegin($suite);
            foreach ($suite as $case) {
                $reg->reporter->onTestCaseBegin($case);
                if (!isset($reg->tests) || in_array($case->name, $reg->tests)) {
                    $reg->runner->run($case);
                } else {
                    $case->state = Testing_DocTest_TestCase::STATE_SKIPPED;
                }
                switch ($case->state) {
                case Testing_DocTest_TestCase::STATE_PASSED:
                    $reg->reporter->onTestCasePass($case);
                    break;
                case Testing_DocTest_TestCase::STATE_SKIPPED:
                    $reg->reporter->onTestCaseSkip($case);
                    break;
                case Testing_DocTest_TestCase::STATE_FAILED:
                    $reg->reporter->onTestCaseFail($case);
                    break;
                case Testing_DocTest_TestCase::STATE_ERROR:
                    $reg->reporter->onTestCaseError($case);
                    break;
                }
                $reg->reporter->onTestCaseEnd($case);
            }
            $reg->reporter->onTestSuiteEnd($suite);
        }
        return $reg->reporter->onEnd($suites);
    }

    // }}}
}
