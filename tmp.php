<?php
$source = '^$';
$trans = array(
		"$"=>"1",
		"^"=>"2"
		);
$cleanSource = strtr($source, $trans);
echo $cleanSource;

?>
