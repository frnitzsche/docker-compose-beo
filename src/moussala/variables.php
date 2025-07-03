<?php 
error_reporting(0);

$arr=get_defined_vars();
$mas=Array();
$blank_arr=Array();
echo("<p><font color=red>GLOBALS</font><br>");
while(list($key,$value) = each($arr)) 
     {
       echo("<br><font color=BLUE>".$key."</font> - ".$value);
       if (is_array($value)) {$mas[$key]=$value;}
     }


if (is_array($mas["_POST"]) && is_array($mas["HTTP_POST_VARS"])) {$mas["HTTP_POST_VARS"]=$blank_arr;}
if (is_array($mas["_GET"]) && is_array($mas["HTTP_GET_VARS"])) {$mas["HTTP_GET_VARS"]=$blank_arr;}
if (is_array($mas["_COOKIE"]) && is_array($mas["HTTP_COOKIE_VARS"])) {$mas["HTTP_COOKIE_VARS"]=$blank_arr;}
if (is_array($mas["_SERVER"]) && is_array($mas["HTTP_SERVER_VARS"])) {$mas["HTTP_SERVER_VARS"]=$blank_arr;}
if (is_array($mas["_ENV"]) && is_array($mas["HTTP_ENV_VARS"])) {$mas["HTTP_ENV_VARS"]=$blank_arr;}
if (is_array($mas["_FILES"]) && is_array($mas["HTTP_POST_FILES"])) {$mas["HTTP_POST_FILES"]=$blank_arr;}


while(list($key,$value) = each($mas)) 
     { 
        echo("<p><font color=red>".$key."</font><br>");
        while(list($key2,$value2) = each($value)) 
             {
                echo("<br><font color=BLUE>".$key2."</font> - ".$value2);
             }
     }

echo("<P/><FONT COLOR=RED>DEFINED CONSTANTS</FONT><pre>");
print_r(get_defined_constants());
echo("</pre>");
?>