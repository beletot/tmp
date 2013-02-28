<?php
10.31 ftydef azef ydf tyaz etyd tyey


$date = new DateTime('27-02-2013');
echo $date->format('dm');
die();
//1 Hour = 3600000 Milliseconds 
echo date('d-m-Y h:i',1321347330).'<br />';
echo date('h:i',1321347330).'<br />';

function getTimeDifference($time1, $time2) {
    $dif = max($time1, $time2) - min($time1, $time2);
    return array(
        "years"  => floor($dif/31536000),
        "weeks"  => floor(($dif%31536000)/604800),
        "days"   => floor((($dif%31536000)%604800)/86400),
        "hours"  => floor(($dif%86400)/3600),
        "minutes"=> floor(($dif%3600)/60),
        "seconds"=> $dif%60,
        
        "earlier" => min($time1, $time2),
        "later"   => max($time1, $time2)
    );    
} 
function formatTimeDifference($dif) {
    $ret = "";
    $order = array("Years", "Weeks", "Days", "Hours", "Minutes", "Seconds");
    foreach($order as $part) {
        $v = $dif[strtolower($part)];
        if ($v != 0) {
            $ret .= strlen($ret) > 0 ? " " : "";
            $ret .= $v." ";
            $ret .= $v==1 ? substr($part, 0, -1) : $part;
        }
    }
    return $ret;
}   

echo "<pre>";

// First - the times
$time1 = 1321347782;
$time2 = 1321340400;

// Usage (doesn't matter which time is the earlier and which is the later)
$diff = getTimeDifference($time1 ,$time2);

// Output
echo "Difference between\n  &middot; ".date('l dS \of F Y h:i:s A', $diff['earlier'])." and\n  &middot; ".date('l dS \of F Y h:i:s A', $diff['later'])."\nis:\n\n";
print_r($diff);
echo "\n".formatTimeDifference($diff);

echo "</pre>";
?>

?>