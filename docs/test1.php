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
 * <code>
 * <?php
 * echo "Foo!";
 * // expects:
 * // Foo!
 * ?>
 * </code>
 *
 * @category  Testing 
 * @package   Testing_DocTest
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @copyright 2008 David JEAN LOUIS
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Testing_DocTest
 * @since     Class available since release 0.1.0
 * @filesource
 */

/**
 * A class that does nothing.
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
class Bar
{
}

/**
 * This is a file level test.
 *
 * <code> 
 * // doctest: file-level doctest 1
 * // setup:
 * // $_ENV['OSTYPE'] = 'linux';
 * echo OS_TYPE;
 * // expects:
 * // linux
 * </code> 
 */ 
define('OS_TYPE', $_ENV['OSTYPE']);

/**
 * Foo class.
 *
 * Below, an example of class level doc test.
 *
 * <code>
 * // we can name our doctest explicitely, if a name is not provided it 
 * // defaults to "class ClassName" (here "class Foo")
 *
 * // doctest: my test for class Foo
 * $foo1 = new Foo();
 * $foo1->attr1 = 'value1';
 * $foo1->attr2 = 'value2';
 * echo $foo1 . "\n";
 * $foo1->attr1 = null;
 * echo $foo1;
 *
 * // expects:
 * // value1_value2
 * // value2
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
class Foo extends Bar
{
    // Foo properties {{{

    /**
     * Properties doc blocs do not accept doc tests.
     *
     * @var string $attr1
     * @access public
     */
    var $attr1 = null;

    /**
     * Properties doc blocs do not accept doc tests.
     *
     * @var string $attr2
     * @access public
     */
    var $attr2 = null;

    // }}}
    // Foo::__construct() {{{

    /**
     * Constructor.
     *
     * @param array $params an optional array of parameters
     *
     * @access public
     */
    public function __construct(array $params=array()) 
    {
        if (isset($params['attr1'])) {
            $this->attr1 = $params['attr1'];
        }
        if (isset($params['attr2'])) {
            $this->attr2 = $params['attr2'];
        }
    }

    // }}}
    // Foo::__toString() {{{

    /**
     * toString method.
     *
     * @access public
     * @return string
     */
    public function __toString() 
    {
        $ret = array();
        if (null !== $this->attr1) {
            $ret[] = $this->attr1;
        }
        if (null !== $this->attr2) {
            $ret[] = $this->attr2;
        }
        return implode('_', $ret);
    }

    // }}}
    // Foo::testString() {{{

    /**
     * <code>
     * // below we specify the name of our test, if not specified it default
     * // to "method ClassName::methodName()" (here "method Foo::testString")
     *
     * // doctest: my test for method Foo::testString
     * $foo = new Foo();
     * echo $foo->testString();
     * // expects:
     * // bar
     * </code>
     *
     * @access public
     * @return string
     */
    public function testString() 
    {
        return 'bar';
    }

    // }}}
    // Foo::testBool() {{{

    /**
     * <code>
     * $foo = new Foo();
     * var_dump($foo->testBool());
     * var_dump($foo->testBool(false));
     * // expects:
     * // bool(true)
     * // bool(false)
     * </code>
     *
     * @param bool $ret return value
     *
     * @access public
     * @return boolean
     */
    public function testBool($ret=true) 
    {
        return $ret;
    }

    // }}}
    // Foo::testInt() {{{

    /**
     * <code>
     * $foo = new Foo();
     * var_dump($foo->testInt());
     * // expects:
     * // int(7)
     * </code>
     *
     * @access public
     * @return int
     */
    public function testInt() 
    {
        return 7;
    }

    // }}}
    // Foo::testFloat() {{{

    /**
     * <code>
     * $foo = new Foo();
     * var_dump($foo->testFloat());
     * // expects: 
     * // float(12.34)
     * </code>
     *
     * @access public
     * @return float
     */
    public function testFloat() 
    {
        return 12.34;
    }

    // }}}
    // Foo::testArray() {{{

    /**
     * <code>
     * $foo = new Foo();
     * print_r($foo->testArray());
     * // expects:
     * // Array
     * // (
     * //     [foo] => foo value
     * //     [bar] => bar value
     * // )
     * </code>
     *
     * @access public
     * @return array
     */
    public function testArray() 
    {
        return array(
            'foo' => 'foo value',
            'bar' => 'bar value',
        );
    }

    // }}}
    // Foo::testObject() {{{

    /**
     * <code>
     * $foo = new Foo(array('attr1'=>'foo', 'attr2'=>'bar'));
     * echo $foo . "\n";
     * echo get_class($foo);
     * // expects:
     * // foo_bar
     * // Foo
     * </code>
     *
     * @access public
     * @return object Foo
     */
    public function testObject() 
    {
        return new Foo();
    }

    // }}}
    // Foo::testResource() {{{

    /**
     * <code>
     * $foo = new Foo();
     * var_dump(is_resource($foo->testResource()));
     * echo get_resource_type($foo->testResource());
     * // expects: 
     * // bool(true)
     * // stream
     * </code>
     *
     * @access public
     * @return ressource file
     */
    public function testResource() 
    {
        return fopen(__FILE__, 'r');
    }

    // }}}
    // Foo::testException() {{{

    /**
     * <code>
     * $foo = new Foo();
     * try {
     *     $foo->testException();
     * } catch (Exception $exc) {
     *     echo $exc->getMessage();
     * }
     * // expects:
     * // Some descriptive message
     * </code>
     *
     * @access public
     * @return Exception
     * @throws Exception
     */
    public function testException() 
    {
        throw new Exception('Some descriptive message');
    }

    // }}}
    // Foo::testNull() {{{

    /**
     * When an instruction does not return a value or return the value NULL, 
     * accepted syntaxes are:
     * // expects: null
     *
     * <code>
     * $foo = new Foo();
     * echo $foo->testNull();
     * // expects:
     * </code>
     *
     * @access public
     * @return null
     */
    public function testNull() 
    {
        return null;
    }

    // }}}
    // Foo::testError() {{{

    /**
     * <code>
     * // flags: ELLIPSIS
     * $foo = new Foo();
     * echo $foo->testError(E_USER_ERROR);
     * // expects:
     * // Fatal error: Foo ! in [...] on line [...]
     * </code>
     *
     * <code>
     * // flags: ELLIPSIS
     * // ini-set: display_errors=Off
     * $foo = new Foo(E_USER_ERROR);
     * echo $foo->testError();
     * // expects:
     * </code>
     *
     * <code>
     * // flags: ELLIPSIS
     * $foo = new Foo();
     * echo $foo->testError(E_USER_WARNING);
     * // expects:
     * // Warning: Foo ! in [...] on line [...]
     * </code>
     *
     * <code>
     * // flags: ELLIPSIS
     * $foo = new Foo();
     * echo $foo->testError(E_USER_NOTICE);
     * // expects:
     * // Notice: Foo ! in [...] on line [...]
     * </code>
     *
     * @param int $level error level
     *
     * @access public
     * @return null
     */
    public function testError($level) 
    {
        trigger_error('Foo !', $level);
    }

    // }}}
    // Foo::testBug16372pre() {{{

    /**
     * <code>
     * // flags: ELLIPSIS
     * $foo = new Foo();
     * echo $foo->testBug16372pre();
     * // expects:
     * // 2
     * </code>
     *
     * @access public
     * @return int
     */
    public function testBug16372pre() 
    {
        $x = 1;
        $y = "{$x}";
        return $x + $y;
    }

    // }}}
    // Foo::testBug16372() {{{

    /**
     * <code>
     * // flags: ELLIPSIS
     * $foo = new Foo();
     * echo $foo->testBug16372();
     * // expects:
     * // 2
     * </code>
     *
     * @access public
     * @return int
     */
    public function testBug16372() 
    {
        return 2;
    }

    // }}}
}

