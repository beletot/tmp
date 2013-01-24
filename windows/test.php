<?php
$filename = 'houhou/voiture.txt';
//print_r(posix_getgrgid(filegroup($filename)));

$stat = stat($filename);

/*
 * Affichage de la date et heure de l'accès à ce fichier,
 * identique à l'appel à la fonction fileatime()
 */
echo 'Date et heure d\'accès : ' . $stat['atime'].'<br />';

/*
 * Affiche de la date et heure de modification du fichier,
 * identique à l'appel à la fonction filemtime()
 */
echo 'Date et heure de modification : ' . $stat['mtime'].'<br />';;

/* Affichage du numéro du device */
echo 'Numéro du Device : ' . $stat['dev'].'<br />';;
?>
