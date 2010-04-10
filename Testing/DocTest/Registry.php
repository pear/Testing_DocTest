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
 * A simple Registry that will allow Doctest components to share options and 
 * instances in order to achieve loose coupling.
 *
 * <code>
 *
 * Testing_DocTest_Registry::singleton()->somevar = 'foo';
 * var_dump(isset(Testing_DocTest_Registry::singleton()->somevar));
 * echo Testing_DocTest_Registry::singleton()->somevar . "\n";
 * unset(Testing_DocTest_Registry::singleton()->somevar);
 * echo Testing_DocTest_Registry::singleton()->somevar;
 *
 * // expects:
 * // bool(true)
 * // foo
 * // 
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
class Testing_DocTest_Registry
{
    // properties {{{

    /**
     * The singleton instance.
     *
     * @var object $_instance Testing_DocTest_Registry instance
     * @access private
     */
    private static $_instance = null;

    /**
     * The registry items array.
     *
     * @var array $_items
     * @access private
     */
    private $_items = array();

    // }}}
    // __construct() {{{

    /**
     * Constructor, can not be called outside this class.
     *
     * @access protected
     * @return void
     */
    protected function __construct() 
    {
    }

    // }}}
    // singleton() {{{

    /**
     * Singleton constructor.
     *
     * @return object an instance of Testing_DocTest_Registry
     * @access public
     */
    public static function singleton()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
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
        $this->_items[$name] = $value;
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
        if (isset($this->_items[$name])) {
            return $this->_items[$name];
        }
        return null;
    }

    // }}}
    // __isset() {{{
 

    /**
     * Overloaded for isset() function.
     *
     * @param string $name name of property
     *
     * @return boolean
     * @access public
     */   
    public function __isset($name)
    {
        return isset($this->_items[$name]);
    }

    // }}}
    // __unset() {{{

    /**
     * Overloaded for unset() function.
     *
     * @param string $name name of property
     *
     * @return void
     * @access public
     */   
    public function __unset($name)
    {
        unset($this->_items[$name]);
    }

    // }}}
}
