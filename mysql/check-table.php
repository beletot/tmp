<?php 
$db= mysql_connect( "localhost" , "root" , "" ) or die ("erreur de connexion base 1");
mysql_select_db('extranet-epfc',$db) or die ("erreur de connexion base 2");

$rows = mysql_query("SHOW TABLE STATUS");

while ($row = mysql_fetch_array($rows)){
	echo "Nom de la table : ".$row['Name']."<br>";
	echo "Nombre de lignes : ".$row['Rows']."<br>";
	$taille = $row['Data_length'] + $row['Index_length'];
	echo "Espace occupe de la table (base de donnees + index)): ".$taille."<br>";
	$pourcentage =  $taille / $row['Max_data_length'];
	//$pourcentage = round($pourcentage, 8);
	echo "Pourcentage d'occupation = ".$pourcentage." %"."<br>";
	echo "Maximum prevu de la table  : ".$row['Max_data_length']."<br>";
	echo "<hr>";
}
?>