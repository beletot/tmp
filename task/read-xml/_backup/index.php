<?php
/*
 * mise à jour des carnets d'adresse thunderbird
 * js - l'event on failure ne se déclenche que si il ne peut pas lire le lien
 * pas si la requêt est nul...
 */
/* 04/04 be
 * comparer avec l'autre fichier.
 */

define('_JEXEC',true);
define ('DS','/');
require ('controller.php');
require('helper'.DS.'database.php');
require('helper'.DS.'writeSql.php');
require('helper'.DS.'ftp.php');
$t = time();

$controller = new JController();
try {
	$controller->writeSql();
	$controller->sendToFtp();
	$controller->insertSql();
} catch (Exception $e) {
    //echo 'Error : ',  $e->getMessage(), '<br />';
    //$error = $e->getMessage();
    echo $controller->error->comment;
    //echo '<pre>'.print_r($error,true).'</pre>';
}

// passer le t en global
//en construct
//$t = time() - $t;
?>
