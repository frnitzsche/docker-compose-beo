<?php
if (@$_SERVER["REDIRECT_QUERY_STRING"]) 
   {
    $arr=explode("&",urldecode($_SERVER["REDIRECT_QUERY_STRING"]));
    foreach($arr as $key=>$value) 
        {
         $arr_little=explode("=",$arr[$key]);
         @$_GET[$arr_little[0]]=@$arr_little[1];
        }
   }
if (@$_GET["download"]) 
   {
    eregi("/?([^/]+)$",$_GET["download"],$file);
    header("HTTP/1.0 200 OK");
    header("Content-Disposition: attachment; filename=".$file[1]);
    header("Content-type: application/octet-stream");
    header("Content-length: ".(string)(filesize($_GET["download"])));
    $fp=fopen($_GET["download"],"rb");
    fpassthru($fp);
exit;    
   }

echo("<font color=red>".$_SERVER["REQUEST_URI"]."</font> can not be found on this server.<br>Sorry!");

?>