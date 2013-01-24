<?php
$i = 0;

$nodeName['parent'] = 'tickets';
$nodeName['child'] =  'ticket';
$line = new stdClass;
$line->name = 'denis';
$line->firstname = 'raoul';
$rows = array($line);

$dom = new DOMDocument('1.0','utf-8');
/*** make the output tidy ***/
$dom -> formatOutput = true;
/*** create the root element ***/
$dom -> appendChild($dom -> createElement($nodeName['parent']));

$tickets = simplexml_import_dom($dom);
// TODO créer les codes

//adding ticket node
if ($rows) {
	foreach ($rows as $row) {
		$ticket = $tickets -> addChild($nodeName['child']);
		foreach ($row as $key => $value) {
			$ticket -> addChild($key, $value);
		}
		$i++;
	}
	$statut = '202';
} else {
	$statut = '404';
}

$tickets -> addAttribute('statut', $statut);
$tickets -> addAttribute('count', $i);
$xml = $tickets -> asXML();
echo header( "content-type: application/xml; charset=utf_8" );
echo $xml;
?>