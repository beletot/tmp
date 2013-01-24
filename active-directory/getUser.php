<?php
/***********************************************************************
************************************************************************
*
* PHP NTLM GET LOGIN
* Version 0.2.1
* http://www.secusquad.com/ntlm/
* Copyright (c) 2004 Nicolas GOLLET ( Nicolas (dot) gollet (at) secusquad (dot) com )
* Copyright (c) 2004 Flextronics Saint-Etienne
*
* This program is free software. You can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License.
*
***********************************************************************/
session_start();
$headers = apache_request_headers(); // Récupération des l'entêtes client
if (@$_SERVER['HTTP_VIA'] != NULL){ // nous verifions si un proxy est utilisé : parceque l'identification par ntlm ne peut pas passer par un proxy
echo "Proxy bypass!";
}
elseif($headers['Authorization'] == NULL){ //si l'entete autorisation est inexistante
header( "HTTP/1.0 401 Unauthorized" ); //envoi au client le mode d'identification
header( "WWW-Authenticate: NTLM" ); //dans notre cas le NTLM
exit; //on quitte
}
if(isset($headers['Authorization'])) //dans le cas d'une authorisation (identification)
{
if(substr($headers['Authorization'],0,5) == 'NTLM '){ // on vérifit que le client soit en NTLM
$chaine=$headers['Authorization'];
$chaine=substr($chaine, 5); // recuperation du base64-encoded type1 message
$chained64=base64_decode($chaine); // decodage base64 dans $chained64
if(ord($chained64{8}) == 1){
// |_ byte signifiant l'etape du processus d'identification (etape 3)
// verification du drapeau NTLM "0xb2" à l'offset 13 dans le message type-1-message (comp ie 5.5+) :
if (ord($chained64[13]) != 178){
echo "NTLM Flag error!";
exit;
}
$retAuth = "NTLMSSP".chr(000).chr(002).chr(000).chr(000).chr(000).chr(000).chr(000).chr(000);
$retAuth .= chr(000).chr(040).chr(000).chr(000).chr(000).chr(001).chr(130).chr(000).chr(000);
$retAuth .= chr(000).chr(002).chr(002).chr(002).chr(000).chr(000).chr(000).chr(000).chr(000);
$retAuth .= chr(000).chr(000).chr(000).chr(000).chr(000).chr(000).chr(000);
$retAuth64 =base64_encode($retAuth); // encode en base64
$retAuth64 = trim($retAuth64); // enleve les espaces de debut et de fin
header( "HTTP/1.0 401 Unauthorized" ); // envoi le nouveau header
header( "WWW-Authenticate: NTLM $retAuth64" ); // avec l'identification supplémentaire
exit;
}
else if(ord($chained64{8}) == 3){
// |_ byte signifiant l'etape du processus d'identification (etape 5)
// on recupere le domaine
$lenght_domain = (ord($chained64[31])*256 + ord($chained64[30])); // longueur du domain
$offset_domain = (ord($chained64[33])*256 + ord($chained64[32])); // position du domain.
$domain = str_replace("\0","",substr($chained64, $offset_domain, $lenght_domain)); // decoupage du du domain
//le login
$lenght_login = (ord($chained64[39])*256 + ord($chained64[38])); // longueur du login.
$offset_login = (ord($chained64[41])*256 + ord($chained64[40])); // position du login.
$login = str_replace("\0","",substr($chained64, $offset_login, $lenght_login)); // decoupage du login
if ( $login != NULL){
// stockage des données dans des variable de session
$_SESSION['Login']=$login;
header("Location: newpage.php");
exit;
}
else{
echo "NT Login empty!";
}
}
}
}
?> 