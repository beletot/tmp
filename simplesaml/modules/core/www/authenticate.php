<?php



$config = SimpleSAML_Configuration::getInstance();
$session = SimpleSAML_Session::getInstance();
/*$included_files = get_included_files();
echo '$included_files <pre>'.print_r($included_files,true).'</pre>';
echo 'session <pre>'.print_r($session,true).'</pre>';
die();*/


if(array_key_exists('logout', $_REQUEST)) {
	SimpleSAML_Auth_Default::initLogout('/' . $config->getBaseURL() . 'logout.php');
}

if (array_key_exists(SimpleSAML_Auth_State::EXCEPTION_PARAM, $_REQUEST)) {
	/* This is just a simple example of an error. */

	$state = SimpleSAML_Auth_State::loadExceptionState();
	assert('array_key_exists(SimpleSAML_Auth_State::EXCEPTION_DATA, $state)');
	$e = $state[SimpleSAML_Auth_State::EXCEPTION_DATA];

	header('Content-Type: text/plain');
	echo "Exception during login:\n";
	foreach ($e->format() as $line) {
		echo $line . "\n";
	}
	exit(0);
}

if(!array_key_exists('as', $_REQUEST)) {
	$t = new SimpleSAML_XHTML_Template($config, 'core:authsource_list.tpl.php');

	$t->data['sources'] = SimpleSAML_Auth_Source::getSources();
	$t->show();
	exit();
}

$as = $_REQUEST['as'];

if (!$session->isValid($as)) {
	$url = SimpleSAML_Utilities::selfURL();
	$hints = array(
		SimpleSAML_Auth_State::RESTART => $url,
	);
	SimpleSAML_Auth_Default::initLogin($as, $url, $url, $hints);
}

$attributes = $session->getAttributes();

$t = new SimpleSAML_XHTML_Template($config, 'status.php', 'attributes');

$t->data['header'] = '{status:header_saml20_sp}';
$t->data['remaining'] = $session->remainingTime();
$t->data['sessionsize'] = $session->getSize();
$t->data['attributes'] = $attributes;
$t->data['logouturl'] = SimpleSAML_Utilities::selfURLNoQuery() . '?logout';
$t->data['icon'] = 'bino.png';
$t->show();

