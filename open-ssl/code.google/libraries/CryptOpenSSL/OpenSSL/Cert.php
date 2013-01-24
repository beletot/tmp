<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This class represents X509 certificate and its private key
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
 * @version    $Id: Cert.php,v 1.2 2008/13/06 21:38:12 saparov Exp $  
 * @link       http://pear.php.net/package/OpenSSL
 *
 */
 
/**
 * Require PEAR class for raising errors in this class.
 */
require_once 'PEAR.php';

/**
 * Require Exception file.
 */
require_once 'Exception.php';

/**
 * Constants need for checking if a X509 certificate can be used for a particular purposes.
 */

/**
 * Purpose verification constant: Can the certificate be used for the client side of an SSL connection?
 */
define('CRYPT_OPENSSL_CERT_PURPOSE_CLIENT', 1);

/**
 * Purpose verification constant: Can the certificate be used for the server side of an SSL connection?
 */
define('CRYPT_OPENSSL_CERT_PURPOSE_SERVER', 2);

/**
 * Purpose verification constant: Can the cert be used for Netscape SSL server?
 */
define('CRYPT_OPENSSL_CERT_PURPOSE_NS_SERVER', 3);

/**
 * Purpose verification constant: Can the cert be used to sign S/MIME email?
 */
define('CRYPT_OPENSSL_CERT_PURPOSE_SMIME_SIGN', 4);

/**
 * Purpose verification constant: Can the cert be used to encrypt S/MIME email?
 */
define('CRYPT_OPENSSL_CERT_PURPOSE_SMIME_ENCRYPT', 5);

/**
 * Purpose verification constant: Can the cert be used to sign a certificate revocation list (CRL)?
 */
define('CRYPT_OPENSSL_CERT_PURPOSE_CRL_SIGN', 6);

/**
 * Purpose verification constant: Can the cert be used for Any/All purposes?
 */
define('CRYPT_OPENSSL_CERT_PURPOSE_ANY', 7);

/**
 * Class represents the OpenSSL X509 certificate and its private key
 *
 * @category   Encryption
 * @package    Crypt_OpenSSL_Cert
 * @author     Pavel Saparov <saparov.p@gmail.com>
 * @copyright  2008 Pavel Saparov
 * @access     public
 */
class Crypt_OpenSSL_Cert
{
    /**
     * A X509 certificate to work with.
     *
     * @access protected
     * @var resource
     */
    protected $_certificateResource;
    
    /**
     * A private key corresponding to the certificate.
     *
     * @access protected
     * @var resource
     */    
    protected $_keyResource;
    
    /**
     * The passphrase for acessing the private key.
     *
     * @access protected
     * @var string
     */     
    protected $_passphrase;
    
    /**
     * X509 certificate infomation.
     *
     * @access public
     * @var array
     */     
    public $certInfo;
    
    /**
     * The default constructor
     *
     * This is the default constructor that will create a Crypt_OpenSSL_Cert object
     * and parse the certificate to readable format.
     * 
     * @access public
     *
     * @param string $certificateFile         Certificate string, resource or file.
     * @param string $keyFile                 Key string, resource or file.
     * @param string $passphrase              If the key file is encrypted the passprase will
     *                                         allow to access the key file.
     *                                      
     * @throws Crypt_OpenSSL_Cert_Exception     
     * @return void                           No return value. Exception object will be thrown if there is
     *                                         an error during construction so the constructor should be called 
     *                                         from a try/catch block.
     *           
     * Crypt_OpenSSL_Cert constructor example:
     * <code>
     * require_once 'Crypt/OpenSSL/Cert.php';
     *
     * try {     
     *     $cert = new Crypt_OpenSSL_Cert('/path/to/cert.pem', '/path/to/privatekey.key', 'privatekey_passphrase');
     * } catch (Crypt_OpenSSL_Cert_Exception $e) {
     *     echo $e->getMessage() . "\n";
     * }
     * </code>      
     */    
    function __construct($certificateFile = null, $keyFile = null, $passphrase = null)
    {
        if (!function_exists('openssl_get_publickey')) {
            throw new Crypt_OpenSSL_Cert_Exception("You PHP5 doesn't support an OpenSSL extension. Please install OpenSSL library >= 0.9.5!\n");
        }
        
        if (!is_null($certificateFile)) {
            if (is_file($certificateFile) || is_resource($certificateFile)) {
                $this->setCert($certificateFile);
                $this->parse();             
            } else {
               throw new Crypt_OpenSSL_Cert_Exception("File $certificateFile should be a X509 certificate file or a resource pointer.\n");
            }
        } else {
            $this->_certificateResource = null;
        }

        if (!is_null($certificateFile)) {
            if (is_file($keyFile) || is_resource($keyFile)) {
                $this->setKey($keyFile, $passphrase);
            } else {
                throw new Crypt_OpenSSL_Cert_Exception("File $keyFile should be a private key file or a resource pointer.\n");
            }
        } else {
            $this->_keyResource = null;
        }
        
        $this->_passphrase = $passphrase;
    }
    
