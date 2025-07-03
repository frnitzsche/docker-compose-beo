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

if (!isset($_SESSION["username"])) {header("Location: ../../../");  exit;}
$remote_ID=$_SERVER["REMOTE_ADDR"]."&u=".@$_POST["username"]."&p=".@$_SERVER["HTTP_X_FORWARDED_FOR"];
if ($_SESSION["IP"] == $remote_ID) {header("Location: ../../../"); exit;}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>title</title>
<meta http-equiv="content-type" content="text/html; charset=windows-1251">
<style><!--

--></style>
</head>
<body leftmargin="10" topmargin="10" marginwidth="10" marginheight="10">
<form action=../output/ method=post target=output>
<input type=text size=20 name=input><input type=submit value=Enter>
</form>
</body>
</html>