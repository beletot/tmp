<?php
/**
 * Exception subclass of PEAR_Exception for Crypt_OpenSSL.
 *
 * PHP Version 5
 *  
 * LICENSE:
 *
 * Copyright (c) 2008 Pavel Saparov, <saparov.p@gmail.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 * 
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Pavel Saparov nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRIC
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *  
 * @category   Encryption
 * @package    Crypt_OpenSSL 
 * @author     Pavel Saparov <saparov.p@gmail.com> 
 * @copyright  2008 Pavel Saparov   
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version    $Id: Exception.php,v 1.2 2008/13/06 12:14:47 saparov Exp $  
 * @link       http://pear.php.net/package/OpenSSL
 *
 */

/**
 * PEAR_Exception
 */
require_once 'PEAR/Exception.php';

/**
 * Crypt_OpenSSL_Exception
 *
 * @category   Encryption
 * @package    Crypt_OpenSSL
 * @author     Pavel Saparov <saparov.p@gmail.com>
 * @copyright  2008 Pavel Saparov 
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://pear.php.net/package/OpenSSL
 * @version    @package_version@
 * @access     public
 */
class Crypt_OpenSSL_Exception extends PEAR_Exception
{}

/**
 * Crypt_OpenSSL_Cert_Exception
 *
 * @category   Encryption
 * @package    Crypt_OpenSSL
 * @author     Pavel Saparov <saparov.p@gmail.com>
 * @copyright  2008 Pavel Saparov 
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://pear.php.net/package/OpenSSL
 * @version    @package_version@
 * @access     public
 */
class Crypt_OpenSSL_Cert_Exception extends PEAR_Exception
{}

/**
 * Crypt_OpenSSL_NewCert_Exception
 *
 * @category   Encryption
 * @package    Crypt_OpenSSL
 * @author     Pavel Saparov <saparov.p@gmail.com>
 * @copyright  2008 Pavel Saparov 
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://pear.php.net/package/OpenSSL
 * @version    @package_version@
 * @access     public
 */
class Crypt_OpenSSL_NewCert_Exception extends PEAR_Exception
{}

?>