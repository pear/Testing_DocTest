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
 * Resuired files
 */
require_once 'Testing/DocTest/RunnerInterface.php';

/**
 * DocTest Runner default class.
 *
 * <code>
 * require_once 'Testing/DocTest.php';
 * require_once 'Testing/DocTest/TestCase.php';
 *
 * $test = new Testing_DocTest_TestCase();
 * $test->code          = 'echo "Foobar!";';
 * $test->expectedValue = '  foobar !';
 *
 * $r = new Testing_DocTest_Runner_Default();
 * $r->run($test);
 * var_dump($test->state === Testing_DocTest_TestCase::STATE_PASSED);
 *
 * $test->flags |= Testing_DocTest::FLAG_NORMALIZE_WHITESPACE;
 * $r->run($test);
 * var_dump($test->state === Testing_DocTest_TestCase::STATE_PASSED);
 *
 * $test->flags |= Testing_DocTest::FLAG_CASE_INSENSITIVE;
 * $r->run($test);
 * var_dump($test->state === Testing_DocTest_TestCase::STATE_PASSED);
 *
 * $test->expectedValue = '  f[...]bar !';
 * $test->flags |= Testing_DocTest::FLAG_ELLIPSIS;
 * $r->run($test);
 * var_dump($test->state === Testing_DocTest_TestCase::STATE_PASSED);
 *
 * $test->flags |= Testing_DocTest::FLAG_SKIP;
 * $r->run($test);
 * var_dump($test->state === Testing_DocTest_TestCase::STATE_SKIPPED);
 *
 * // expects:
 * // bool(false)
 * // bool(false)
 * // bool(true)
 * // bool(true)
 * // bool(true)
 * </code>
 *
 * <code>
 * // flags: ELLIPSIS
 * require_once 'Testing/DocTest.php';
 * require_once 'Testing/DocTest/TestCase.php';
 *
 * $test                = new Testing_DocTest_TestCase();
 * $test->code          = 'echo nonExistantFunc();';
 * $test->expectedValue = 'foo';
 * $runner              = new Testing_DocTest_Runner_Default();
 * $runner->run($test);
 * var_dump($test->actualValue);
 * // expects:
 * // string([...]) "[...]Fatal error: Call to undefined function [...]"
 *
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
class Testing_DocTest_Runner_Default implements Testing_DocTest_RunnerInterface
{
    // run() {{{

    /**
     * Run the test provided by comparing the expected result with the actual
     * result returned by the test code.
     * Each test is run in its own php process.
     *
     * @param object $testCase Testing_DocTest_TestCase instance to run
     *
     * @access public
     * @return void
     * @throws Testing_DocTest_Exception
     */
    public function run(Testing_DocTest_TestCase $testCase)
    {
        if ($testCase->parsingError) {
            $testCase->state = Testing_DocTest_TestCase::STATE_ERROR;
            return;
        }
        $codetpl = "<?php\n%s\n";
        if ($testCase->file !== null) {
            $codetpl .= "require_once '{$testCase->file}';\n";
        }
        $codetpl .= "%s\n?>\n";

        $tmplfile="";
        if ($testCase->tmplCode) {
            $tmplfile=$testCase->tmplCode;
        } elseif (isset($testCase->_shellOptions['template_code']) 
        && $testCase->_shellOptions['template_code']
        ) {
            $tmplfile=$testCase->_shellOptions['template_code'];
        }

        if ($tmplfile) {
            $codetpl=file_get_contents($tmplfile);
            if (empty($codetpl)) {
                throw new Testing_DocTest_Exception("Invalid template: $tmplfile");
            }
        }

        // skip condition
        if (($skipCode = $testCase->skipIfCode) !== null) {
            $skipCode = trim($skipCode);
            $ret      = $this->_exec(sprintf('<?php echo %s; ?>', $skipCode));
            if ($ret['code'] ==! 0 || strlen($ret['output']) > 1) {
                throw new Testing_DocTest_Exception(
                    'skip-condition in test "'. $testCase->name . 
                    '" must be a boolean expression, '. 'got: ' . $skipCode
                );
            }
            $skip = $ret['code'] === 0 && trim($ret['output']) === '1';
        } else {
            $skip = false;
        }
        if ($skip || $testCase->hasFlag(Testing_DocTest::FLAG_SKIP)) {
            $testCase->state = Testing_DocTest_TestCase::STATE_SKIPPED;
            return;
        }
        // handle ini settings
        if (!empty($testCase->iniSettings)) {
            $options = $this->_formatIniSettings($testCase->iniSettings);
        } else {
            $options = null;
        }
        
        $ret = $this->_exec(
            sprintf($codetpl, $testCase->setupCode, $testCase->code),
            $options, $testCase
        );
        
        if ($ret['code'] !== 0) {
            $testCase->actualValue = trim($ret['output']);
        } else {
            $testCase->actualValue = $ret['output'];
        }
        if ($this->_compare($testCase)) {
            $testCase->state = Testing_DocTest_TestCase::STATE_PASSED;
        } else {
            if ($ret['code'] !== 0) {
                $testCase->state = Testing_DocTest_TestCase::STATE_ERROR;
            } else {
                $testCase->state = Testing_DocTest_TestCase::STATE_FAILED;
            }
        }
        // handle clean line
        if (($cleanCode = $testCase->cleanCode) !== null) {
            $cleanCode = trim($cleanCode);
            $ret       = $this->_exec(sprintf('<?php %s; ?>', $cleanCode));
            if ($ret['code'] ==! 0) {
                throw new Testing_DocTest_Exception(
                    'cleaning code failed in ' . 'test "' .
                    $testCase->name . '": ' . $cleanCode
                );
            }
        }

    }

    // }}}
    // _formatIniSettings() {{{

    /**
     * Given an array of directive=>value this method return the commandline
     * string to pass to the php interpreter.
     *
     * @param array $iniSettings an array of ini settings
     *
     * @access private
     * @return string
     */
    private function _formatIniSettings(array $iniSettings)
    {
        $ret = '';
        foreach ($iniSettings as $k=>$v) {
            if (!$v) {
                continue;
            }
            
            if (substr(PHP_OS, 0, 3) == 'WIN') {
                // XXX check why windows does not like escapeshellarg
                $ret .= ' -d' . $k . '=' . $v;
            } else {
                $ret .= ' -d' . escapeshellarg($k . '=' . addslashes($v));
            }
        }
        return $ret;
    }

    // }}}
    // _compare() {{{

    /**
     * Compare the expected result with the actual result.
     *
     * @param object $test a Testing_DocTest_TestCase instance
     *
     * @access private
     * @return boolean
     */
    private function _compare(Testing_DocTest_TestCase $test)
    {
        $exp = trim($test->expectedValue, "\n");
        $act = trim($test->actualValue, "\n");
        if ($test->hasFlag(Testing_DocTest::FLAG_CASE_INSENSITIVE)) {
            $exp = strtolower($exp);
            $act = strtolower($act);
        }
        if ($test->hasFlag(Testing_DocTest::FLAG_NORMALIZE_WHITESPACE)) {
            $exp = preg_replace('/\s/', '', $exp);
            $act = preg_replace('/\s/', '', $act);
        }
        if ($test->hasFlag(Testing_DocTest::FLAG_ELLIPSIS)) {
            $exp = str_replace(
                array("\n", "\r\n", '[...]'),
                array('', '', '__ellipsis__'), $exp
            );
            $act = str_replace(array("\n", "\r\n", '[...]'), '', $act);
            $rx  = preg_quote($exp, '/');
            $rx  = str_replace('__ellipsis__', '.*?', $rx);
            return (bool)preg_match('/^'.$rx.'$/', $act);
        }
        return $exp == $act;
    }

    // }}}
    // _exec() {{{

    /**
     * Run given php code in a subprocess and return an array as follows:
     *
     * <code>
     * array(
     *     'code'   => true|false, // return code of the process
     *     'output' => 'string'    // output of the process (stdout or stderr)
     * )
     * </code>
     *
     * @param string                   $code     the php code to execute
     * @param string                   $options  additionnal options to pass to php
     * @param Testing_DocTest_TestCase $testCase aa
     *
     * @access private
     * @return array
     * @throws Testing_DocTest_Exception if the process cannot be opened
     */
    private function _exec(
        $code, $options=null,
        Testing_DocTest_TestCase $testCase=null
    ) {
        if (isset($testCase, $testCase->_shellOptions['php_wrapper']) 
            && $testCase->_shellOptions['php_wrapper']
        ) {
            //Needed for general framework setup
            putenv('DOCTEST_SCRIPT='.$testCase->file);
            $php = $testCase->_shellOptions['php_wrapper'];
        } else {
            $php = substr('/usr/bin/php', 0, 1) == '@' ? 'php ' : 'php';
            if (substr(PHP_OS, 0, 3) == 'WIN') {
                $php = '"' . $php . '"';
            }
        }

        if ($options !== null) {
            $php .= ' ' . $options;
        }

        $descriptors = array(
                0 => array('pipe', 'r'), // stdin
                1 => array('pipe', 'w'), // stdout
                2 => array('pipe', 'w')  // stderr
        );
        // try to open proc and raise an exception if it fails
        $process = proc_open($php, $descriptors, $pipes);

        if (!is_resource($process)) {
            throw new Testing_DocTest_Exception("Unable to open process: $php");
        }

        // write code to stdin
        fwrite($pipes[0], $code);
        fflush($pipes[0]);
        fclose($pipes[0]);
        // will contain script output
        $output = '';

        while (true) {
            // hide errors from interrupted syscalls
            $r = $pipes;
            $e = null;
            $w = null;
            $n = @stream_select($r, $w, $e, 60);
            if ($n <= 0) {
                // timed out
                $output .= "\n ** ERROR: process timed out **\n";
                return array(proc_terminate($process), $output);
            }
            if (false === ($data = fgets($pipes[1]))) {
                // nothing on stdout, try stderr
                //if (false === ($data = fgets($pipes[2]))) {
                    break;
                    //}
            }
            $output .= $data;
        }

        // close stdout and stderr
        fflush($pipes[1]);
        fclose($pipes[1]);
        fflush($pipes[2]);
        fclose($pipes[2]);
        // get return code by closing the process
        return array('code' => proc_close($process), 'output' => $output);
    }

        // }}}
}
