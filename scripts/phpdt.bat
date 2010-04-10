@echo off
REM This file is part of the PEAR Testing_DocTest package.
REM 
REM PHP version 5
REM 
REM LICENSE: This source file is subject to the MIT license that is available
REM through the world-wide-web at the following URI:
REM http://opensource.org/licenses/mit-license.php
REM 
REM @category  Testing 
REM @package   Testing_DocTest
REM @author    David JEAN LOUIS <izimobil@gmail.com>
REM @copyright 2008 David JEAN LOUIS
REM @license   http://opensource.org/licenses/mit-license.php MIT License 
REM @version   CVS: $Id: phpdt.bat,v 1.1 2008-12-17 16:15:04 izi Exp $
REM @link      http://pear.php.net/package/Testing_DocTest
REM @since     File available since release 0.1.0

"@php_bin@" -d auto_append_file="" -d auto_prepend_file="" -d include_path="@php_dir@" "@bin_dir@\phpdt" %*
