<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * OpenSSL wrapper class for managing X509 certificates
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
 * @version    $Id: OpenSSL.php,v 1.2 2008/13/06 21:43:08 saparov Exp $  
 * @link       http://pear.php.net/package/OpenSSL
 *
 */
 
/**
 * Require PEAR class for raising errors in this class.
 */
require_once 'PEAR.php';

/**
 * Require Exceptions to throw.
 */
require_once 'OpenSSL/Exception.php';

/**
 * Constants need for creating a new certificate.
 */

/**
 * SHA1 signature algorithm
 */ 
define('CRYPT_OPENSSL_SIGN_SHA1', OPENSSL_ALGO_SHA1);

/**
 * MD5 signature algorithm
 */ 
define('CRYPT_OPENSSL_SIGN_MD5', OPENSSL_ALGO_MD5);

/**
 * MD2 signature algorithm
 */ 
define('CRYPT_OPENSSL_SIGN_MD4', OPENSSL_ALGO_MD4);

/**
 * MD2 signatura algorithm
 */ 
//define('CRYPT_OPENSSL_SIGN_MD2', OPENSSL_ALGO_MD2);

/**
 * Class for encryption and decryption with ciphers, generating a new self-signed or
 * CA signed certificate, exporting certificate, generating and validating signature.
 *  
 * @category   Encryption
 * @package    Crypt_OpenSSL
 * @author     Pavel Saparov <saparov.p@gmail.com>
 * @copyright  2008 Pavel Saparov
 * @access     public
 */

class Crypt_OpenSSL
{
    /**
     * The OpenSSL_Cert object.
     *
     * @access protected
     * @var object
     */
    protected $_cert;
    
    /**
     * Contains an encrypted data for futher decryprion
     * created with public key.     
     *         
     * @access public    
     * @var string 
     */   
    public $cryptedData;

    /**
     * The envelope of keys needed for decryption.
     *         
     * @access public    
     * @var string
     */   
    public $envKey;
    
    /**
     * Contains an encrypted data for futher decryprion
     * created with public key.     
     *         
     * @access public    
     * @var string 
     */   
    public $signature;

    /**
     * Configuration parameters for openssl certificate additional options. 
     *         
     * @access public    
     * @var array  
     */
    public $sslConfig = array(
                             /**
                              * digest_alg -       Selects which digest method to use.
                              */                              
                             "digest_alg" => "md5",
                             /**
                              * x509_extensions -  Selects which extensions should be used when creating a x509 certificate.
                              *                    The extentions to add to the self signed cert.                            
                              */                  
                             "x509_extensions" => "v3_ca",
                             /**
                              * req_extensions -   Selects which extensions should be used when creating a CSR.
                              *                    The extensions to add to a certificate request.             
                              */                                                                          
                             "req_extensions" => "v3_req",
                             /**
                              * private_key_bits - Specifies how many bits should be used to generate a private key.
                              */                                                           
                             "private_key_bits" => 1024,
                             /**
                              * private_key_type - Specifies the type of private key to create. This can be one of OPENSSL_KEYTYPE_DSA,
                              *                    OPENSSL_KEYTYPE_DH or OPENSSL_KEYTYPE_RSA. The default value is OPENSSL_KEYTYPE_RSA 
                              *                    which is currently the only supported key type.
                             */                              
                             "private_key_type" => OPENSSL_KEYTYPE_RSA,  
                             /**
                              * encrypt_key -      Should an exported key (with passphrase) be encrypted?
                              */                              
                             "encrypt_key" => true,
                             /*
                              * config -           Path to to the openssl.conf file.
                              */                              
                             "config" => "/etc/ssl/openssl.cnf"
                             //"config" => "/etc/pki/tls/openssl.cnf"                              
                          );
    /**
     * The default constructor
     *
     * This is the default constructor that will create a Crypt_OpenSSL object
     * for manipulation with a certificate.
     * 
     * @access public
     *
     * @param object $cert            First parameter must be an object of type Crypt_OpenSSL_Cert.
     * 
     * @return void                   No return value. Exception object will be thrown if there is
     *                                 an error during construction so the constructor should be called 
     *                                 from a try/catch block.
     *                                 
     * OpenSSL constructor example:
     * <code>
     * require_once 'Crypt/OpenSSL.php';     
     * require_once 'Crypt/OpenSSL/Cert.php';
     *
     * try {
     *     $cert = new Crypt_OpenSSL_Cert();
     *     $openssl = new Crypt_OpenSSL($cert);
     * } catch (Crypt_OpenSSL_Exception $e) {
     *     echo $e->getMessage() . "\n";
     * }              
     * </code>
     */
    function __construct(&$cert)
    {
        if ($cert instanceof Crypt_OpenSSL_Cert) {
            $this->_cert = $cert;
        } else {
            throw new Exception("Parameter \$cert must be an object type of a Crypt_OpenSSL_Cert class.\n");
        }
    }

