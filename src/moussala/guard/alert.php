<?php
set_time_limit(300);
ignore_user_abort(1);
echo("OK");
flush();


include("Settings.php");
if ($_SERVER["SERVER_ADDR"] == $_SERVER["REMOTE_ADDR"])
   {
    $arr=unserialize(stripslashes(urldecode($_GET["data"])));
    if (count($arr)>4)
       {
        $type_of_attack="lamers";
       }
    else
       {
        $type_of_attack="automatic";
       }
    $DNS_name="";
    if ($resolve_DNS) 
       {
        $temp=gethostbyname($_GET["IP"]);
        if ($temp != $_GET["IP"]) 
           {
            $attacker=$temp." [".$_GET["IP"]."]";
           }
        else
           {
            $attacker=$_GET["IP"]." (DNS not found)";
           }
       }
     else
       {
        $attacker=$_GET["IP"];
       }

    if (eregi("^[0123456789]{9,}@.{3,}\..{2,3}",$mail_to_sms_address))
       {
        $GSM_message="Account ".$_GET["u"]." is under ".$type_of_attack." attack from $attacker at ".date("H:i:s j M",$arr[0]);
        $headers="From: ".$mail_from;
        ini_set("SMTP",$sms_smtp_server);
        $sms_OK=@mail($mail_to_sms_address,"Attack from ".$_GET["IP"],$GSM_message,$headers);
       }

    if ($mail_address || $attacks_log)
       {
        $count=Array(1=>"one",2=>"two",3=>"three",4=>"four",5=>"five",6=>"six",7=>"seven",8=>"eight",9=>"nine",10=>"ten",11=>"eleven",12=>"twelve",13=>"thirteen",14=>"fourteen",15=>"fifteen",16=>"sixteen",17=>"seventeen",18=>"eighteen",19=>"nineteen",20=>"twenty",);
        $COUNT=Array(1=>"first",2=>"second",3=>"third",4=>"fourth",5=>"fifth",6=>"sixth",7=>"seventh",8=>"eighth",9=>"ninth",10=>"tenth",11=>"eleventh",12=>"twelfeth",13=>"thirteenth",14=>"fourteenth",15=>"fifteenth",16=>"sixteenth",17=>"seventeenth",18=>"eighteenth",19=>"nineteenth",20=>"twentieth",);
        $headers="From: ".$mail_from."\r\nMIME-Version: 1.0\r\nContent-Type: multipart/alternative; boundary=\"----=_NextPart_000_01C3108D.13A94780\"\r\nContent-Transfer-Encoding: 8bit";
        $message="At ".date("H:i:s jS \o\f M Y",$arr[0])." account <font color=red>".$_GET["u"]."</font> on server ".$_SERVER["SERVER_NAME"]."[".$_SERVER["SERVER_ADDR"]."] has been attacked.\r\n";
        $message.="The attack apparently came from address <font color=red>".$attacker."</font>";

        $tampered=0;
        for ($i=0;$i<count($arr);$i++) 
            {
             if ($i==0) 
                {
                 $temp_var=preg_match("/^[0123456789]+#/","",$arr[$i]);
                }
             else
                {
                 if ($temp_var != preg_match("/^[0123456789]+#/","",$arr[$i])) {$tampered=1;}
                }
            }

        if (!$tampered && $temp_var=="")
           {   
            $message.=".\r\n";
           }
        else
           {
            if (!$tampered) 
               {
                $message.=",\r\nbut X_FORWARDED_FOR header is present:<font color=red>X_FORWARDED_FOR: $temp_var</font>\r\n";
               }
            else
               {
                $message.=",\r\nbut X_FORWARDED_FOR header is present in different forms.\r\n";
               }
           }
        $message.="There have been <font color=red>".$count[count($arr)]."</font> login attempts with wrong password:\r\n";
        for ($i=1;$i<=count($arr);$i++) 
            {
             $message.=sprintf("%' -7s%s",$COUNT[$i]," at ".date("H:i:s j M Y",$arr[$i-1]));
             if ($tampered)
                {
                 $message.=" - <font color=red>X_FORWARDED_FOR: ".preg_match("/^[0123456789]+#/","",$arr[$i-1]."</font>");
                }
             $message.=($i<count($arr)?",\r\n":".");
            }
        $message.="\r\nIn result of that Password Protection Mechanism has been activated for this account.";
        $IP_info_link="";
        $investigation="";
        if ($investigate_ip)
           {
            include("investigate_ip.php");
            $investigation="\r\n".$var;
           }
        else
           {
            $IP_info_link="\r\n<a href=http://".$_SERVER["SERVER_ADDR"]."/investigate_ip.php?IP=".$_GET["IP"].">Gather information about IP address ".$_GET["IP"]."</a>";
           }
            
        if (eregi("^.{1,}@.{3,}\..{2,3}",$mail_address))
           {
            $body="This is a multi-part message in MIME format.\r\n\r\n------=_NextPart_000_01C3108D.13A94780\r\nContent-Type: text/plain; charset=Windows-1251\r\nContent-Transfer-Encoding: 8bit\r\n\r\n".strip_tags($message.$investigation)."\r\n------=_NextPart_000_01C3108D.13A94780\r\nContent-Type: text/html; charset=Windows-1251\r\nContent-Transfer-Encoding: 8bit\r\n\r\n<HTML><HEAD></HEAD><BODY text=#000000><PRE>".$message.$IP_info_link.$investigation."</PRE></BODY></HTML>\r\n------=_NextPart_000_01C3108D.13A94780--";
            ini_set("SMTP",$mail_smtp_server);
            $mail_OK=@mail($mail_address,"Attack from ".$_GET["IP"],$body,$headers);
           }
            
        if ($attacks_log)
           {
            $mail_delivery_report="";
            if (isset($sms_OK)) 
               {
                if ($sms_OK) 
                   {
                    $mail_delivery_report.="\r\nSms sent to ".$mail_to_sms_address.".";
                   }
                else
                   {
                    $mail_delivery_report.="\r\nFailed to send sms to ".$mail_to_sms_address.".";
                   }
               }
            if (isset($mail_OK)) 
               {
                if ($mail_OK)
                   {
                    $mail_delivery_report.="\r\nMail sent to ".$mail_address.".";
                   }
                else
                   {
                    $mail_delivery_report.="\r\nFailed to send mail to ".$mail_address.".";
                   }

               }
            if (!isset($mail_OK) && !isset($sms_OK))
               {
                $mail_delivery_report.="\r\nNo body has been alarmed.";
               }
            $fp=fopen($attacks_log,"a+");
            fwrite($fp,strip_tags($message)."\r\n".$mail_delivery_report."\r\n".$investigation."\r\nxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\r\n\r\n");
            fclose($fp);
           }
       }
   }
else
   {
    echo("Access denied");
   }

?>