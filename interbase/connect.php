<?php
date_default_timezone_set('Europe/Brussels');
$dateTime =  date('l jS \of F Y h:i:s A');

//mail('bertrandletot@gmail.com', 'serveur dev task manager', 'task '.$dateTime);


if (function_exists('ibase_connect')) {
    echo "ibase_connect functions are available.<br />\n";
} else {
    echo "ibase_connect functions are not available.<br />\n";
}
$db = ibase_connect("isis:c:\epfc1213.fdb", "sysdba", "epfccfpe");
//$db = ibase_connect("epfc01dev01:c:\epfc1213-be.fdb", "sysdba", "epfccfpe");
//$db = ibase_connect("epfc01dev01:c:\epfc1213test.fdb", "sysdba", "epfccfpe");
		if(!$db){
			$return->error = 1;
        	$return->comment = "Impossible de se connecter : " . mysql_error();
        	echo $return->comment;
        	//return $return;
		}else{
	echo 'connect db okay';
}
$query = "select first 5 C.NO_CLASSE, C.SIGLE, C.ID_ECOLE, C.ID_UF, C.ID_ORG, C.ID_SECTION, C.PERIODE_JOURNEE, coalesce(CH.INTITULE, UF.DENOM) INTITULE, C.ID_UF_ALTERNATIVE, C.BATIMENT from CLASSES C join UF on C.ID_UF = UF.ID_UF left join CATEGORIES_HORAIRE CH on C.ID_UF = CH.ID_CATEGORIE_HORAIRE 
			";
$rows = ibase_query($query);
		//$return = array();
	while ($result = ibase_fetch_object($rows)) {
        echo '<pre>'.print_r($result,true).'</pre>';
	}
?>