<?php

// Ouverture de quelques fichiers pour lecture
$file = 'file.docx';
$fp = fopen($file, 'r');

// Mise en place d'une connexion basique
$conn_id = ftp_connect('ftp.epfc.eu');

// Identification avec un nom d'utilisateur et un mot de passe
$login_result = ftp_login($conn_id, 'epfckkdq-dev', 'Jpeqechn');
$t = time();
echo 'max_execution_time '.ini_get('max_execution_time') .'<br />';
echo 'max_input_time '.ini_get('max_input_time') .'<br />';
echo 'upload_max_filesize '.ini_get('upload_max_filesize') .'<br />';
echo 'post_max_size '.ini_get('post_max_size') .'<br />';

// Tente de charger le fichier $file
if (ftp_fput($conn_id, $file, $fp, FTP_ASCII)) {
	$t = time() - $t;
    echo "Chargement avec succès du fichier $file en $t s\n";
} else {
    echo "Il y a eu un problème lors du chargement du fichier $file\n";
}

// Fermeture de la connexion et du pointeur de fichier
ftp_close($conn_id);
fclose($fp);

?>
