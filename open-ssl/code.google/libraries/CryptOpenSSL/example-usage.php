<?php
require_once 'OpenSSL.php';
require_once 'OpenSSL/Cert.php';

PEAR::setErrorHandling(PEAR_ERROR_PRINT);

try {
    /**
     * Files were generated with openssl
     *      
     * SELF-SIGNED CERTIFICATE
     * openssl req -new -x509 -keyout cakey.pem -out cacert.pem -days 3650 -text
     *      
     */
    
    //Create a new OpenSSL_Cert class                
    $Cert = new Crypt_OpenSSL_Cert('certs/cacert.pem', 'certs/cakey.pem', 'passphrase');

    //Class handler for OpenSSL_Cert class
    $OpenSSL = new Crypt_OpenSSL($Cert);
    
} catch (Crypt_OpenSSL_Cert_Exception $e) {

    echo $e->getMessage() . "\n";
    
}

//Other way to setup files
/*
    $Cert->setCert('certs/testcert.crt');
    $Cert->setKey('certs/testcert.key', 'passphrase');    
*/

//Get some info about cert
echo "Certificate's common name: " . $Cert->subject['commonName'] . "\n";

//Check if a private key corresponds to a certificate
if($Cert->check()) {
    echo "Cert is OK! <br />";
}

//Verify signature
$data = "Follow the white rabit!";

$signature = $OpenSSL->signature($data);
if($OpenSSL->verify('Follow the white rabit!', $signature)) {
    echo "\$signature is OK! <br />";
}

//Encryption via cert
$ar = $OpenSSL->encrypt('The matrix is YOU!');

//echo '<pre>'.print_r($ar,true).'</pre>';
// Decription
echo $OpenSSL->decrypt($ar['0'], $ar['1']);
?>