/**
 * This little function will explain the usage of flags in doc tests.
 * At the moment, doc tests can have the following flags:
 *
 *   - NORMALIZE_WHITESPACE: tells the runner to compare strings ignoring 
 *     all whitespace differences;
 *   - CASE_INSENSITIVE:  tells the runner to compare strings ignoring case;
 *   - SKIP: tells the parser to just ignore the test;
 *   - ELLIPSIS: allow to pass a wildcard pattern: [...] that will match 
 *     any string in the actual result.
 *
 * flags syntax:
 * // flags: FLAG_1, FLAG_2 , ... , FLAG_N
 *
 * or:
 *
 * // flags: FLAG_1
 * // FLAG_2 , FLAG_3
 * // FLAG_N
 *
 * Here are some examples:
 *
 * <code>
 * // flags: NORMALIZE_WHITESPACE
 * echo testFlags('   fo  o        ');
 * // expects:
 * // function says: foo
 * </code>
 *
 * <code>
 * // flags: CASE_INSENSITIVE
 * echo testFlags('foo');
 * // expects:
 * // FUNCtion says: Foo
 * </code>
 *
 * <code>
 * // flags: SKIP
 * echo testFlags('bar');
 * // expects:
 * // don't care too much...
 * </code>
 *
 * <code>
 * // flags: ELLIPSIS
 * echo testFlags('bar');
 * // expects:
 * // function [...]: [...]
 * </code>
 *
 * @param string $foo some string
 *
 * @return string
 */