    /**
     * Encrypts data
     *
     * Method encrypt() will encrypt plain data with X509 public key.
     * 
     * @access public
     *
     * @param string $dataForEncryption            Data for encryption.
     * 
     * @return array                               Will return an array with two elements, the first one
     *                                              are crypted data second is key envelope.
     *                                              
     * Encrypt and decrypt data example:
     * <code>     
     * require_once 'Crypt/OpenSSL.php';     
     * require_once 'Crypt/OpenSSL/Cert.php';
     *
     * try {
     *     $cert = new Crypt_OpenSSL_Cert();
     *     $openssl = new Crypt_OpenSSL($cert);
     * } catch (Crypt_OpenSSL_Exception $e) {
     *     echo $e->getMessage() . "\n";
     * }
     * 
     * //setting the public key for encryption          
     * $cert->setCert('/path/to/cert.pem');
     *      
     * //encrypt data 
     * $ar = $openssl->encrypt('The Matrix is YOU!');
     * //print_r($ar);
     * 
     *
     * try {
     *     $cert2 = new Crypt_OpenSSL_Cert();
     *     $openssl2 = new Crypt_OpenSSL($cert2);
     * } catch (Crypt_OpenSSL_Exception $e) {
     *     echo $e->getMessage() . "\n";
     * }
     * 
     * //setting the private key for decryption          
     * $cert2->setKey('/path/to/privatekey.key', 'privatekey_passphrase');
     * 
     * //decrypt data                                         
     * echo $openssl2->decrypt($ar['0'], $ar['1']);                
     * </code>                         
     */    
    function encrypt($dataForEncryption)
    {   
        if (!is_string($dataForEncryption)) {
            return PEAR::raiseError("encrypt() error: You have to add data for encryption.\n");
        }
        
        openssl_seal($dataForEncryption, $cryptedData, $envelope_Key, array($this->_cert->getCert()));
        $this->cryptedData = $cryptedData;
        $this->envKey = $envelope_Key;
        
        return array($cryptedData, $envelope_Key['0']);
    }

    /**
     * Decrypts data
     *
     * Method decrypt() will decrypt data with certificate private key.
     * 
     * @access public
     *
     * @param string $cryptedData            Data for decription.
     * 
     * @return true                          Returns true, otherwise a PEAR_Error class.
     */   
    function decrypt($cryptedData, $envelope_Key)
    {
        if (!is_string($cryptedData)) {
            return PEAR::raiseError("decrypt() error: You have to add data for decryption.\n");
        }
        
        if (!is_string($envelope_Key)) {
            return PEAR::raiseError("decrypt() error: You have to set a key envelope.\n");
        }
        
        openssl_open($cryptedData, $decryptedData, $envelope_Key, $this->_cert->getKey());
        return $decryptedData;
    }

    /**
     * Sign data
     *
     * Method signature() creates encrypted data with a certificate private key.
     * 
     * @access public
     *
     * @param string $dataToSign             Data to sign.
     * @param int    $algorithm              Specify which encryption algorithm to use.
     * 
     * @return string                        Returns string with encrypted data.
     * 
     * Sign and verify data example:     
     * <code>     
     * require_once 'Crypt/OpenSSL.php';     
     * require_once 'Crypt/OpenSSL/Cert.php';
     *
     * try {
     *     $cert = new Crypt_OpenSSL_Cert();
     *     $openssl = new Crypt_OpenSSL($cert);
     * } catch (Crypt_OpenSSL_Exception $e) {
     *     echo $e->getMessage() . "\n";
     * }
     *          
     * $cert->setCert('/path/to/cert.pem');
     * $cert->setKey('/path/to/privatekey.key', 'privatekey_passphrase');
     * 
     * $signature = $openssl->signature('Follow the white rabit!');
     *
     * $openssl->verify('Follow the white rabit!', $signature); //will return 1
     * $openssl->verify('Follow the rabit!', $signature); //will return 0
     * </code>          
     */     
    function signature($dataToSign, $algorithm = CRYPT_OPENSSL_SIGN_SHA1)
    {
        if (!is_string($dataToSign)) {
            return PEAR::raiseError("signature() error: You have to add data for encryption.\n");
        }
        
        openssl_sign($dataToSign, $signature, $this->_cert->getKey(), $algorithm);
        $this->signature = $signature;
        
        return $signature;    
    }

