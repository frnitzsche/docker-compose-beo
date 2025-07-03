<?php
ignore_user_abort(1);
ini_set("session.name","sid"); 

function rotate_session()
   {
    global $session_name,$cookies;
    if ($cookies) 
       {
        $old_id=$_COOKIE[$session_name];
       }
    else 
       {
        if (isset($_GET[$session_name]))
           {
            $old_id=$_GET[$session_name];
           }
        else
           {
            return;
           }
       }
    $path=ini_get("session.save_path");
    $new_id=md5(uniqid(rand(),1));
    $ok=@rename($path."\\sess_".$old_id,$path."\\sess_".$new_id);
    if ($ok) 
       {
        session_id($new_id);
       }
    else
       {
        return;
       }
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
if ($cookies && !$opera360) {rotate_session();}
session_start();

if (!isset($_SESSION["username"])) {header("Location: ../"); exit;}
$remote_ID=$_SERVER["REMOTE_ADDR"]."&u=".@$_POST["username"]."&p=".@$_SERVER["HTTP_X_FORWARDED_FOR"];
if ($_SESSION["IP"] == $remote_ID) {{header("Location: ../"); exit;}}

if (@$_GET["js"])
   { 
    $_SESSION["js"]=1 ?>
    <frameset cols=50%,50% rows=30,*>
      <frame src=./bar/?w=A&js=yes name=bar_A scrolling=no>
      <frame src=./bar/?w=B&js=yes name=bar_B scrolling=no>
      <frame src=./main/?w=A&js=yes name=main_A scrolling=yes>
      <frame src=./main/?w=B&js=yes name=main_B scrolling=yes>
    </frameset> <?php
    exit;
   }
else
   { ?>
<script language=JavaScript><!--
window.location='./?js=yes';
document.write("<html>");
//--></script>
    <frameset cols=50%,50%>
      <frame src=./main/?w=A name=main_A scrolling=yes>
      <frame src=./main/?w=B name=main_B scrolling=yes>
    </frameset> <?php
   }
?>





