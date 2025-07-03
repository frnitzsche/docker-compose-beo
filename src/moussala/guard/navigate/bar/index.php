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
if ($_SESSION["IP"] == $remote_ID) {{header("Location: ./../../"); exit;}}

eregi("^([^:]):(.+)",$_SERVER["DOCUMENT_ROOT"],$data);
$data[1]=strtoupper($data[1]);
$letters=Array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");

$drives[0]="A";
$drives[1]="B";
$count_arr=count($letters);

for ($i=2;$i<$count_arr;$i++)
    {
     $letter=$letters[$i];
     $dp=@opendir($letter.":/");
     if (!$dp && $i>1)
        {
         break;
        }
     else
        {
         $drives[$i]=$letter;
        }
    }
echo("<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.0 Transitional//EN'><html><head>
<script language=JavaScript><!--
function do_it(No)
   {
    if (top.main_".$_GET["w"].".document.forms[0].action[No]) 
       {
        top.main_".$_GET["w"].".document.forms[0].action[No].click();
       }
   }
//--></script>
</head><body bgcolor=silver leftmargin=0 topmargin=0 marginwidth=0 marginheight=0>");
echo("<form action=../main/?w=".$_GET["w"]." target=main_".$_GET["w"]." method=get><table border=0 cellpadding=0 cellspacing=0 width=100% height=100%><tr><td>");
echo("<select name=drive onchange=top.main_".$_GET["w"].".location='../main/?w=".$_GET["w"]."&drive='+document.forms[0].drive.value+'&rand='+Math.random()+'';>");
for ($i=0;$i<count($drives);$i++) {echo("\r\n<option value=$drives[$i] ".($drives[$i]==$data[1]?"selected":"no").">$drives[$i]</option>");}
echo("\r\n</select><input type=hidden name=w value=".$_GET["w"]."><input type=submit value=GO>");
echo("<input type=button value=Copy onclick='do_it(0)'><input type=button onclick='do_it(1)' name=action value=Move><input type=button onclick='do_it(2)' name=action value=Delete><input type=button onclick='do_it(3)' name=action value=Rename><input type=button onclick='do_it(4)' name=action value=\"New DIR\"><input type=button name=action value=Upload onclick='do_it(5)'><input type=button name=action value=Refresh onclick='do_it(6)'>");
echo("</td></tr></table></form></body></html>");
?>