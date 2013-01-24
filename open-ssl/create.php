<?php
// Assigne les valeurs du nom distingué à utiliser avec le certificat
// Vous devez remplacer les valeurs suivantes pour qu'elles correspondent
// au nom de votre compagnie, ou, plus précisément, le nom de la personne
// qui représente le site de votre compagnie pour qui vous générez des certificats.
// Pour les certificats SSL, le commonName est généralement le nom de domaine
// pour lequel vous installez le certificat, mais pour les certificats S/MIME,
// le commonName sera le nom de la personne qui utilisera le certificat.
$dn = array(
    "countryName" => "UK",
    "stateOrProvinceName" => "Somerset",
    "localityName" => "Glastonbury",
    "organizationName" => "The Brain Room Limited",
    "organizationalUnitName" => "PHP Documentation Team",
    "commonName" => "Wez Furlong",
    "emailAddress" => "wez@example.com",
    "config" => "/etc/ssl/openssl.cnf"
);

// Génère les clés privée et publique
$privkey = openssl_pkey_new();

// Génère la requête de signature de certificat
$csr = openssl_csr_new($dn, $privkey);

// Vous souhaiterez généralement créer un certificat auto-signé
// une fois que votre autorité de certification accède à votre requête
// Cette commande crée une certificat auto-signé valide 365 jours
$sscert = openssl_csr_sign($csr, null, $privkey, 365);

// Maintenant, vous voulez préserver la clé privée, la CSR et le certificat
// auto-signé, de façon à ce qu'ils puissent être installés sur votre
// serveur web, serveur mail ou client mail (suivant l'utilisation).
// Cet exemple vous montre comment placer ces éléments dans des variables
// mais vous pouvez aussi les mettre directement dans des fichiers.
// Typiquement, vous allez envoyer la CSR à votre autorité de certification
// qui vous émettra un "vrai" certificat.
openssl_csr_export($csr, $csrout) and var_dump($csrout);
openssl_x509_export($sscert, $certout) and var_dump($certout);
openssl_pkey_export($privkey, $pkeyout, "mypassword") and var_dump($pkeyout);

// Affiche les erreurs qui sont survenues
while (($e = openssl_error_string()) !== false) {
    echo $e . "\n";
}
die();
$passphrase = 'voiture';
$config = array(
	'private_key_bits' => 1024,
	'private_key_type' => OPENSSL_KEYTYPE_RSA,
);
$privateKey = openssl_pkey_new($config);

echo '__LINE__ <pre>'.print_r($privateKey,true).'</pre>';
openssl_pkey_export_to_file($privateKey, 'privatekey', $passphrase);
 
// get the public key $keyDetails['key'] from the private key;
$keyDetails = openssl_pkey_get_details($privateKey);
file_put_contents('publickey', $keyDetails['key']);
?>