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

if (!isset($_SESSION["username"])) {header("Location: ../../"); exit;}
$remote_ID=$_SERVER["REMOTE_ADDR"]."&u=".@$_POST["username"]."&p=".@$_SERVER["HTTP_X_FORWARDED_FOR"];
if ($_SESSION["IP"] == $remote_ID) {{header("Location: ../../"); exit;}}

if ($_GET["download"]) 
   {
    eregi("/?([^/]+)$",$_GET["download"],$file);
    eregi("\.?([^\.]*)$",$file[1],$ext);
    if ($ext[1] == "html" || $ext[1] == "htm" || $ext[1] == "text" || $ext[1] == "log") 
       {
        header("Content-Disposition: inline; filename=".$file[1]);
        header("Content-type:  text/html");
        header("Content-length: ".(string)(filesize($_GET["download"])));
        $fp=fopen($_GET["download"],"rb");
        fpassthru($fp);
       }
    else
       {
        header("Content-Disposition: attachment; filename=".$file[1]);
        header("Content-type: application/octet-stream");
        header("Content-length: ".(string)(filesize($_GET["download"])));
        $fp=fopen($_GET["download"],"rb");
        fpassthru($fp);
       }
   }

?>