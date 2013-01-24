<?php

function check_passwd() {
	$username = 'bletot';
	$password = 'letotb';

	// Change made for LDAP Auth  based on -> http://osticket.com/forums/showthread.php?t=3312
	// Change this line to the FQDN of your domain controller
	$ds = ldap_connect('epfc01afs01.epfc.local',389) or die("Couldn't connect to AD!");

	// Change this line to the name of your Active Directory domain
	if ($ds) {
		echo 'connected to ldap <br />';
		$domain = "epfc";
		$ldapbind = ldap_bind($ds);
		if (!@ldap_bind($ds, $domain . "\\" . $username, $password)) {
			// Auth failed! lets try at osTicket database
			echo 'password not checked';
			// return(FALSE);
		} else {
			// Auth succeeded!
			echo 'password checked';
		}
	}
}
check_passwd();
?>
