<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>

	<body>
		<?php
		//echo chr($ascii);
		$string = 'º Burešová Kristýna)';
		echo $string . '<br />';
		echo utf8_encode($string) . '<br />';
		echo utf8_decode($string) . '<br />';
		echo str_replace('º', '°', $string) . '<br />';
		echo utf8_decode($string) . '<br />';
		//echo htmlentities($string) . '<br />';
		echo htmlspecialchars($string) . '<br />';
		echo htmlspecialchars_decode($string) . '<br />';
		?>
	</body>
</html>
