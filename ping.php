<?php
$str = exec("ping -n 1 -w 1 10.1.100.248", $input, $error);
if ($error == 0){
echo "okay";
}else{
echo "false";
}
echo '<pre>'.print_r($input,true).'</pre>';
echo '<pre>'.print_r($error,true).'</pre>';
?>