function testFlags($foo='')
{
    return 'function says: ' . strtolower($foo);
}

/**
 * A simple function that multiply two int or float and return a float number.
 * It throws an exception if arguments given have a wrong type.
 *
 * Note that the "^M" chars have been intentionally added for tests purpose ;)
 *
 * <code>
 *
 * printf("%01.2f\n", multiply(3, 4));
 * printf("%01.2f\n", multiply(3.2, 4));
 * printf("%01.2f\n", multiply(3.2, 4.2));
 * try {
 *     multiply('foo', 4.2);
 * } catch (Exception $exc) {
 *     echo $exc->getMessage() . "\n";
 * }
 * try {
 *     multiply(3.2, 'foo');
 * } catch (Exception $exc) {
 *     echo $exc->getMessage() . "\n";
 * }
 * // expects:
 * // 12.00
 * // 12.80
 * // 13.44
 * // Wrong type for first argument.
 * // Wrong type for second argument.
 *
 * </code>
 *
 * @param mixed $a an int or a float
 * @param mixed $b an int or a float
 *
 * @return float the result of the multiplication
 * @throws Exception if arguments given have a wrong type
 */
function multiply($a, $b)
{
    // check first arg type
    if (!is_int($a) && !is_float($a)) {
        throw new Exception("Wrong type for first argument.");
    }
    // check second arg type
    if (!is_int($b) && !is_float($b)) {
        throw new Exception("Wrong type for second argument.");
    }
    return (float)($a * $b);
}

/**
 * A simple function that multiply two int or float and return a float number.
 * It throws an exception if arguments given have a wrong type.
 * 
 * This example shows the use of an external doctest file.
 *
 * <code>
 * // test-file: docs/external_file.doctest
 * </code>
 *
 * @param mixed $a an int or a float
 * @param mixed $b an int or a float
 *
 * @return float the result of the multiplication
 * @throws Exception if arguments given have a wrong type
 */
function multiply2($a, $b)
{
    // check first arg type
    if (!is_int($a) && !is_float($a)) {
        throw new Exception("Wrong type for first argument.");
    }
    // check second arg type
    if (!is_int($b) && !is_float($b)) {
        throw new Exception("Wrong type for second argument.");
    }
    return (float)($a * $b);
}

/**
 * A simple function that return a simple or multidimensional array.
 *
 * <code>
 * // flags: 
 * print_r(testArray(true));
 * // expects: 
 * // Array
 * // (
 * //     [foo] => 1
 * //     [bar] => 2
 * // )
 * </code>
 *
 * <code>
 * // note that here we must add a blank line at the end because we are using 
 * // STRICT_WHITESPACE flag.
 *
 * // flags: NORMALIZE_WHITESPACE
 * print_r(testArray());
 * // expects:
 * // Array([0]=>foo [1]=>bar)
 * </code>
 *
 * @param bool $multi return a multidimensional array if set to true.
 *
 * @return array multidimensionnal array
 */
function testArray($multi=false)
{
    return $multi ? array('foo'=>'1', 'bar'=>'2') : array('foo', 'bar');
}

/**
 * A simple function that return a string.
 *
 * <code>
 * // note that here we must set the ELLIPSIS flag cause we cannot predict 
 * // exactly the result of the function
 *
 * // flags: ELLIPSIS
 * echo testString();
 * // expects:
 * // A string that cannot be predicted [...].
 *
 * </code>
 *
 * <code>
 * // an example on how we can wrap long text
 *
 * // flags: ELLIPSIS
 * echo testString();
 * // expects:
 * // A string \
 * // that cannot \
 * // be predicted [...].
 *
 * </code>
 *
 * @return array multidimensionnal array
 */
function testString()
{
    return sprintf('A string that cannot be predicted %s.', microtime());
}

/**
 * This is another file level test.
 *
 * <code> 
 * // doctest: file-level doctest 2
 * // setup:
 * // $_REQUEST['foo'] = 'bar';
 * var_dump(defined('FOO'));
 * // expects:
 * // bool(true)
 * </code> 
 */ 
if (isset($_REQUEST['foo']) && $_REQUEST['foo'] == 'bar') {
    define('FOO', 'bar');
}


/**
 * simple test of tmplCode flag
 *
 * <code>
 * // doctest: tmpl-code doctest
 * // tmpl-code: docs/tmpl_code.doctest.php
 *
 * print $this->bar();
 *
 * // expects:
 * // I'm really private
 * </code>
 */