    /**
     * The default deconstructor
     *
     * This is the default deconstructor that will release resources used by the class.
     */     
    function __destruct()
    {
        @openssl_free_key($this->_certificateResource);
        @openssl_free_key($this->_keyResource);
    }    

    /**
     * Overloaded __get method
     *
     * This overloaded __get method allow get information from parsed X509 certificate.
     * 
     * @access public
     *
     * @param string $arrayName    Array value for accessing X509 information.
     *
     * @return mixed               Returns value from the parsed X509 certificate, otherwise raise a PEAR_Error class.
     *              
     * Overloaded get() method example:
     * <code>
     * require_once 'Crypt/OpenSSL/Cert.php';
     *
     * try {     
     *     $cert = new Crypt_OpenSSL_Cert('/path/to/cert.pem');
     * } catch (Crypt_OpenSSL_Cert_Exception $e) {
     *     echo $e->getMessage() . "\n";
     * }
     *      
     * echo "Certificate's common name: " . $cert->subject['commonName'];     
     * echo "Certificate is valid to: " . date("d m Y H:i:s", $cert->validTo_time_t);
     * </code>    
     */ 
    function __get($arrayName)
    {
        if (!isset($this->certInfo)) {
            return PEAR::raiseError("__get() error: Unable to retreive infomation. First, you should call a parse() method.\n");
        } else if (!array_key_exists($arrayName, $this->certInfo)) {
            return PEAR::raiseError("__get() error: Unable to retreive infomation. Bad argument $arrayName.\n");
        }

        return $this->certInfo[$arrayName];
    }

    /**
     * Get the X509 certificate resource
     *
     * The getCert method returns the X509 certificate resource.
     * 
     * @access public
     *
     * @return mixed Returns the X509 certificate resource, otherwise a PEAR_Error class.
     */ 
    function getCert()
    {
        if (!isset($this->_certificateResource)) {
            return PEAR::raiseError("getCert() error: Unable to get the X509 certificate resource pointer.\n");
        } else {
            return $this->_certificateResource;
        }
    }

    /**
     * Get the key certificate resource
     *
     * The getKey method returns the private key resource.
     * 
     * @access public
     *
     * @return mixed Returns the private key resource, otherwise a PEAR_Error class.
     */ 
    function getKey()
    {
        if (!isset($this->_keyResource)) {
            return PEAR::raiseError("getKey() error: Unable to get the private key resource pointer.\n");
        } else {
            return $this->_keyResource;
        }
    }
    
    /**
     * Get the passphrase
     *
     * The getPassphrase method returns a passphrase for accessing the private key.
     * 
     * @access public
     *
     * @return string Returns a passphrase string.
     */ 
    function getPassphrase()
    {
        return $this->_passphrase;
    }    

    /**
     * Set a certificate resource
     *
     * This function will create a resource from a X509 certificate file or another X509 resource
     * and automaticaly parse infomation about the certificate.
     *
     * @access public
     *
     * @param mixed $certificateFile  A X509 certificate resource or file.
     *
     * @return mixed                  Returns true or a PEAR_Error class.
     * 
     * setCert() method example:
     * <code>
     * require_once 'Crypt/OpenSSL/Cert.php';
     *
     * try {     
     *     $cert = new Crypt_OpenSSL_Cert();
     * } catch (Crypt_OpenSSL_Cert_Exception $e) {
     *     echo $e->getMessage() . "\n";
     * }
     *      
     * $cert->setCert('/path/to/cert.pem');
     * </code>          
     */
    function setCert($certificateFile)
    {
    	if (is_resource($certificateFile)) {
            $this->_certificateResource = $certificateFile;
            $this->parse();
        } else if (is_file($certificateFile)) {
            $this->_certificateResource = openssl_x509_read(file_get_contents($certificateFile));
            $this->parse();
        } else {
            return PEAR::raiseError("setCert() error: File $certificateFile should be a X509 certificate file or a resource pointer.\n");
        }

        return true;
    }

    /**
     * Set a key resource
     *
     * This function will create a resource from a private key file or from another key resource.
     *
     * @access public
     *
     * @param mixed  $keyFile      A X509 certificate key resource or file.
     * @param string $passphrase   If the key is encrypted you have to set passphrase for the right
     *                              functionality and manipulation with certificate.
     *
     * @return mixed               Returns true or a PEAR_Error class.
     * 
     * setKey() method example:
     * <code>
     * require_once 'Crypt/OpenSSL/Cert.php';
     *
     * try {     
     *     $cert = new Crypt_OpenSSL_Cert();
     * } catch (Crypt_OpenSSL_Cert_Exception $e) {
     *     echo $e->getMessage() . "\n";
     * }
     *      
     * $cert->setKey('/path/to/privatekey.key', 'privatekey_passphrase');
     * </code>           
     */     
    function setKey($keyFile, $passphrase = null)
    {
        $this->_passphrase = $passphrase;
		if (is_resource($keyFile)) {
            $this->_keyResource = $keyFile; 
		} else if (is_file($keyFile)) {
            $this->_keyResource = openssl_get_privatekey(file_get_contents($keyFile), $passphrase);
			echo '<pre>'.print_r(openssl_pkey_get_details($this->_keyResource),true).'</pre>';
        } else {
            return PEAR::raiseError("setKey() error: File $keyFile should be a private key file or a resource pointer.\n");
        }

        return true;
    }

