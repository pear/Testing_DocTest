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
 * Required file.
 */
require_once 'Testing/DocTest/ParserInterface.php';
require_once 'Testing/DocTest/TestSuite.php';
require_once 'Testing/DocTest/TestCase.php';

/**
 * DocTest Parser default class.
 * Important note: this class will be refactored soon so do not rely on it yet 
 * if you want to subclass or customize Testing_DocTest.
 *
 * @category  Testing
 * @package   Testing_DocTest
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @copyright 2008 David JEAN LOUIS
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   Release: 0.3.1
 * @link      http://pear.php.net/package/Testing_DocTest
 * @since     Class available since release 0.1.0
 */
class Testing_DocTest_Parser_Default implements Testing_DocTest_ParserInterface
{
    // doctest syntax prefix {{{

    /**
     * Doctest syntax prefix default is a standard php inline comment: '//'
     */
    const SYNTAX_PREFIX = '//';

    // }}}
    // Keywords constants {{{

    /**
     * Keyword for the name of the external doctest file
     */
    const KW_DOCTEST_FILE = 'test-file';
    
    /**
     * Keyword for the name of the doctest
     */
    const KW_DOCTEST_NAME = 'doctest';

    /**
     * Keyword for the doctest flags
     */
    const KW_DOCTEST_FLAGS = 'flags';

    /**
     * Keyword for the skip condition
     */
    const KW_DOCTEST_SKIP_IF = 'skip-if';

    /**
     * Keyword for the ini settings
     */
    const KW_DOCTEST_INI_SET = 'ini-set';

    /**
     * Keyword for the tmpl code settings
     */
    const KW_DOCTEST_TMPL_CODE = 'tmpl-code';


    /**
     * Keyword for the doctest expected result
     */
    const KW_DOCTEST_EXPECTS = 'expects';

    /**
     * Keyword for the doctest expected file
     */
    const KW_DOCTEST_EXPECTS_FILE = 'expects-file';

    /**
     * Keyword for the clean part
     */
    const KW_DOCTEST_CLEAN = 'clean';

    /**
     * Keyword for the setup part
     */
    const KW_DOCTEST_SETUP = 'setup';

    // }}}
    // State constants {{{

    /**
     * State after parsing a doctest line.
     */
    const STATE_DOCTEST = 1;

    /**
     * State after parsing flags.
     */
    const STATE_FLAGS = 2;

    /**
     * State after parsing a skip condition line.
     */
    const STATE_SKIP_IF = 3;

    /**
     * State after parsing a ini-set line.
     */
    const STATE_INI_SET = 4;

    /**
     * State after parsing expects line.
     */
    const STATE_EXPECTS = 5;

    /**
     * State after parsing expects-file line.
     */
    const STATE_EXPECTS_FILE = 6;

    /**
     * State after parsing code line.
     */
    const STATE_CODE = 7;

    /**
     * State after parsing clean line.
     */
    const STATE_CLEAN = 8;

    /**
     * State after parsing setup line.
     */
    const STATE_SETUP = 9;

    /**
     * State after parsing tmpl code.
     */
    const STATE_TMPL_CODE = 10;



    // }}}
    // Properties {{{

    /**
     * Current state of the parser, null or one of the STATE_*  constants.
     *
     * @var int $_state
     * @access private
     */
    private $_state = null;

    /**
     * Testing_DocTest_TestCase instance.
     *
     * @var object $_testCase
     * @access private
     */
    private $_testCase = null;


    // }}}
    // setShellOptions() {{{

    /**
     * set command line options
     *
     * @param array $options an array of command line options
     *
     * @access public
     * @return array
     */
    public function setShellOptions(array $options)
    {
        $this->_shellOptions = $options;
    }

    // }}}
    // parse() {{{

