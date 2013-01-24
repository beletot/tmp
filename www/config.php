<?php  /// Moodle Configuration File
## debug bertrand
/*session_start();
$_SESSION['favcolor'] = 'green';
$_SESSION['time']     = time();*/
//unset ($_SESSION);


//error_reporting(E_ALL ^ E_NOTICE);
//echo '<pre>'.print_r($current_error_reporting,true).'</pre>';
## end debug
//die();

//echo '<div style="left:600px;top:5px;position:fixed;background-color:white;padding:5px;border-radius: 10px;">DEBUG index.php/ file etc windows<br />'.$_SERVER['SERVER_ADDR'].'</div>';

//be
define('DS', DIRECTORY_SEPARATOR);
function jsd($v)
{
	echo
		'<script type = "text/javascript">
			console.log(' . json_encode($v) . ');
		</script>';
}

unset($CFG);

$CFG = new stdClass();

$CFG->anneeSD = '2012';

$CFG->debut = microtime(true);
//db moodle
//TODO create user mysql
$CFG->dbtype    = 'mysql';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'epfc';
$CFG->dbuser    = 'root';
$CFG->dbpass    = 'iXViTxFr';
$CFG->dbpersist =  false;
$CFG->prefix    = '';

$CFG->wwwroot   = 'http://www.e-pfc.eu';
$CFG->dirroot   = '/home/epfc/www';
$CFG->dataroot  = '/home/epfc/moodledata';
$CFG->admin     = 'admin';

//$CFG->dirE_PFC = $CFG->dirroot . "/e-pfc";
//$CFG->dirLibE_PFC = $CFG->dirroot . "/e-pfc/Lib";
//require_once($CFG->dirLibE_PFC . "/bd.php");

$CFG->directorypermissions = 00777;  // try 02777 on a server in Safe Mode
//$CFG->directorypermissions = 00705;  // try 02777 on a server in Safe Mode
$CFG->passwordsaltmain = '';

$CFG->autologinMD5Salt = 'aEf456!%';

$CFG->resource_hide_text = true;
$CFG->resource_hide_ims = true;

$CFG->assignment_hide_offline = true;
$CFG->assignment_hide_uploadsingle = true;

require_once("$CFG->dirroot/lib/setup.php");
error_reporting(E_ALL);

// MAKE SURE WHEN YOU EDIT THIS FILE THAT THERE ARE NO SPACES, BLANK LINES,
// RETURNS, OR ANYTHING ELSE AFTER THE TWO CHARACTERS ON THE NEXT LINE.