    /**
     * Set passphrase
     *
     * The setPassphrase will set the passphrase for private key.
     *      
     * @param string $passphrase   Passphrase for private key. 
     *     
     * @access public
     */ 
    function setPassphrase($passphrase)
    {
        return $this->_passphrase = $passphrase;
    }

    /**
     * Check if a private key corresponds to a certificate.
     *
     * Check wether the given key is the private key that corresponds to the X509 cert.
     *
     * @access public
     *
     * @return mixed True if the private key corresponds to certificate, otherwise false.
     *                If the X509 certificate and the key are not set this will return a PEAR_Error class.
     *            
     * check() method example:
     * <code>
     * require_once 'Crypt/OpenSSL/Cert.php';
     *
     * try {     
     *     $cert = new Crypt_OpenSSL_Cert('/path/to/cert.pem', '/path/to/privatekey.key', 'privatekey_passphrase');
     * } catch (Crypt_OpenSSL_Cert_Exception $e) {
     *     echo $e->getMessage() . "\n";
     * }
     *      
     * if ($cert->check()) echo "All is fine";
     *  else echo "Problems were truned out to be here...";
     * </code>            
     */
    function check()
    {
        if (!(isset($this->_certificateResource) && isset($this->_keyResource))) {
            return PEAR::raiseError("check() error: You have to set a X509 certificate and private key.\n");
        }

        if (openssl_x509_check_private_key($this->_certificateResource, $this->_keyResource)) {
            return true;
        }

        return false;
    }

    /**
     * Parse the X509 certificate and return the information as an array.
     *
     * Returns information about the supplied x509cert, including fields such as subject name, 
     * issuer name, purposes, valid from and valid to dates etc.
     * 
     * @access public
     *
     * @param bool   $shotnames                  Shortnames controls how the data is indexed in 
     *                                            the array - if shortnames is TRUE (the default) 
     *                                            then fields will be indexed with the short name form, 
     *                                            otherwise, the long name form will be used - e.g.: 
     *                                            CN is the shortname form of commonName, etc.
     * @param string $certificateFile            Content of the certificate or path to it.
     *
     * @return mixed                             Returning array with information about the certificate, otherwise a PEAR_Error class.
     * 
     * parse() method example:
     * <code>
     * require_once 'Crypt/OpenSSL/Cert.php';
     *
     * try {     
     *     $cert = new Crypt_OpenSSL_Cert('/path/to/cert.pem');
     * } catch (Crypt_OpenSSL_Cert_Exception $e) {
     *     echo $e->getMessage() . "\n";
     * }
     * 
     * $cert->parse(true);
     *                    
     * echo "Certificate common name: " . $cert->subject['CN']; 
     * </code>            
     */
    function parse($shotnames = false)
    {
        if (!isset($this->_certificateResource)) {
            return PEAR::raiseError("parse() error: You didn't specify the X509 certificate. Use a method setCert().\n");
        }

        $this->certInfo = openssl_x509_parse($this->_certificateResource, $shotnames);
        return $this->certInfo;
    }

    /** 
     * Verifies if a certificate can be used for a particular purpose.
     *
     * Method checkPurpose() examines a certificate to see if it can be used for the specified purpose.
     * 
     * @access public
     *
     * @param int    $purpose         You may specify only one purpose which are defined above. 
     * @param string $certificateFile Content of the certificate or path to it.
     * 
     * @return bool                   Returns TRUE if the certificate can be used for the intended purpose, otherwise FALSE if it cannot.
     *
     * checkPurpose() method example:
     * <code>
     * require_once 'Crypt/OpenSSL/Cert.php';
     *
     * try {     
     *     $cert = new Crypt_OpenSSL_Cert('/path/to/cert.pem');
     * } catch (Crypt_OpenSSL_Cert_Exception $e) {
     *     echo $e->getMessage() . "\n";
     * }
     *
     * if ($cert->checkPurpose(CRYPT_OPENSSL_CERT_PURPOSE_SMIME_ENCRYPT)) echo "You can use it";
     *  else echo "The certificate cannot be used for this purpose";
     * </code>          
     */
    function checkPurpose($purpose = CRYPT_OPENSSL_CERT_PURPOSE_ANY)
    {
        if (!isset($this->_certificateResource)) {
            return PEAR::raiseError("checkPurpose() error: You didn't specify the X509 certificate. Use a method setCert().\n");
        }

        if (!isset($this->certInfo) || ($purpose > 0 && $purpose < 8)) {
            return PEAR::raiseError("checkPurpose() error: Unable to check purpose. Bad constant $purpose.\n");
        }

        if (array_key_exists("purposes", $this->certInfo)) {
            return $this->certInfo['purposes'][$purpose][0];
        }

        return false;
    }
}
?>
