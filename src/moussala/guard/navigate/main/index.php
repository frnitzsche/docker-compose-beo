<?php
ignore_user_abort(1);
set_time_limit(120);
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
if (eregi("opera 3.60",@$_SERVER["HTTP_USER_AGENT"])) {$old_browser=1;} else {$old_browser=0;}
session_start();

if (!isset($_SESSION["username"])) {header("Location: ../../"); exit;}
$remote_ID=$_SERVER["REMOTE_ADDR"]."&u=".@$_POST["username"]."&p=".@$_SERVER["HTTP_X_FORWARDED_FOR"];
if ($_SESSION["IP"] == $remote_ID) {{header("Location: ../../"); exit;}}

if (isset($_POST["drive"]) && isset($_POST["change_drive"])) {$_GET["drive"]=urldecode($_POST["drive"]); $_GET["path"]="/";}
if (!isset($_GET["drive"]) && !isset($_GET["path"]))
   {
    eregi("^([^:]+):(.+)",$_SERVER["DOCUMENT_ROOT"],$data);
    $_GET["drive"]=$data[1];
    $_GET["path"]=$data[2]."/";
   }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>title</title>
<meta http-equiv="content-type" content="text/html; charset=windows-1251">

<style><!--
a {color=#000000; text-decoration:none;}
a.dir {color:#0000cc; text-decoration:none; font-weight:bold;}
.back {color:#9999ff;}
.path {color:gray; font-weight:bold; text-size:+2;}
tr {background:#d0d0d0;}
td {font-size:10px; font-family:verdana;}
--></style>
<script language=JavaScript><!--
function on_load()
   {
    if (document.forms[0].new_dir) {document.forms[0].new_dir.focus();}
    if (document.forms[0].upload_file) {document.forms[0].upload_file.focus();}
    if (document.all.upper_bar) {document.all.upper_bar.style.display='none';}
    if (document.all.top_bar) {document.all.top_bar.style.display='none';}
   }
function do_it()
   {
    setTimeout("<?php echo("top.main_".($_GET["w"]=="A"?"B":"A").".location='../main/?w=".($_GET["w"]=="A"?"B":"A")."&Refresh=yes'"); ?>",300);
   }
//--></script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="on_load();">

<?php
if (@$_POST["action"] == "Delete") 
   {
    if (isset($_POST["unit_dir"]))
       {
        foreach(@$_POST["unit_dir"] as $key=>$value)
          {
           @rmdir($_SESSION["dir_window_".(@$_GET["w"])].urldecode($value)."/");
          }
       }
    if (isset($_POST["unit_file"]))
       {
        foreach(@$_POST["unit_file"] as $key=>$value)
          {
           @unlink($_SESSION["dir_window_".@$_GET["w"]].urldecode($value));
          }
       }
   }
elseif (@$_POST["action"] == "Rename")
   {
    if (isset($_POST["rename_from"]) && isset($_POST["rename_to"]))
       {
        $rename_from=urldecode($_POST["rename_from"]);
        $rename_to=$_GET["drive"].":".$_GET["path"].urldecode($_POST["rename_to"]);
        @rename($rename_from,$rename_to);
       }
    else
       {
        echo("<form action=./?w=".@$_GET['w']."&drive=".@$_GET['drive']."&path=".urlencode(@$_GET['path'])." method=post>");
        if (isset($_POST["unit_dir"]))
           {
            echo("<font color=gray face=verdana size=4>Rename directory <font color=#000088><b>".urldecode($_POST["unit_dir"][0])."/</b></font>"); 
            echo(" <nobr>to </font> <input type=text size=16 name=rename_to>&nbsp;&nbsp;<input type=submit name=action value=Rename></nobr><input type=hidden name=rename_from value=\"".$_GET["drive"].":".$_GET["path"].urldecode($_POST["unit_dir"][0])."\">");
            echo(" &nbsp;<input type=submit name=action value=Cancel>");
            echo("</form></body></html>");
            exit;
           }
        elseif (isset($_POST["unit_file"]))
           {
            echo("<font color=gray face=verdana size=4>Rename file <font color=#000088><b>".urldecode($_POST["unit_file"][0])."</b></font>"); 
            echo(" <nobr>to </font> <input type=text size=16 name=rename_to>&nbsp;&nbsp;<input type=submit name=action value=Rename></nobr><input type=hidden name=rename_from value=\"".$_GET["drive"].":".$_GET["path"].urldecode($_POST["unit_file"][0])."\">");
            echo(" &nbsp;<input type=submit name=action value=Cancel>");
            echo("</form></body></html>");
            exit;
           }
       }
   }
elseif (@$_POST["action"] == "New DIR" || isset($_POST["new_dir"]))
   {
    if (isset($_POST["new_dir"]) ) 
       {
        @mkdir($_SESSION["dir_window_".(@$_GET["w"])].urldecode($_POST["new_dir"]));
       }
    else
       {
        echo("<form action=./?w=".@$_GET['w']."&drive=".@$_GET['drive']."&path=".urlencode($_GET['path'])." method=post>");        
        echo("<font color=gray face=verdana size=4>Type new directory name </font><input type=text name=new_dir size=16>&nbsp;&nbsp;<input type=submit value=Create>");
        echo(" &nbsp;<input type=submit name=action value=Cancel>");
        echo("</form>");
        exit;
       }
   }
elseif (@$_POST["action"] == "Copy")
   {
    if (isset($_POST["unit_file"]))
       {
        foreach($_POST["unit_file"] as $key=>$value)
           {
            copy($_SESSION["dir_window_".$_GET["w"]].urldecode($value),$_SESSION["dir_window_".($_GET["w"]=="A"?"B":"A")].urldecode($value));
           }
        if (isset($_SESSION["js"])) {echo("<script language=JavaScript> top.main_".($_GET["w"]=="A"?"B":"A").".location='./?w=".($_GET["w"]=="A"?"B":"A")."&Refresh=yes'; </script>");}
       }
   }
elseif (@$_POST["action"] == "Move")
   {
    if (isset($_POST["unit_dir"]))
       {
        foreach($_POST["unit_dir"] as $key=>$value)
           {
            @rename($_SESSION["dir_window_".$_GET["w"]].urldecode($value),$_SESSION["dir_window_".($_GET["w"]=="A"?"B":"A")].urldecode($value));
           }
        if (isset($_SESSION["js"])) {echo("<script language=JavaScript> top.main_".($_GET["w"]=="A"?"B":"A").".location='./?w=".($_GET["w"]=="A"?"B":"A")."&Refresh=yes'; </script>");}
       }
    if (isset($_POST["unit_file"]))
       {
        foreach($_POST["unit_file"] as $key=>$value)
           {
            @rename($_SESSION["dir_window_".$_GET["w"]].urldecode($value),$_SESSION["dir_window_".($_GET["w"]=="A"?"B":"A")].urldecode($value));
           }
        if (isset($_SESSION["js"])) {echo("<script language=JavaScript> top.main_".($_GET["w"]=="A"?"B":"A").".location='./?w=".($_GET["w"]=="A"?"B":"A")."&Refresh=yes'; </script>");}
       }
   }
elseif (@$_POST["action"] == "Upload")
   {
    if (is_uploaded_file(@$_FILES['upload_file']['tmp_name'])) 
       {
        copy($_FILES["upload_file"]["tmp_name"],$_SESSION["dir_window_".$_GET["w"]].$_FILES["upload_file"]["name"]);
       }
    elseif (!@$_GET["real"])
       {
        echo("<form enctype=multipart/form-data action=./?w=".@$_GET['w']."&drive=".@$_GET['drive']."&path=".urlencode(@$_GET['path'])."&real=yes method=post>");
        echo("<font color=gray face=verdana size=4>Upload file </font><input type=file name=upload_file>&nbsp;&nbsp;<input type=submit name=action value=Upload>");
        echo(" &nbsp;<input type=submit name=action value=Cancel>");
        echo("</form></body></html>");

        exit;
       }
   }


if (isset($_GET["Refresh"]) || isset($_POST["Cancel"]))
   {
    eregi("^([^:]):/.*",$_SESSION["dir_window_".$_GET["w"]],$drive);
    eregi("^[^/]+(/.*)",$_SESSION["dir_window_".$_GET["w"]],$path);
    $_GET["drive"]=$drive[1];
    $_GET["path"]=$path[1];
   }




if (@$_GET["drive"]) 
   {
    if (@$_GET["path"]) 
       {
        $path=eregi_replace("[^/]*/../$","",$_GET["path"]);
       }
    else
       {
        $path="/";
       }
       
    $path_2=$path;
    $path_2=eregi_replace("^/","",$path_2);
    $path_2=eregi_replace("/$","",$path_2);
    $path_arr=explode("/",$path_2);
    $link_path="<a class=path href=./?drive=".$_GET["drive"]."&w=".@$_GET["w"].">".$_GET["drive"].":/</a>";
    $real_path="/";
    for ($i=0;$i<count($path_arr);$i++)
        {
         if ($i==0 && $path_arr[0] == "") {break;}
         $real_path.=$path_arr[$i]."/";
         $link_path.="<a class=path href=./?drive=".$_GET["drive"]."&path=".urlencode($real_path)."&w=".@$_GET["w"]." >".$path_arr[$i]."/</a>";
        }
echo("<form action=./?w=".@$_GET['w']."&drive=".@$_GET['drive']."&path=".urlencode(@$_GET['path'])." method=post>");
$letters=Array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
eregi("^([^:]):(.+)",$_SERVER["DOCUMENT_ROOT"],$data);
if (!isset($_GET["drive"])) {$_GET["drive"]=strtoupper($data[1]);} else {$_GET["drive"]=strtoupper($_GET["drive"]);}
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

echo("<table border=0 cellpadding=0 cellspacing=1 width=100%>");
    echo("<tr><td colspan=".($old_browser?"1":"6").">");
    echo("<span name=upper_bar id=upper_bar><select name=drive>");
    for ($i=0;$i<count($drives);$i++) {echo("\r\n<option value=$drives[$i] ".($drives[$i]==$_GET["drive"]?"selected":"no").">$drives[$i]</option>");}
    echo("\r\n</select><input type=submit value=GO name=change_drive>");
    echo("<input type=submit name=action value=Copy>&nbsp;&nbsp;<input type=submit name=action value=Move>&nbsp;&nbsp;<input type=submit name=action value=Delete>&nbsp;&nbsp;<input type=submit name=action value=Rename>&nbsp;&nbsp;<input type=submit name=action value=\"New DIR\">&nbsp;&nbsp;<input type=submit name=action value=Upload>&nbsp;&nbsp;<input type=submit name=action value=Refresh></span></td></tr>");



    $path=eregi_replace("[^/]+/\.\./$","",$path);
    $dp=@opendir($_GET["drive"].":".$path);
    if (!$dp) {echo("</TD></TR></TABLE></FORM>Drive <b>".$_GET["drive"]."</b> is not available."); echo("drive-".$_GET["drive"]."<p>path-".$path);exit;}

    echo("<tr><td colspan=".($old_browser?"1":"6")."><span class=path><b>&nbsp;".$link_path."</b></span></td></tr>");
    if (@$_GET["w"] == "A") {$_SESSION["dir_window_A"]=strip_tags($link_path);}
    if (@$_GET["w"] == "B") {$_SESSION["dir_window_B"]=strip_tags($link_path);}
     
    while($file=@readdir($dp))
       {
        if (is_dir($_GET["drive"].":".$path.$file))
           {
            if ($file != ".") {$directories[count(@$directories)]=strtoupper($file);}
           }
        else
           {
            $files[count(@$files)]=strtolower($file);
           }
       }
    if (is_array(@$directories))
       {
        sort($directories,SORT_STRING);
        foreach($directories as $key=>$value) 
           {
            if ($old_browser) 
               { 
                $mtime=filemtime($_GET["drive"].":".$path.$value);
                $mtime=date("H:i d M y",$mtime);
                if ($value == "..") 
                   {
                    $mtime="&nbsp;";
                   }
                echo("<tr><td width=100%><table width=100% border=0 cellpadding=0 cellspacing=0><tr align=right><td width=1 align=left><input type=checkbox name=unit_dir[] value=\"".urlencode($value)."\"><a class=dir href=./?drive=".$_GET["drive"]."&path=".urlencode($path.$value)."/&w=".@$_GET["w"]." ><b>".($directories[$key] == ".."?"<span class=back>$value back</span>":"$value")."</b></a></td><td width=1><tt>".$mtime."&nbsp;</tt></td></tr></table></td></tr>");
               }
            else
               {
                $mtime=filemtime($_GET["drive"].":".$path.$value);
                $mtime=date("H:i d M y",$mtime);
                $mtime_arr=explode(" ",$mtime);
                if ($value == "..") {$mtime_arr[0]="&nbsp;"; $mtime_arr[1]="&nbsp;";$mtime_arr[2]="&nbsp;";$mtime_arr[3]="&nbsp;";}
                echo("<tr><td width=100%><table width=100% border=0 cellpadding=0 cellspacing=0><tr align=right><td align=left width=1><input type=checkbox name=unit_dir[] value=\"".urlencode($value)."\"></td><td align=left><a class=dir href=./?drive=".$_GET["drive"]."&path=".urlencode($path.$value)."/&w=".@$_GET["w"]." ><b>".($directories[$key] == ".."?"<span class=back>$value back</span>":"$value")."</b></a></td><td width=40>".$mtime_arr[0]."</td><td width=18>".$mtime_arr[1]."</td><td width=26>".$mtime_arr[2]."</td><td width=21><nobr>".$mtime_arr[3]."&nbsp;</nobr></td></tr></table></td></tr>");
               }
           }
       }
    if (is_array(@$files)) 
       {
        sort($files);
      
        foreach($files as $key=>$value) 
           {
            $size=filesize($_GET["drive"].":".$path.$value);
            if ($size > 1023) 
               {
                if ($size > 1048575)
                   {
                    $size=round($size/1048576,1)."<b>M</b>";
                   }
                else
                   {
                    $size=round($size/1024)."<b>k</b>";
                   }
               } 
            $mtime=filemtime($_GET["drive"].":".$path.$value);
            $mtime=date("H:i d M y",$mtime);
            $mtime_arr=explode(" ",$mtime);
            if ($old_browser)
               {
                echo("<tr><td><table width=100% border=0 cellpadding=0 cellspacing=0><tr align=right><td width=1 align=left><input type=checkbox name=unit_file[] value=\"".urlencode($value)."\"><a href=../download/?download=".urlencode($_GET["drive"].":".$path.$value).">".$value."</a></td><td width=1><tt>".$size."&nbsp;".$mtime_arr[0]."&nbsp;".$mtime_arr[1]."&nbsp;".$mtime_arr[2]."&nbsp;".$mtime_arr[3]."&nbsp;</tt></td></tr></table></td></tr>");
               }
            else
               {
                echo("<tr><td><table width=100% border=0 cellpadding=0 cellspacing=0><tr align=right><td width=1><input type=checkbox name=unit_file[] value=\"".urlencode($value)."\"></td><td align=left><a href=../download/?download=".urlencode($_GET["drive"].":".$path.$value).">".$value."</a></td><td width=1>".$size."</td><td width=40>".$mtime_arr[0]."</td><td width=18>".$mtime_arr[1]."</td><td width=26>".$mtime_arr[2]."</td><td width=21><nobr>".$mtime_arr[3]."&nbsp;</nobr></td></tr></table></td></tr>");
               }
           }
       }
    $lines=@count(@$files) + @count(@$directories);
    if ($lines>20 && !isset($_SESSION["js"]))
       {
        echo("<tr><td colspan=".($old_browser?"1":"6").">");
        echo("<select name=drive>");
        for ($i=0;$i<count($drives);$i++) {echo("\r\n<option value=$drives[$i] ".($drives[$i]==$_GET["drive"]?"selected":"no").">$drives[$i]</option>");}
        echo("\r\n</select><input type=submit value=GO name=change_drive>");
        echo("<input type=submit name=action value=Copy>&nbsp;&nbsp;<input type=submit name=action value=Move>&nbsp;&nbsp;<input type=submit name=action value=Delete>&nbsp;&nbsp;<input type=submit name=action value=Rename>&nbsp;&nbsp;<input type=submit name=action value=\"New DIR\">&nbsp;&nbsp;<input type=submit name=action value=Upload>&nbsp;&nbsp;<input type=submit name=action value=Refresh></td></tr>");
       }

    echo("</table>");
   }

?>
</form>
</body>
</html>