    /**
     * Parse the files passed and return an array of Testing_DocTest_TestSuite 
     * instances.
     *
     * @param array $files an array of file pathes
     *
     * @access public
     * @return array
     */
    public function parse(array $files)
    {
        $ret = array();
        $kw  = preg_quote(self::KW_DOCTEST_NAME, '/')    . '|'
             . preg_quote(self::KW_DOCTEST_FLAGS, '/')   . '|'
             . preg_quote(self::KW_DOCTEST_SKIP_IF, '/') . '|'
             . preg_quote(self::KW_DOCTEST_INI_SET, '/') . '|'
             . preg_quote(self::KW_DOCTEST_SETUP, '/')   . '|'
             . preg_quote(self::KW_DOCTEST_TMPL_CODE, '/') . '|'
             . preg_quote(self::KW_DOCTEST_CLEAN, '/')   . '|'
             . preg_quote(self::KW_DOCTEST_EXPECTS, '/') . '|'
             . preg_quote(self::KW_DOCTEST_EXPECTS_FILE, '/');
        foreach ($files as $file) {
            $testCaseArray = $this->_parseFile($file);
            $suite         = false;
            foreach ($testCaseArray as $testCaseData) {
                // split raw code into lines
                $docblocs = $this->_extractCodeBlocs($testCaseData['docComment']);
                // build our suite
                if (!empty($docblocs) && false == $suite) {
                    $suite       = new Testing_DocTest_TestSuite();
                    $suite->name = $file; 
                }
                foreach ($docblocs as $docbloc) {
                    $this->_testCase        = new Testing_DocTest_TestCase();
                    $this->_testCase->_shellOptions = $this->_shellOptions;
                    $this->_testCase->file  = $file;
                    $this->_testCase->level = $testCaseData['level'];
                    $this->_testCase->name  = $testCaseData['name'];
                    // split string into an array of lines
                    $lines = preg_split('/(\n|\r\n)/', $docbloc);
		            try {
	                    foreach ($lines as $i=>$l) {
	                        // remove spaces and * at the beginning
	                        $l = preg_replace('/^\s*\*\s?/', '', $l);
	                        $p = preg_quote(self::SYNTAX_PREFIX, '/');
	                        if (preg_match("/^\s*$p\s?($kw):\s*(.*)$/", $l, $m)) {
				    			//First doctest line number
	                    	    $this->_testCase->lineNumber = $testCaseData['line'];
	
	                            switch ($m[1]) {
	                            case self::KW_DOCTEST_NAME:
	                                $this->_handleDoctestLine($m[2]);
	                                break;
	                            case self::KW_DOCTEST_FLAGS:
	                                $this->_handleFlagsLine($m[2]);
	                                break;
	                            case self::KW_DOCTEST_SKIP_IF:
	                                $this->_handleFlagsLine($m[2]);
	                                break;
	                            case self::KW_DOCTEST_INI_SET:
	                                $this->_handleIniSetLine($m[2]);
	                                break;
	                            case self::KW_DOCTEST_EXPECTS:
	                                $this->_handleExpectsLine($m[2]);
	                                break;
	                            case self::KW_DOCTEST_EXPECTS_FILE:
	                                $this->_handleExpectsFileLine($m[2]);
	                                break;
	                            case self::KW_DOCTEST_CLEAN:
	                                $this->_handleCleanLine($m[2]);
	                                break;
	                            case self::KW_DOCTEST_SETUP:
	                                $this->_handleSetupLine($m[2]);
	                                break;
				    			case self::KW_DOCTEST_TMPL_CODE: 
	                                $this->_handleTmplCode($m[2]);
									break;
	                            }
	                        } else if (preg_match('/^\s*'.$p.'\s?(.*)$/', $l, $m)) {
	                            $this->_handleLineContinuation($m[1]);
	                        } else {
	                            if (trim($l) != '') {
	                                $this->_handleCodeLine($l);
	                            }
	                        }
	                    }
	                
	 	  	    	}catch( Exception $e ){
	                        $this->_testCase->parsingError = $e->getMessage();
		  	    	}
                    // trim last eol
                    $this->_testCase->expectedValue  
                    	= substr($this->_testCase->expectedValue, 0, -1);
                    // reset state
                    $this->_state = null;
                    // append the test case
                    $suite->addTestCase($this->_testCase);
                }
            }
            if ($suite) {
                $ret[] = $suite;
            }


        }
        return $ret;
    }

