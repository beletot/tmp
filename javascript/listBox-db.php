<?php
//get data from the mootools request
$deptId = (int)$_POST['dept_id'];
if (!$deptId) {
	return false;
}

$link = mysql_connect('localhost', 'root', '');
if (!$link) {
	die('Impossible de se connecter : ' . mysql_error());
}
if (!mysql_select_db('osticket-1.6')) {
	die('Impossible de sélectionner la table : ' . mysql_error());
}

$result = mysql_query('SELECT topic_id as id, topic as name FROM ost_help_topic where dept_id = ' . $deptId);
if (!$result) {
	die('Impossible d\'exécuter la requête :' . mysql_error());
}

$string = '<select name="topicId">
		<option selected="" value="">Select One</option>';

while ($row = mysql_fetch_object($result)) {
	$string .= '<option value="' . $row -> id . '">' . $row -> name . '</option>';
}
$string .= '</select>';
echo utf8_encode($string);
mysql_free_result($result);
mysql_close($link);
?>
