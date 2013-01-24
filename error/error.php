<?php
function inverse($x) {
    if (!$x) {
        throw new Exception('Division par zéro.');
    }
    else return 1/$x;
}
function writeFile ($fileName){
	echo 'init writeFile <br />';
	$canWrite = 1;
	if(!$fileName){
		throw new Exception('No file Name');
	}
	if($canWrite == 0){
		throw new Exception('Can\'t write');
	}
}

try {
	inverse(0);
    writeFile ('voiture');
} catch (Exception $e) {
    echo 'Exception reçue : ',  $e->getMessage(), '<br />';
}
?>