    // }}}
    // _parseFile() {{{

    /**
     * Parse the file $file and return an array of Testing_DocTest_TestCase 
     * instances.
     *
     * @param string $file path to the file to parse.
     *
     * @access private
     * @return array
     */
    private function _parseFile($file)
    {
        $return = array();
        $tokens = $this->_tokenize($file);
        if (false === $tokens) {
            // return an empty array
            return $return;
        }
        $curlyLevel  = -1;
        $curlyOpen   = -1;
        $className   = null;
        $insideQuote = false;
        $insideClass = false;
        while (false !== ($item = each($tokens))) {
            // memoize curly level in order to detect if we are inside a class
            if (is_string($item['value'])) {
                if (!$insideQuote) {
                    if ($item['value'] == '{') {
                        $curlyLevel++;
                    } else if ($item['value'] == '}' 
                    	&& --$curlyLevel == $curlyOpen
                    ) {
                        // curly is the close curly of current class
                        $insideClass = false;
                    }
                }
                if ($item['value'] == '"') {
                    $insideQuote = !$insideQuote;
                }
                continue;
            }
            list($id, $token, $line) = $item['value'];
            // skip all tokens but doc comments
            if ($id !== T_DOC_COMMENT) {
                continue;
            }
            // find next token
            $ids  = array(T_CLASS, T_FUNCTION, T_DOC_COMMENT);
            $next = $this->_findNextToken($ids, $tokens);
            /*
            if (false === $next && !empty($return)) {
                break;
            }
            */
            // build Testing_DocTest_TestCase instance
            $ret               = array();
            $ret['docComment'] = $token;
            $ret['line']       = $line;
            $ret['file']       = $file;
            if (false === $next || T_DOC_COMMENT === $next[0]) {
                $ret['name']  = 'test';
                $ret['level'] = 'file level';
            } else {
                $nToken = $this->_findNextToken(T_STRING, $tokens);
                if (false === $nToken) {
                    continue;
                }
                if ($next[0] === T_CLASS) {
                    $insideClass  = true;
                    $curlyOpen    = $curlyLevel;
                    $ret['name']  = $nToken[1];
                    $className    = $nToken[1];
                    $ret['level'] = 'class';
                } else if ($insideClass) {
                    $ret['name']  = $className . '::' . $nToken[1];
                    $ret['level'] = 'method';
                } else {
                    $ret['name']  = $nToken[1];
                    $ret['level'] = 'function';
                }
            }
            $return[] = $ret;
        }
        return $return;
    }

    // }}}
    // _tokenize() {{{

    /**
     * Tokenize the file $file into an array of tokens using the builtin php 
     * tokenizer extension. Before tokenizing the method check that the file
     * contains at least a doctest.
     *
     * @param string $file the file to parse.
     *
     * @access private
     * @return array array of tokens
     */
    private function _tokenize($file)
    { 
        $data = file_get_contents($file);
        // speed improvement, don't bother tokenizing file if it does not 
        // contain any doctest
        if (false === strstr($data, self::KW_DOCTEST_EXPECTS)) {
            return array();
        }
        return token_get_all($data);
    }

    // }}}
    // _findNextToken() {{{

    /**
     * Find the next token matching the id $id and return it or return false if 
     * no matching token is found.
     *
     * @param mixed $id      id or array of ids the token must match
     * @param array &$tokens tokens array passed by reference
     *
     * @access private
     * @return array array of tokens
     */
    private function _findNextToken($id, &$tokens)
    { 
        $next = current($tokens);
        while ($next !== false) {
            if (!is_string($next)) {
                if (is_int($id) && $next[0] === $id) {
                    return $next;
                }
                if (is_array($id) && in_array($next[0], $id)) {
                    return $next;
                }
            }
            // move to next token
            $next = next($tokens);
        }
        return false;
    }

    // }}}
    // _extractCodeBlocs() {{{

