<?php
ignore_user_abort(1);
ini_set("session.name","sid"); 
$session_name=session_name();
if (isset($_COOKIE[$session_name])) 
   {
    ini_set("session.use_trans_sid","0");
    $cookies=1;
   }
else 
   {
    ini_set("session.use_trans_sid","1");
    $cookies=0;
   }
if (eregi("opera 3.60",@$_SERVER["HTTP_USER_AGENT"])) {$opera360=1;} else {$opera360=0;}
session_start();

 if (!isset($_SESSION["username"])) {header("Location: ../../"); echo("ass"); exit;}
 $remote_ID=$_SERVER["REMOTE_ADDR"]."&u=".@$_POST["username"]."&p=".@$_SERVER["HTTP_X_FORWARDED_FOR"];
 if ($_SESSION["IP"] == $remote_ID) {header("Location: ../../"); exit;}

?>
<frameset rows=40,*>
  <frame src=./input/ scrolling=no name=input>
  <frame src=./output/?jhj name=output name=output>
</frameset>