    /**
     * Verify signed data
     *
     * Method verify() will check if data corresponds to the created signature.
     * 
     * @access public
     *
     * @param string    $dataToCheck         Data need to be checked.
     * @param string    $signature           Generated signature.
     * 
     * @return mixed                         Returns 1 if the signature is correct, 0 if it is incorrect, and -1 on error, otherwise a 
     *                                        PEAR_Error class.
     */     
    function verify($dataToCheck, $signature, $algorithm = CRYPT_OPENSSL_SIGN_SHA1)
    {
        if (!is_string($dataToCheck)) {
            return PEAR::raiseError("verify() error: You have to add data for encryption.\n");
        }
        
        if (!is_string($signature)) {
            return PEAR::raiseError("verify() error: You have to set the signature.\n");
        }        
        
        $check = openssl_verify($dataToCheck, $signature, $this->_cert->getCert(), $algorithm);
        
        return $check;    
    }    

    /**
     * Sign a certificate
     *
     * Method sign() will sign/create a new certificate.
     * First parameter $Cert will by signed by second parameter $caCert. If $caCert is null
     * then it will be used default certificate.     
     * 
     * @access public
     *
     * @param object $Cert     A Crypt_OpenSSL_NewCert object.  
     * @param object $caCert   A Crypt_OpenSSL_Cert object.
     * 
     * @return mixed         Returns a new Crypt_OpenSSL object, otherwise PEAR_Error.
     * 
     * Creating, signing and exporting a new certificate example:
     * <code>
     * require_once 'Crypt/OpenSSL.php';     
     * require_once 'Crypt/OpenSSL/NewCert.php';
     * 
     * try {
     *   //Create a new OpenSSL_Cert object
     *   $cert = new Crypt_OpenSSL_Cert('certs/cacert.pem', 'certs/cakey.pem', 'passphrase');
     *   //Class handler for OpenSSL_Cert class
     *   $openssl = new Crypt_OpenSSL($cert);
     * } catch (Crypt_OpenSSL_Exception $e) {
     *    echo $e->getMessage() . "\n";
     * }
     *      
     * $certconfig = array(
     *    "countryName" => "CZ",
     *    "stateOrProvinceName" => "Czech Republic",
     *    "localityName" => "Prague",
     *    "organizationName" => "Whoknows Ltd.",
     *    "organizationalUnitName" => "PHP Developer",
     *    "commonName" => "paul.saparov.cz",
     *    "emailAddress" => "saparov.p@example.com"
     *    );
     *
     * //Set up a new cert with $certConfig above and validation 1026 days
     * $newCert = new Crypt_OpenSSL_NewCert($certConfig, 1026);
     *
     * //A new certificate will be signed by $Cert object
     * $newOpenSSL = $OpenSSL->sign($newCert, 0);
     *
     * //A new certificate will be self-signed
     * //$newOpenSSL = $OpenSSL->sign($newCert, 1);
     *      
     * //Now we can test the certificate by generating and verifying signed data
     * $signature = $newOpenSSL->signature('Follow the white rabit!');
     * echo $newOpenSSL->verify('Follow the white rabit!', $signature); //Should return 1
     *      
     * //Export all to files
     * $newOpenSSL->exportCert('newcert.crt');
     * $newOpenSSL->exportKey('newcert.key', 'MY_SECRET_PASSPHRASE_TO_PRIVATE_KEY');
     * $newOpenSSL->exportCsr('newcert.csr');
     * </code>                                        
     */  
    function sign(&$newCert, $selfsigned = 0)
    {
        if ($newCert instanceof Crypt_OpenSSL_NewCert) {           
            $newCert->setKey(openssl_pkey_new($newCert->certConfig));
            $csrNew=openssl_csr_new($newCert->certConfig, $newCert->getKey(), $this->sslConfig);
            $strerr=openssl_error_string(); 
            $newCert->setCsr($csrNew);
            $newCert->setCert(openssl_csr_sign($newCert->getCsr(), 
                                                ($selfsigned == 0 ? $this->_cert->getCert() : null), 
                                                array($this->_cert->getKey(),
                                                      $this->_cert->getPassphrase()),
                                                $newCert->validDays,
                                                $this->sslConfig));
        } else {
            return PEAR::raiseError("sign() error: The first parameter have to be a Crypt_OpenSSL_NewCert object.\n");        
        }
                                               
        return new Crypt_OpenSSL($newCert);
    }