    /**
     * Extract all <code></code> blocs in the given raw docstring.
     *
     * @param string $docstring raw docstring
     *
     * @access private
     * @return array an array of code blocs strings.
     */
    private function _extractCodeBlocs($docstring)
    {
        $ret = array();
        // extract <code></code> blocks, we use preg_match_all because there 
        // could be more than one code block by docstring
        $rx = '/<code>[\s\*]*(<[\?\%](php)?)?\s*' 
            . '(.*?)\s*([\?\%]>)?[\s\*]*<\/code>/si';
        preg_match_all($rx, $docstring, $tokens);
        if (isset($tokens[3]) && is_array($tokens[3])) {
            foreach ($tokens[3] as $i => $token) {
                if ($this->_hasStandaloneDoctest($token)) {
                    $testfile_contents = $this->_handleStandaloneDoctest($token);
                    if ($testfile_contents !== false) {
                        // replace the current doctest code with the contents
                        // of the external included file
                        $token = $testfile_contents;
                    }
                }
                if (!$this->_hasDocTest($token)) {
                    // not a doctest
                    continue;
                }
                $ret[] = $token;
            }
        }
        return $ret;
    }

    // }}}
    // _hasStandaloneDoctest() {{{

    /**
     * Return true if the string data provided contains an external doctest file.
     *
     * @param string $data The docstring data
     *
     * @return boolean
     */
    private function _hasStandaloneDoctest($data)
    {
        $p = preg_quote(self::SYNTAX_PREFIX, '/');
        $k = preg_quote(self::KW_DOCTEST_FILE, '/');
        return preg_match("/$p\s?$k/m", $data);
    }

    // }}}
    // _hasDocTest() {{{

    /**
     * Return true if the string data provided contains a doctest.
     *
     * @param string $data string data
     *
     * @access private
     * @return boolean 
     */
    private function _hasDocTest($data)
    {
        $p = preg_quote(self::SYNTAX_PREFIX, '/');
        $k = preg_quote(self::KW_DOCTEST_EXPECTS, '/');
        return preg_match("/$p\s?$k/m", $data);
    }

    // }}}
    // _handleStandaloneDoctest() {{{

    /**
     * Return the contents of the external doctest file.
     *
     * @param string $docbloc The docstring data
     *
     * @return mixed boolean or string
     */
    private function _handleStandaloneDoctest($docbloc)
    {
        $p     = preg_quote(self::SYNTAX_PREFIX, '/');
        $k     = preg_quote(self::KW_DOCTEST_FILE, '/');
        $lines = preg_split('/(\n|\r\n)/', $docbloc);

        foreach ($lines as $i => $l) {
            $l = preg_replace('/^\s*\*\s?/', '', $l);
            $p = preg_quote(self::SYNTAX_PREFIX, '/');
            if (preg_match("/^\s*$p\s?($k):\s*(.*)$/", $l, $matches)) {
                $f = trim($matches[2]);
                if (false === ($contents = @file_get_contents(realpath($f)))) {
                    throw new Testing_DocTest_Exception(
                        "Unable to read standalone doctest file \"$f\""
                    );
                }
                // remove the php tags
                $rx = '/(<[\?\%](php)?)?(.*?)([\?\%]>)?/si';
                return preg_replace($rx, '\3', $contents);
            }
        }
        return false;
            
    }

    // }}}
    // _handleDoctestLine() {{{

    /**
     * Parse the doctest line provided.
     *
     * @param string $line the line of code to parse
     *
     * @access private
     * @return void
     * @throws Testing_DocTest_Exception
     */
    private function _handleDoctestLine($line)
    {
        $states = array(null, self::STATE_FLAGS, self::STATE_DOCTEST,
            self::STATE_SKIP_IF, self::STATE_INI_SET, self::STATE_TMPL_CODE);
        if (!in_array($this->_state, $states)) {
            throw new Testing_DocTest_Exception("Unexpected doctest line: $line");
        }
        $this->_testCase->altname .= $line;
        $this->_state              = self::STATE_DOCTEST;
    }

    // }}}
    // _handleFlagsLine() {{{

