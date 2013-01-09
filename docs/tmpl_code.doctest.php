<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of the PEAR Testing_DocTest package.
 * 
 * PHP version 5
 * 
 * @category  Testing 
 * @package   Testing_DocTest
 * @author    Tobia Caneschi <tobia.caneschi@gmail.com>
 * @copyright 2008 Appsfuel
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Testing_DocTest
 * @since     Class available since release 0.1.0
 * @filesource
 */
//Example of general framework bootstrap

//framework setup phase
define('TEXT_VAR', "I'm really private");

//Needed for phpdt setup flag 
%s

/**
 * A tess framework .
 *
 * @category  Testing
 * @package   Testing_DocTest
 * @author    Tobia Caneschi <tobia.caneschi@gmail.com>
 * @copyright 2013 xxx
 * @license   xx http://license.com
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Testing_DocTest
 * @since     xx
 */
class Foo
{
    /**
     * Properties doc blocs do not accept doc tests.
     *
     * @var string $_privateVar
     * @access private
     */
    private $_privateVar = TEXT_VAR;
   
    /**
     * Run method.
     *
     * @access public
     * @return Nothing
     */
    public function run() 
    {
        //doctest code 
        %s
    }

    /**
     * bar method.
     *
     * @access public
     * @return Nothing
     */
    public function bar() 
    {
        return $this->_privateVar;
    }
}

//run phase
$fm1 = new Foo();
$fm1->run();
?>
