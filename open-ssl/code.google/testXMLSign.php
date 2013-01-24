<?php

require_once( 'libraries/xmlseclibs/xmlseclibs.php' );

require_once ('libraries/CryptOpenSSL/OpenSSL.php');
require_once ('libraries/CryptOpenSSL/OpenSSL/NewCert.php');


define ('RSA_PUBKEY_PATH', 'keys/RSA/pubkey.pem');
define ('RSA_PUBKEY_DER_PATH', 'keys/RSA/pubkey.der');
define ('RSA_PRIVKEY_PATH', 'keys/RSA/privkey.pem');

define ('x509_CERT_PATH', 'keys/x509/cert.crt');
define ('x509_PRIVKEY_PATH', 'keys/x509/cert.key');
define ('x509_CSR_PATH', 'keys/x509/cert.csr');


//
// $authType ( RSA / CERT )
// $signMethod ( command / library )

function signResponse($authType,$signMethod) 
{
		 
	$tempFileName = 'saml-response-hobhfbeoijkhlmflmeeogpcabaioknlcmmkcnobf.xml';
	
	$responseXmlString=@file_get_contents($tempFileName);

	if ($authType=='RSA')
	{	  
		$pubKey=RSA_PUBKEY_PATH;
      	$binPubKey=RSA_PUBKEY_DER_PATH;
      	$privKey=RSA_PRIVKEY_PATH;
	}
      else if ($authType=='CERT')
      {
      	$pubCert=x509_CERT_PATH;
      	$privKey=x509_PRIVKEY_PATH;
      }
      
      if ($signMethod=='command')
      {
      	
      	if ($authType=='RSA')
		{	  
      	
      		$cmd = 'xmlsec1 sign --privkey-pem ' . $privKey .
	             ' --pubkey-der ' . $binPubKey . ' --output ' . $tempFileName .
	             '.out  ' . $tempFileName . " >error.txt 2> error2.txt";
		}
		else if ($authType=='CERT')
      	{
			$cmd = 'xmlsec1 sign --privkey-pem ' . $privKey .
	             ' --pubkey-cert-pem ' . $pubCert . ' --output ' . $tempFileName .
	             '.out  ' . $tempFileName . " >error.txt 2> error2.txt";
      		
      	}
		exec($cmd, $resp, $rvalue);
      	
      }
      else if ($signMethod=='library')
      {
      	

		$doc = new DOMDocument();
		$doc->loadXML($responseXmlString);
		
		$nodelist=$doc->getElementsByTagName("Signature");
		$parentnode=$doc->getElementsByTagName("Response");
		$parentnode=$parentnode->item(0);
		$domElement=$nodelist->item(0);
		$parentnode->removeChild($domElement);
	
		$objDSig = new XMLSecurityDSig();
		
		$objDSig->setCanonicalMethod(XMLSecurityDSig::C14N_COMMENTS);

		$objDSig->addReference($doc, XMLSecurityDSig::SHA1, array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'),array('force_uri'=>'true'));

		$objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type'=>'private'));

		$objKey->loadKey($privKey,true);
		
		$objDSig->sign($objKey);
	
		if ($authType=='RSA')
		{	  
			$objDSig->myAppendKey($objKey);
		}
      	else if ($authType=='CERT')
      	{
      		$certCont=@file_get_contents($pubCert);
      		$objDSig->add509Cert($certCont);
      	}
	

		$objDSig->appendSignature($doc->documentElement);
	
		$doc->save($tempFileName.'.out');
  
      }
  
 
	$xmlResult = @file_get_contents($tempFileName . '.out');
	return $xmlResult;

}




function pem2der($pem_data) 
{
		$begin = "BEGIN PUBLIC KEY-----";
		$end   = "-----END";
		$pem_data = substr($pem_data, strpos($pem_data, $begin)+strlen($begin));   
		$pem_data = substr($pem_data, 0, strpos($pem_data, $end));
		$der = base64_decode($pem_data);
		return $der;
}




function generateKeys($authType)
{	
		
		
	if ($authType=='RSA')
	{
	
		//$config = array('private_key_type' => OPENSSL_KEYTYPE_DSA);
		$config = array('private_key_type' => OPENSSL_KEYTYPE_RSA,'private_key_bits' => 1024);
	
		
		$res = openssl_pkey_new($config);
	

		openssl_pkey_export($res, $privkey);

		
		$pubkey=openssl_pkey_get_details($res);
	    $pubkey=$pubkey["key"];
    
		$binaryPubkey=pem2der($pubkey);


		file_put_contents(RSA_PUBKEY_PATH,$pubkey);
		file_put_contents(RSA_PUBKEY_DER_PATH,$binaryPubkey);
		file_put_contents(RSA_PRIVKEY_PATH,$privkey);
		
		//Here I can Test the key generation
	
	}
	else if ($authType=='CERT')
	{
	

		//Options used in a new certificate
		$certConfig = array(
    	"countryName" => "ES",
    	"stateOrProvinceName" => "Spain",
    	"localityName" => "Valencia",
    	"organizationName" => "JoomGapps",
    	"organizationalUnitName" => "PHP Developer",
    	"commonName" => "JoomGapps",
    	"emailAddress" => "info@joomgapps.net"
		);

		//Create a new OpenSSL_Cert object
		$Cert = new Crypt_OpenSSL_Cert('keys/CA/cacert.pem', 'keys/CA/cakey.pem');
		//Class handler for OpenSSL_Cert class
		$OpenSSL = new Crypt_OpenSSL($Cert);

		//Set up a new cert with $certConfig above and validation 1026 days
		$NewCert = new Crypt_OpenSSL_NewCert($certConfig, 1026);

		//A new certificate will be signed by $Cert object
		//$newOpenSSL = $OpenSSL->sign($NewCert, 0);

		//A new certificate will be self-signed
		$newOpenSSL = $OpenSSL->sign($NewCert, 1);

		//Now we can test the certificate by generating and verifying signed data
		$signature = $newOpenSSL->signature('Follow the white rabit!');
		$res=$newOpenSSL->verify('Follow the white rabit!', $signature); //Should return 1
		if ($res==1)
		{
				//Export all to files
			$newOpenSSL->exportCert(x509_CERT_PATH);
			$newOpenSSL->exportKey(x509_PRIVKEY_PATH);
			$newOpenSSL->exportCsr(x509_CSR_PATH);
		
		}
		
	}

}

generateKeys('RSA');
generateKeys('CERT');


signResponse('CERT','command');
//signResponse('RSA','library');
//signResponse('RSA','command');
//signResponse('CERT','library');
//signResponse('CERT','command');






?>




