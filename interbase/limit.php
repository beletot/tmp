<?php
date_default_timezone_set('Europe/Brussels');
$dateTime = date('l jS \of F Y h:i:s A');

//mail('bertrandletot@gmail.com', 'serveur dev task manager', 'task '.$dateTime);

if (function_exists('ibase_connect')) {
	echo "ibase_connect functions are available.<br />\n";
} else {
	echo "ibase_connect functions are not available.<br />\n";
}
$db = ibase_connect("isis:c:\epfc1112.fdb", "sysdba", "epfccfpe");
if (!$db) {
	$return -> error = 1;
	$return -> comment = "Impossible de se connecter : " . mysql_error();
	echo $return -> comment;
	//return $return;
} else {
	echo 'connect db okay';
}
$t = time();
$i = 0;

//ORDER BY NO_INSC ROWS 6 TO 15 
//SELECT FIRST 200 SKIP 200

$query = "SELECT FIRST 50 SKIP 300000
	  		apu.NO_INSC
			FROM
	  		ARCHIVE_POINTS_UF apu
			ORDER BY apu.NO_INSC ASC
			";
$rows = ibase_query($query);
//$return = array();
while ($result = ibase_fetch_object($rows)) {
	echo '<pre>' . print_r($result, true) . '</pre>';
	$i++;
}
$t = time() - $t;
echo $i." records en ".$t.' secondes.';
?>