    /**
     * Export a certificate resource to file
     *
     * Method exportCert() will export the new X509 cert into specific directory 
     * 
     * @access public
     *
     * @param string $filename    File name.
     * @param bool   $notreadable Should be the certificate in human readable format?    
     * 
     * @return mixed              Returns true, otherwise a PEAR_Error class.
     */         
    function exportCert($filename = 'default.crt', $notreadable = true)
    {
        $filepath = pathinfo($filename);
        if (!is_writable($filepath['dirname'])) {
            return PEAR::raiseError("exportCert() error: Unable to create a file. Check if your directory is writeable.\n");
        }
        
        openssl_x509_export_to_file($this->_cert->getCert(), $filename, $notreadable);
        return true;
    }

    /**
     * Export a key resource to file
     *
     * Method exportKey() will export the cert key into specific directory 
     * 
     * @access public
     *
     * @param string $filename    File name.
     * @param bool   $passphrase  Secret passphrase to the key    
     * 
     * @return mixed              Returns true, otherwise a PEAR_Error class.
     */    
    function exportKey($filename = 'default.key', $passphrase = null)
    {
        $filepath = pathinfo($filename);
        if (!is_writable($filepath['dirname'])) {
            return PEAR::raiseError("exportKey() error: Unable to create a file. Check if your directory is writeable.\n");
        }
        
        openssl_pkey_export_to_file($this->_cert->getKey(), $filename, $passphrase, $this->sslConfig);
        return true;
    }

    /**
     * Export a certificate request resource to file
     *
     * Method exportCsr() will export the certificate request into specific directory 
     * 
     * @access public
     *
     * @param string $filename    File name.
     * @param bool $notreadable   The optional parameter $notreadable affects the verbosity of the output; 
     *                             if it is FALSE then additional human-readable information is included 
     *                             in the output. The default value of notext is TRUE.         
     * 
     * @return mixed              Returns true, otherwise a PEAR_Error class.
     */      
    function exportCsr($filename = 'default.csr', $notreadable = true)
    {
        $filepath = pathinfo($filename);
        if (!is_writable($filepath['dirname'])) {
            return PEAR::raiseError("exportCsr() error: Unable to create a file. Check if your directory is writeable.\n");
        }
        
        openssl_csr_export_to_file($this->_cert->getCsr(), $filename, $notreadable);
        return true;
    }
    
    /**
     * Export a certificate resource as a string
     *
     * Method exportStringCert() will print the new X509 certificate as a string 
     * 
     * @access public
     *
     * @param bool $notreadable   The optional parameter $notreadable affects the verbosity of the output; 
     *                             if it is FALSE then additional human-readable information is included 
     *                             in the output. The default value of notext is TRUE.    
     * 
     * @return string             Returns a string.
     */         
    function exportStringCert($notreadable = true)
    {        
        openssl_x509_export($this->_cert->getCert(), $output, $notreadable);
        return $output;
    }
    
    /**
     * Export a key resource into a string
     *
     * Method exportStringKey() will print an exportable representation as a string 
     * 
     * @access public
     *
     * @param bool   $passphrase  Secret passphrase to the key    
     * 
     * @return string             Returns a string.
     */    
    function exportStringKey($passphrase = null)
    {
        openssl_pkey_export($this->_cert->getKey(), $output, $passphrase, $this->sslConfig);
        return $output;
    }
    
    /**
     * Export a certificate request resource as a string
     *
     * Method exportCsr() will print the certificate request as a string 
     * 
     * @access public
     *
     * @param bool $notreadable   The optional parameter $notreadable affects the verbosity of the output; 
     *                             if it is FALSE then additional human-readable information is included 
     *                             in the output. The default value of notext is TRUE.
     * 
     * @return string             Returns a string.
     */      
    function exportStringCsr($notreadable = true)
    {
    /*
        if (!isset($this->_cert->getCsr()) {
            return PEAR::raiseError("exportStringCsr() error: Certificate request resource isn't set.\n");
        }        
    */    
        openssl_csr_export_to_file($this->_cert->getCsr(), $output, $notreadable);
        return $output;
    }               
}
?>