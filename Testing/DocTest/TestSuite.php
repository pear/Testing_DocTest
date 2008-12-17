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
 * This class is a container for a "suite" of test cases.
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
class Testing_DocTest_TestSuite implements IteratorAggregate, Countable
{
    // Properties {{{

    /**
     * The registry items array.
     *
     * @var array $_properties
     * @access private
     */
    private $_properties = array('name' => null);

    /**
     * Array of sections.
     *
     * @var array $_sections
     * @access private
     */
    private $_testCases = array();

    // }}}
    // addTestCase() {{{

    /**
     * Add a test case to the suite.
     *
     * @param object $case a Testing_DocTest_TestCase instance.
     *
     * @return void
     * @access public
     */
    public function addTestCase(Testing_DocTest_TestCase $case)
    {
        $case->suite        = $this;
        $this->_testCases[] = $case;
    }

    // }}}
    // __toString() {{{

    /**
     * String representation of the suite.
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
            return $this->_properties[$name];
        }
        return null;
    }

    // }}}
    // IteratorAggregate interface implementation {{{

    /**
     * Part of IteratorAggregate interface implementation.
     *
     * @return mixed
     * @access public
     */
    public function current()
    {
        return $this->_cases[$this->key()];
    }

    /**
     * Part of IteratorAggregate interface implementation.
     *
     * @return mixed
     * @access public
     */ 
    public function key()
    {
        return key($this->_testCases);
    }

    /**
     * Part of IteratorAggregate interface implementation.
     *
     * @return mixed
     * @access public
     */ 
    public function next()
    {
        next($this->_testCases);
    }

    /**
     * Part of IteratorAggregate interface implementation.
     *
     * @return void
     * @access public
     */
    public function rewind()
    {
        reset($this->_testCases);
    }

    /**
     * Part of IteratorAggregate interface implementation.
     *
     * @return boolean
     * @access public
     */
    public function valid()
    {
        return current($this->_testCases) !== false;
    }

    /**
     * Part of IteratorAggregate interface implementation.
     *
     * @return object instance of ArrayObject
     * @access public
     */
    public function getIterator()
    {
        return new ArrayObject($this->_testCases);
    }

    // }}}
    // Countable interface implementation {{{

    /**
     * Part of Countable interface implementation.
     *
     * @return object instance of ArrayObject
     * @access public
     */
    public function count()
    {
        return count($this->_testCases);
    }

    // }}}
}
