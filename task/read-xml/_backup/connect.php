<?php
date_default_timezone_set('Europe/Brussels');
$dateTime =  date('l jS \of F Y h:i:s A');

//mail('bertrandletot@gmail.com', 'serveur dev task manager', 'task '.$dateTime);


if (function_exists('ibase_connect')) {
    echo "ibase_connect functions are available.<br />\n";
} else {
    echo "ibase_connect functions are not available.<br />\n";
}

$path = 'isis:c:\epfc1011.fdb';
/***	host	***/
$ip = gethostbyname('isis');

echo '<p>IpAdresss '.$ip.'</p>';


//$path = 'epfc01ats01:c:\epfc1011.fdb';
$db = ibase_connect($path, "sysdba", "epfccfpe");
print_r($db);		
if(!$db){
			print_r($db);			
			$return->error = 1;
        		$return->comment = "Impossible de se connecter : ";
        	echo $return->comment;
        	//return $return;
		}else{
	echo 'connect db okay';
	mail('bertrandletot@gmail.com', 'serveur dev task manager', 'task '.$dateTime);
}
?>