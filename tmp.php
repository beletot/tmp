<?php

$query = 'fVLLTuswEN0j8Q+W93m1G7CaoAJCVOIR0XAX7FxnkhjscfA4Lfw9aQqCu4Dt8ZnzGM/i7M0atgVP2mHOszjlDFC5WmOb88fqKjrhZ8Xx0YKkNb1YDqHDB3gdgAIbJ5HE9JDzwaNwkjQJlBZIBCXWy9sbMYtT0XsXnHKGs9VlzrFpjTVgYdNp86zqjcF+A88KXuq+frF102KHbYec/fuKNdvHWhENsEIKEsMIpdk8SrNonlXpqUhPxGz+xFn56XSu8dDgr1ibA4nEdVWVUXm/riaBra7B343snLfOtQZi5ezevpREejvCjTQEnC2JwIcx4IVDGiz4NfitVvD4cJPzLoSeRJLsdrv4WyaRCfSNimFIpCJeTGsVUzP/Y59/55Zfvrz4Vl4kP6SKz+/at1hdls5o9c6WxrjdhQcZxgrBD2ODK+etDL+7ZXE2IbqOmokqBqQelG401JwlxcH1/7sYr+UD';

$base64 = base64_encode($query);
echo $query.'<hr />';
echo $base64;

die();
//youtube
$query = '&SAMLRequest=fVLJTsMwEL0j8Q%2BR79mKEMhqggoIUYkloikHbsaZJgbbEzxOC3%2BPmxYBB7g%2Bv3nLeKZn70ZHa3Ck0BYsTzIWgZXYKNsWbFlfxafsrDw8mJIwuuezwXf2Ad4GIB%2BFSUt8fCjY4CxHQYq4FQaIe8kXs9sbPkky3jv0KFGzaH5ZMI0NIorW6lcAaNvOvPTdSysNgu3AAGh4lY0O7MevWJNtrDnRAHNLXlgfoCw%2FirM8nhzXecaPTnl%2B8sSiau90ruyuwX%2Bxnnck4td1XcXV%2FaIeBdaqAXcX2AVrEVsNiUSzta8EkVoHeCU0AYtmROB8CHiBlgYDbgFurSQsH24K1nnfE0%2FTzWaTfMukIoV%2BJRMYUiGJleNa%2BdjM%2Fdjn%2F7nFly8rv5Wn6Q%2Bpcv9d2xbzywq1kh%2FRTGvcXDgQPlTwbggNrtAZ4f92y5N8RFQTr0YqHyz1INVKQcOitNy5%2Fr6LcC2f&RelayState=https%3A%2F%2Faccounts.google.com%2FCheckCookie%3Fcontinue%3Dhttps%253A%252F%252Fwww.youtube.com%252Fsignin%253Faction_handle_signin%253Dtrue%2526feature%253Dsign_in_button%2526hl%253Dfr_FR%2526next%253D%25252F%25253Ftab%25253Dm1%2526nomobiletemp%253D1%26hl%3Dfr_FR%26service%3Dyoutube';
//$query = '&SAMLRequest=fVJNTxsxEL1X4j9Yvu9XKKKysotCItRI0K7IwoGb4x07Jl578XiT9t%2FX2SSCHuD6%2FOZ9jGd686czZAcetbMlLdKcErDCtdqqkj41d8kPelNdfJsi70zPZkPY2Ed4GwADiZMW2fhQ0sFb5jhqZJZ3gCwItpo93LNJmrPeu%2BCEM5QsFyVdq36zVdDarexeuVQqCshWQKe2ABvZSsdVb9aCkudzrMkh1hJxgKXFwG2IUF5cJnmRTK6a%2FJpNvrP88oWS%2BuR0q%2B2xwVex1kcSsp9NUyf171UzCux0C%2F5XZJdUOacMpMJ1B%2FuaI%2BpdhCU3CJTMEMGHGHDuLA4d%2BBX4nRbw9Hhf0k0IPbIs2%2B%2F36btMxjPopUhhyLhAWo1rZWMz%2F2GfX%2BfmZ19avStPsw9S1em7Di2Wi9oZLf6SmTFuP%2FfAQ6wQ%2FBAb3Dnf8fC5W5EWI6LbRI5UNljsQWipoaUkq46u%2F99FvJZ%2F&RelayState=https%3A%2F%2Fwww.google.com%2Fa%2Fepfc.eu%2FServiceLogin%3Fservice%3Dmail%26passive%3Dtrue%26rm%3Dfalse%26continue%3Dhttps%253A%252F%252Fmail.google.com%252Fa%252Fepfc.eu%252F%26ss%3D1%26ltmpl%3Ddefault%26ltmplcache%3D2';

$query = urldecode($query);
foreach (explode('&', $query) as $chunk) {
    $param = explode("=", $chunk);

    if ($param) {
        printf("La valeur du paramètre \"%s\" est \"%s\"<br/>\n", urldecode($param[0]), urldecode($param[1]));
    }
}
exit;
echo $query;
echo '<hr>';
echo urldecode($query);

echo '<pre>'.print_r($_SERVER,true).'</pre>';

echo $_SERVER['QUERY_STRING'].'<br />';

?>
