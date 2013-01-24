<?php
/*
 * not working
 */
require_once ('../Loader.php');
        //
        //Zend_Loader::loadClass('Zend_Gdata');
        //Zend_Loader::loadClass('Zend_Gdata_AuthSub');
       //Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
        Zend_Loader::loadClass('Zend_Gdata_HttpClient');
        //Zend_Loader::loadClass('Zend_Gdata_Calendar');
        //Zend_Loader::loadClass('Zend_Gdata_Gapps');
        //Zend_Loader::loadClass('Zend_Gdata_App_AuthException');
$str = '';
$sock = fsockopen('www.example.com', 80);
$req =     "GET / HTTP/1.1\r\n" .
            "Host: www.example.com\r\n" .
            "Connection: close\r\n" .
            "\r\n";
 
fwrite($sock, $req);
while ($buff = fread($sock, 1024))
$str .= $sock;
 
$response = Zend_Http_Response::fromString($str);
?>