<?php 

#################  This is session template  ####################

ignore_user_abort(1);
ini_set("session.name","sid"); 

@session_start();

if (!isset($_SESSION["username"])) {header("Location: http://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/guard/authenticate/"); exit;}
$remote_ID=$_SERVER["REMOTE_ADDR"]."&u=".@$_POST["username"]."&p=".@$_SERVER["HTTP_X_FORWARDED_FOR"];
// if ($_SESSION["IP"] == $remote_ID) {{header("Location: http://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/guard/authenticate/"); exit;}}

?>