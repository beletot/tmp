<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This class represents a new X509 certificate
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
 * @version    $Id: Cert.php,v 1.2 2008/13/06 22:56:34 saparov Exp $  
 * @link       http://pear.php.net/package/OpenSSL
 *
 */
 
/**
 * Require Crypt_OpenSSL_Cert to extend a new Crypt_OpenSSL_NewCert class.
 */
require_once 'Cert.php';

/**
 * Extended class for a new certificate
 *
 * @category   Encryption
 * @package    Crypt_OpenSSL_NewCert
 * @author     Pavel Saparov <saparov.p@gmail.com>
 * @copyright  2008 Pavel Saparov
 * @access     public
 */
class Crypt_OpenSSL_NewCert extends Crypt_OpenSSL_Cert
{
    /**
     * A certificate request resource.
     *
     * @access protected
     * @var resource
     */
    protected $_crtRequestResource;
    
    /**
     * How many days should be the new certificate valid.    
     *         
     * @access public    
     * @var array
     */     
    
    public $validDays;
    
    /**
     * A new certificate default information.
     *         
     * @access public    
     * @var array
     */   
    public $certConfig = array(
                             /**
                              * countryName -            Country Name (2 letter code) - eg. CZ
                              */                                                           
                             "countryName" => "CZ",
                             /**
                              * stateOrProvinceName -    State or Province Name (full name) - eg. Czech Republic
                              */                              
                             "stateOrProvinceName" => "Czech Republic",
                             /**
                              * localityName -           Locality Name - eg. Prague  
                              */                                
                             "localityName" => "Prague",
                             /**
                              * organizationName -       Organization Name - eg. Whoknows Ltd.  
                              */                             
                             "organizationName" => "Whoknows Ltd.",
                             /**
                              * organizationalUnitName - Organizational Unit Name - eg. PHP Developer.  
                              */                              
                             "organizationalUnitName" => "PHP Developer",
                             /**
                              * !! IMPORTANT !!
                              * commonName -             The Common Name field usually must exactly match the hostname of 
                              *                          the system the certificate will be used on; otherwise, clients should 
                              *                          complain about a certificate to hostname mismatch.                       
                              */                                                           
                             "commonName" => "paul.saparov.biz",
                             /**
                              * emailAddress -           The owener e-mail address - eg. saparov.p@example.com                         
                              */                             
                             "emailAddress" => "saparov.p@example.com"
                              );

    /** 
     *
     * The default constructor that will create a Crypt_OpenSSL_NewCert object
     * for future generated certificate.
     * 
     * @access public
     *
     * @param  array   $certConfig   An array contais iformation about a new certificate.
     * @param  int     $validDays    How many days should be the new certificate valid. (Default: 365 days)
     *       
     * @return void                  No return value. Exception object will be thrown if there is
     *                                an error during construction so the constructor should be called 
     *                                from a try/catch block.     
     */         
    function __construct($certConfig, $validDays = '365')
    {        
        if (!is_array($certConfig)) {
            throw new Crypt_OpenSSL_NewCert_Exception("Constructor error: The first parameter have to be an array with certificate options.\n");
        }
        
        if(array_keys($this->certConfig) != array_keys($certConfig)) {
            throw new Crypt_OpenSSL_NewCert_Exception("Constructor error: An array with certificate options should have keys: countryName, 
                                                        stateOrProvinceName, localityName, organizationName, organizationalUnitName, commonName,
                                                        emailAddress.\n");
        }
        
        $this->certConfig = $certConfig;
        $this->validDays = $validDays;
    }
    
    /**
     * Set a csr resource
     *
     * This function will set a resource given in parameter.
     *
     * @access public
     *
     * @param mixed  $crtResource      A certificate request resource.
     *
     * @return mixed                   Returns true or a PEAR_Error class.        
     */     
    function setCsr($crtResource)
    {
        if (is_resource($crtResource)) {
            $this->_crtRequestResource = $crtResource; 
        } else {
            return PEAR::raiseError("setCrt() error: The first parameter have to be a csr resource.\n");
        }

        return true;
    }    
    
    /**
     * Get the crt resource
     *
     * The getCsr method returns the certificate request resource.
     * 
     * @access public
     *
     * @return mixed Returns the crt resource, otherwise a PEAR_Error class.
     */ 
    function getCsr()
    {
        if (!isset($this->_crtRequestResource)) {
            return PEAR::raiseError("getCsr() error: Unable to get the certificate request resource pointer.\n");
        } else {
            return $this->_crtRequestResource;
        }
    }                                 
}
?>
