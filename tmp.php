<?php
$url = '&SAMLRequest=fVJNTxsxEL1X4j9Yvu9XKKKysotCItRI0K7IwoGb4x07Jl578XiT9t%2FX2SSCHuD6%2FOZ9jGd686czZAcetbMlLdKcErDCtdqqkj41d8kPelNdfJsi70zPZkPY2Ed4GwADiZMW2fhQ0sFb5jhqZJZ3gCwItpo93LNJmrPeu%2BCEM5QsFyVdq36zVdDarexeuVQqCshWQKe2ABvZSsdVb9aCkudzrMkh1hJxgKXFwG2IUF5cJnmRTK6a%2FJpNvrP88oWS%2BuR0q%2B2xwVex1kcSsp9NUyf171UzCux0C%2F5XZJdUOacMpMJ1B%2FuaI%2BpdhCU3CJTMEMGHGHDuLA4d%2BBX4nRbw9Hhf0k0IPbIs2%2B%2F36btMxjPopUhhyLhAWo1rZWMz%2F2GfX%2BfmZ19avStPsw9S1em7Di2Wi9oZLf6SmTFuP%2FfAQ6wQ%2FBAb3Dnf8fC5W5EWI6LbRI5UNljsQWipoaUkq46u%2F99FvJZ%2F&RelayState=https%3A%2F%2Fwww.google.com%2Fa%2Fepfc.eu%2FServiceLogin%3Fservice%3Dmail%26passive%3Dtrue%26rm%3Dfalse%26continue%3Dhttps%253A%252F%252Fmail.google.com%252Fa%252Fepfc.eu%252F%26ss%3D1%26ltmpl%3Ddefault%26ltmplcache%3D2';
echo $url;
echo '<hr>';
echo urldecode($url);

echo '<pre>'.print_r($_SERVER,true).'</pre>';

echo $_SERVER['QUERY_STRING'].'<br />';

?>
