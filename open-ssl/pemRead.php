<?php
$privatekeyTxt = file_get_contents('privatekey.pem');
$privatekey = openssl_get_privatekey($privatekeyTxt);
$details 	= openssl_pkey_get_details($privatekey);
echo '<pre>'.print_r($details,true).'</pre>';
?>