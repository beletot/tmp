<?php

$link = mysql_connect('localhost', 'root', '');
if (!$link) {
   die('Impossible de se connecter : ' . mysql_error());
}

// Rendre la base de données foo, la base courante
$db_selected = mysql_select_db('tmp', $link);
if (!$db_selected) {
   die ('Impossible de sélectionner la base de données : ' . mysql_error());
}

if (is_numeric($_GET['id']) && isset($_GET['content'])) {
	$query = "UPDATE edit_ajax SET content = '" . mysql_real_escape_string(stripslashes($_GET['content'])) . "' WHERE id = " . (int)$_GET['id'];
	error_log($query);
	if(!$result = mysql_query($query, $link)){
	};
}
mysql_close($link);
?>