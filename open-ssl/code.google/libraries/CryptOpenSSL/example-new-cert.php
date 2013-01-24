<?php
//This example generates a new certificate

require_once 'OpenSSL.php';
require_once 'OpenSSL/NewCert.php';

//Options used in a new certificate
$certConfig = array(
    "countryName" => "CZ",
    "stateOrProvinceName" => "Czech Republic",
    "localityName" => "Prague",
    "organizationName" => "Whoknows Ltd.",
    "organizationalUnitName" => "PHP Developer",
    "commonName" => "HelloWorld",
    "emailAddress" => "saparov.p@example.com"
);

//Create a new OpenSSL_Cert object
$Cert = new Crypt_OpenSSL_Cert('certs/cacert.pem', 'certs/cakey.pem', 'passphrase');
//Class handler for OpenSSL_Cert class
$OpenSSL = new Crypt_OpenSSL($Cert);

//Set up a new cert with $certConfig above and validation 1026 days
$NewCert = new Crypt_OpenSSL_NewCert($certConfig, 1026);

//A new certificate will be signed by $Cert object
$newOpenSSL = $OpenSSL->sign($NewCert, 0);

//A new certificate will be self-signed
//$newOpenSSL = $OpenSSL->sign($NewCert, 1);

//Now we can test the certificate by generating and verifying signed data
$signature = $newOpenSSL->signature('Follow the white rabit!');
echo $newOpenSSL->verify('Follow the white rabit!', $signature); //Should return 1

//Export all to files
$newOpenSSL->exportCert('newcert.crt');
$newOpenSSL->exportKey('newcert.key', 'MY_SECRET_PASSPHRASE_TO_PRIVATE_KEY');
$newOpenSSL->exportCsr('newcert.csr');
?>