    /**
     * Parse the flag line provided.
     *
     * @param string $line The flag line to parse
     *
     * @access private
     * @return void
     * @throws Testing_DocTest_Exception
     */
    private function _handleFlagsLine($line)
    {
        $states = array(null, self::STATE_FLAGS, self::STATE_DOCTEST,
            self::STATE_SKIP_IF, self::STATE_INI_SET, self::STATE_TMPL_CODE);
        if (!in_array($this->_state, $states)) {
            throw new Testing_DocTest_Exception("Unexpected flags line: $line");
        }
        $flags = explode(',', $line);
        foreach ($flags as $flag) {
            $const = 'Testing_DocTest::FLAG_' . strtoupper(trim($flag));
            if (defined($const)) {
                $this->_testCase->flags |= constant($const);
            }
        }
        $this->_state = self::STATE_FLAGS;
    }

    // }}}
    // _handleExpectsLine() {{{

    /**
     * Parse the expects line provided.
     *
     * @param string $line the expects line to parse
     *
     * @access private
     * @return void
     * @throws Testing_DocTest_Exception
     */
    private function _handleExpectsLine($line)
    {
        $states = array(self::STATE_CODE, self::STATE_EXPECTS,
            self::STATE_SETUP);
        if (!in_array($this->_state, $states)) {
            throw new Exception("unexpected expects line: $line");
        }
        $this->_testCase->expectedValue .= $line;
        // handle line continuation
        if (substr(trim($line), -1) !== '\\') {
            $this->_testCase->expectedValue .= "\n";
        } else {
            $this->_testCase->expectedValue 
            	= trim($this->_testCase->expectedValue, '\\');
        }
        $this->_state = self::STATE_EXPECTS;
    }

    // }}}
    // _handleExpectsFileLine() {{{

    /**
     * Parse the expects-file line provided.
     *
     * @param string $line the expects-file line to parse
     *
     * @access private
     * @return void
     * @throws Testing_DocTest_Exception
     */
    private function _handleExpectsFileLine($line)
    {
        $states = array(self::STATE_CODE, self::STATE_EXPECTS_FILE,
            self::STATE_SETUP);
        if (!in_array($this->_state, $states)) {
            throw new Exception("unexpected expects-file line: $line");
        }
        $f = realpath(trim($line));
        if (false === ($contents = @file_get_contents($f))) {
            throw new Testing_DocTest_Exception("Unable to read expects file $f");
        }
        $this->_testCase->expectedValue = $contents;
        $this->_state                   = self::STATE_EXPECTS_FILE;
    }

    // }}}
    // _handleCodeLine() {{{

    /**
     * Parse the code line provided.
     *
     * @param string $line the code line to parse
     *
     * @access private
     * @return void
     * @throws Testing_DocTest_Exception
     */
    private function _handleCodeLine($line)
    {
        $states = array(self::STATE_EXPECTS, self::STATE_EXPECTS_FILE,
            self::STATE_CLEAN);
        if (in_array($this->_state, $states)) {
            throw new Testing_DocTest_Exception("Unexpected code line: $line");
        }
        $this->_testCase->code .= rtrim($line) . "\n";
        $this->_state           = self::STATE_CODE;
    }

    // }}}
    // _handleSkipIfLine() {{{

    /**
     * Parse the skip-if line provided.
     *
     * @param string $line the skip-if line to parse
     *
     * @access private
     * @return void
     * @throws Testing_DocTest_Exception
     */
    private function _handleSkipIfLine($line)
    {
        $states = array(null, self::STATE_FLAGS, self::STATE_DOCTEST,
            self::STATE_SKIP_IF, self::STATE_INI_SET, self::STATE_TMPL_CODE);
        if (!in_array($this->_state, $states)) {
            throw new Testing_DocTest_Exception("Unexpected skip-if line: $line");
        }
        $this->_testCase->skipIfCode .= rtrim($line) . "\n";
        $this->_state                 = self::STATE_SKIP_IF;
    }

    // }}}
    // _handleIniSetLine() {{{

