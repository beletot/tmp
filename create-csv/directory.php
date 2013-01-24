<?php
// Ouverture de la base de données qui contient les adresses 
$fp = fopen('file.csv', 'w');

$db= mysql_connect( "localhost" , "root" , "" ) or die ("erreur de connexion base 1");
mysql_select_db('extranet-epfc',$db) or die ("erreur de connexion base 2");
$rows=mysql_query("select name, firstname, concat(login, '@epfc.eu') as email from jos_directory LIMIT 0,2 "); 
while($row = mysql_fetch_assoc($rows))
  {
  	echo '<pre>'.print_r($row,true).'</pre>';
  	fputcsv($fp, $row, ';', '"');
  }
fclose($fp);