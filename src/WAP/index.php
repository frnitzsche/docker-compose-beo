<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

header("Content-type: text/vnd.wap.wml");
echo("<?xml version=\"1.0\"?>");
echo "<!DOCTYPE wml PUBLIC \"-//WAPFORUM//DTD WML 1.1//EN\""
   . " \"http://www.wapforum.org/DTD/wml_1.1.xml\">";


$host="localhost";
$username="wap";
$password="wap";
$database="moussala";

$link=mysql_connect($host,$username,$password);
if (!$link) {
   error("No connection to the $host ",$mysql_error_message);
}
$OK=mysql_select_db("moussala",$link);
if (!$OK) {
   error("Can not select database $database ",$mysql_error_message);
}


$query="SELECT data from temperature order by timestamp desc limit 1";
$result=mysql_query($query,$link);
if (!isset($result) || !$result || mysql_num_rows($result) != 1) {
   error("query- $query - didn't broght expected the result");
}
$temperature=mysql_fetch_array($result);


$query="SELECT data from pressure order by timestamp desc limit 1";
$result=mysql_query($query,$link);
if (!isset($result) || !$result || mysql_num_rows($result) != 1) {
   error("query- $query - didn't broght expected the result");
}
$pressure=mysql_fetch_array($result);


$query="SELECT data from wind_velocity order by timestamp desc limit 1";
$result=mysql_query($query,$link);
if (!isset($result) || !$result || mysql_num_rows($result) != 1) {
   error("query- $query - didn't broght expected the result");
}
$wind_velocity=mysql_fetch_array($result);


$query="SELECT data from wind_direction order by timestamp desc limit 1";
$result=mysql_query($query,$link);
if (!isset($result) || !$result || mysql_num_rows($result) != 1) {
   error("query- $query - didn't broght expected the result");
}
$wind_direction=mysql_fetch_array($result);


$query="SELECT data from humidity order by timestamp desc limit 1";
$result=mysql_query($query,$link);
if (!isset($result) || !$result || mysql_num_rows($result) != 1) {
   error("query- $query - didn't broght expected the result");
}
$humidity=mysql_fetch_array($result);


$query="SELECT data from precipitation order by timestamp desc limit 1";
$result=mysql_query($query,$link);
if (!isset($result) || !$result || mysql_num_rows($result) != 1) {
   error("query- $query - didn't broght expected the result");
}
$precipitation=mysql_fetch_array($result);


$query="SELECT data from neutrons order by timestamp desc limit 1";
$result=mysql_query($query,$link);
if (!isset($result) || !$result || mysql_num_rows($result) != 1) {
   error("query- $query - didn't broght expected the result");
}
$neutrons=mysql_fetch_array($result);


$query="SELECT data from gamma order by timestamp desc limit 1";
$result=mysql_query($query,$link);
if (!isset($result) || !$result || mysql_num_rows($result) != 1) {
   error("query- $query - didn't broght expected the result");
}
$gamma=mysql_fetch_array($result);


$query="SELECT data from no order by timestamp desc limit 1";
$result=mysql_query($query,$link);
if (!isset($result) || !$result || mysql_num_rows($result) != 1) {
   error("query- $query - didn't broght expected the result");
}
$no=mysql_fetch_array($result);


$query="SELECT data from no2 order by timestamp desc limit 1";
$result=mysql_query($query,$link);
if (!isset($result) || !$result || mysql_num_rows($result) != 1) {
   error("query- $query - didn't broght expected the result");
}
$no2=mysql_fetch_array($result);


$query="SELECT data from o3 order by timestamp desc limit 1";
$result=mysql_query($query,$link);
if (!isset($result) || !$result || mysql_num_rows($result) != 1) {
   error("query- $query - didn't broght expected the result");
}
$o3=mysql_fetch_array($result);


$query="SELECT data from uv_ab order by timestamp desc limit 1";
$result=mysql_query($query,$link);
if (!isset($result) || !$result || mysql_num_rows($result) != 1) {
   error("query- $query - didn't broght expected the result");
}
$uv_ab=mysql_fetch_array($result);



echo("
<wml>
<card id='beo' title='BEO Database'>
<p>Besic Environmental Observatory</p>
<p>CURRENT DATA</p>
<p>Temperature $temperature[0] Deg C</p>
<p>Pressure $pressure[0] HPa</p>
<p>Wind Velocity $wind_velocity[0] m/s</p>
<p>Wind Direction $wind_direction[0] Deg</p>
<p>Humidity $humidity[0] %</p>
<p>Precipitations $precipitation[0] mm</p>
<p>Neutrons $neutrons[0] nSv/h</p>
<p>Gamma $gamma[0] nSv/h</p>
<p>NO $no[0] ppb</p>
<p>NO2 $no2[0] ppb</p>
<p>O3 $o3[0] ppb</p>
<p>UV-AB $uv_ab[0] W/m2</p>
<p>
<anchor title='LINK'>Cameras
 <go href='./cameras'>
 </go>
</anchor>
</p>
</card>
</wml>
");
?>