    /**
     * Parse the ini-set line provided.
     *
     * @param string $line the ini-set line to parse
     *
     * @access private
     * @return void
     * @throws Testing_DocTest_Exception
     */
    private function _handleIniSetLine($line)
    {
        $states = array(null, self::STATE_FLAGS, self::STATE_DOCTEST,
            self::STATE_SKIP_IF, self::STATE_INI_SET, self::STATE_TMPL_CODE);
        if (!in_array($this->_state, $states)) {
            throw new Testing_DocTest_Exception("Unexpected ini-set line: $line");
        }
        $a = explode('=', trim($line));
        if (count($a) != 2) {
            throw new Testing_DocTest_Exception("Malformed ini-set line: $line");
        }
        $this->_testCase->iniSettings[$a[0]] = $a[1];
        $this->_state                        = self::STATE_INI_SET;
    }

    // }}}
    // _handleTmplCode() {{{

    /**
     * Parse the tmpl-code line provided.
     *
     * @param string $line the ini-set line to parse
     *
     * @access private
     * @return void
     * @throws Testing_DocTest_Exception
     */
    private function _handleTmplCode($line)
    {
        $states = array(null, self::STATE_FLAGS, self::STATE_DOCTEST,
            self::STATE_SKIP_IF, self::STATE_INI_SET, self::STATE_TMPL_CODE);
        if (!in_array($this->_state, $states)) {
            throw new Testing_DocTest_Exception("Unexpected tmpl-code line: $line");
        }
        $tmplfile = trim($line);
        if (!file_exists($tmplfile)) {
            throw new Testing_DocTest_Exception(
            	"Malformed tmpl-code line: 
            	$line (File not exists)"
            );
        }
        $this->_testCase->tmplCode=$tmplfile;
        $this->_state                        = self::STATE_TMPL_CODE;
    }

    // }}}
    // _handleCleanLine() {{{

    /**
     * Parse the clean line provided.
     *
     * @param string $line the clean line to parse
     *
     * @access private
     * @return void
     * @throws Testing_DocTest_Exception
     */
    private function _handleCleanLine($line)
    {
        $states = array(self::STATE_EXPECTS, self::STATE_EXPECTS_FILE,
            self::STATE_CLEAN);
        if (!in_array($this->_state, $states)) {
            throw new Testing_DocTest_Exception("Unexpected clean line: $line");
        }
        $this->_testCase->cleanCode .= rtrim($line) . "\n";
        $this->_state                = self::STATE_CLEAN;
    }

    // }}}
    // _handleSetupLine() {{{

    /**
     * Parse the setup line provided.
     *
     * @param string $line the setup line to parse
     *
     * @access private
     * @return void
     * @throws Testing_DocTest_Exception
     */
    private function _handleSetupLine($line)
    {
        $states = array(self::STATE_CODE, self::STATE_EXPECTS,
            self::STATE_EXPECTS_FILE, self::STATE_CLEAN);
        if (in_array($this->_state, $states)) {
            throw new Testing_DocTest_Exception("Unexpected setup line: $line");
        }
        $this->_testCase->setupCode .= rtrim($line) . "\n";
        $this->_state                = self::STATE_SETUP;
    }

    // }}}
    // _handleLineContinuation() {{{

    /**
     * Parse a line continuation.
     *
     * @param string $line the line to parse
     *
     * @access private
     * @return void
     */
    private function _handleLineContinuation($line)
    {
        switch ($this->_state) {
        case self::STATE_EXPECTS:
            $this->_handleExpectsLine($line);
            break;
        case self::STATE_FLAGS:
            $this->_handleFlagsLine($line);
            break;
        case self::STATE_DOCTEST:
            $this->_handleDoctestLine($line);
            break;
        case self::STATE_SKIP_IF:
            $this->_handleSkipIfLine($line);
            break;
        case self::STATE_INI_SET:
            $this->_handleIniSetLine($line);
            break;
        case self::STATE_CLEAN:
            $this->_handleCleanLine($line);
            break;
        case self::STATE_SETUP:
            $this->_handleSetupLine($line);
	    	break;
 		case self::STATE_TMPL_CODE;
            $this->_handleTmplCode($line);
            break;
        }
    }

    // }}}
}
