#!/usr/local/bin/php

<?php
$log_file="./eurdep.log";
$database="moussala";
$db_server="localhost";
$username="eurdep";
$password="eurdep";


$to_send="eurdepdata@jrc.it,alexei@inrne.bas.bg";  
##########################################################
#                                                        #
#         COMMA SEPARATED LIST OF MAIL RECIPIENTS        #
#                                                        #
##########################################################



$sms_alert_addresses="359889812154@sms.mtel.net";
##########################################################
#                                                        #
#         COMMA SEPARATED LIST OF MAIL TO SMS            #
#         ADDRESSES FOR ALERT IN CASE OF ERROR           #
#                                                        #
##########################################################



##########################################################
#                                                        #
#              DEFENITIONS OF FUNCTIONS                  #
#                                                        #
##########################################################

function get_previous_day($today,$day=1) {
  return date("ymd",strtotime($today." 13:00") - 86400*$day);
}

function error($message,$error_var=""){
   global $log_file;
   $error_message="\r\n".date("m.d.Y H:i:s")." - ".$message."   ".$error_var;
   error_log($error_message,3,$log_file);
}

function get_data($date,$table){
   global $link;
   $begin_timestamp=$date."000000";
   $end_timestamp=$date."235959";

   $query="SELECT avg(data) FROM $table WHERE timestamp>=$begin_timestamp AND timestamp<=$end_timestamp";
   $result=mysql_query($query,$link);
   if (!$result || mysql_num_fields($result) != 1) {
      return false;
   }
   $data_arr=mysql_fetch_array($result,MYSQL_NUM);
   if (!$data_arr[0]) {return false;}
   $final_arr["data"]=$data_arr[0];

   $query="SELECT min(timestamp) FROM $table WHERE timestamp>=$begin_timestamp AND timestamp<=$end_timestamp";
   $result=mysql_query($query,$link);
   if (!$result || mysql_num_fields($result) != 1) {
      return false;
   }
   $min_time_arr=mysql_fetch_array($result,MYSQL_NUM);
   $final_arr["start_date"]=substr($min_time_arr[0],0,10);
   $final_arr["start_time"]=substr($min_time_arr[0],11,17);

   $query="SELECT max(timestamp) FROM $table WHERE timestamp>=$begin_timestamp AND timestamp<=$end_timestamp";
   $result=mysql_query($query,$link);
   if (!$result || mysql_num_fields($result) != 1) {
      return false;
   }
   $min_time_arr=mysql_fetch_array($result,MYSQL_NUM);
   $final_arr["stop_date"]=substr($min_time_arr[0],0,10);
   $final_arr["stop_time"]=substr($min_time_arr[0],11,17);

   return $final_arr;
}

###########################################################
#                                                         #
#          END OF DEFENITIONS OF FUNCTIONS                #
#                                                         #
###########################################################

###########################################################
#                                                         #
#              send_mail CLASS DEFINITION                 #
#                                                         #
###########################################################

class send_mail
   {
     var $server;
     var $to;
     var $from;
     var $subject;
     var $message;
     var $data;
     var $attach;
     var $boundary;
     var $attach_data;
     var $HELO;
     var $rcpt;
     var $header_to;
     var $header_from;
     var $charset;

     function send_mail()
        {
          $this->server="mailserv.inrne.bas.bg";
          $this->to="";
          $this->from="alexei@inrne.bas.bg";
          $this->subject="not specified";
          $this->message="";
          $this->attach=false;
          $this->boundary="b".md5(uniqid(time()));
          $this->charset="iso-8859-1";  //iso-8859-1
          $this->ctype="text/plain";
          $this->attach_data="";
          $this->HELO="nobody";
          $this->rcpt=array();
          $this->header_to="";
          $this->header_from="";
        }

     function dialogue($message,$answer)
        {
          global $lastmessage;
          global $fp;
          $ret=false;
          fputs($fp,$message."\r\n");
          $data=fgets($fp,1024);
          preg_match("/^([0-9]+).(.*)$/",$data,&$mass);
          $lastmessage=$data;
          if ($mass[1] == $answer) {$ret=true;}
          return $ret;
        }

     function add_attachment($file_location,$file_name,$ctype="application/octet-stream",$action="file")
        {
          global $attach_data;
          if ($action == "file")
             {
               $fp=fopen($file_location,"rb");
               $message=fread($fp,filesize($file_location));
               if (!$message)
                  {
                    return;
                  }
             }
          else
             {
               $message=$file_location;
             }
          $this->attach="true";
          $attach_data.="--$this->boundary\r\n";
          $attach_data.="Content-Type: $ctype; name=\"$file_name\"\r\n";
          $attach_data.="Content-Transfer-Encoding:base64\r\n";
          $attach_data.="Content-Disposition: attachment; filename=\"$file_name\"\r\n\r\n";
          $attach_data.=chunk_split(base64_encode($message));
        }


function send()
   {
     global $attach_data;
     global $fp;
     global $lastmessage;
     $rcpt=explode(",",$this->to);
     $error=true;

     if ($this->header_from) {$from_command=$this->header_from;} else {$from_command=$this->from;}
     $data="From: $from_command\r\n";
     $data.="To: ";
     if ($this->header_to)
        {
          $data.=$this->header_to;
        }
     else
        {
          $data.=$this->to;
        }
     $data.="\r\nSubject: $this->subject\r\n";
     $data.="MIME-Version: 1.0\r\n";
     if ($this->attach)
        {
          $data.="Content-Type: multipart/mixed; boundary=\"$this->boundary\"\r\n\r\n";
          $data.="This is a multi-part message in MIME format.\r\n\r\n";
          $data.="--$this->boundary\r\n";
          $data.="Content-Type: $this->ctype;charset=\"$this->charset\"\r\n";
          $data.="Content-Transfer-Encoding:base64\r\n\r\n";
          $data.=base64_encode($this->message);
          $data.="$attach_data--$this->boundary--";
        }
     else
        {
          $data.="Content-Type: $this->ctype;charset=\"$this->charset\"\r\nContent-Transfer-Encoding: 8bit\r\n\r\n".$this->message;
        }


     $fp=@fsockopen($this->server,25,$errno, $errstr, 10);
     if (!$fp)
        {
         return FALSE;
        }
     fgets($fp,1024);
     if (!$this->dialogue("HELO $this->HELO",250) || !$this->dialogue("MAIL FROM:$this->from",250))
        {
          echo("Failed delivering mail: <b>$lastmessage</b>"); fclose($fp); exit;
        }
     for ($i=0;$i<count($rcpt);$i++) {if ($this->dialogue("RCPT TO: $rcpt[$i]",250))  {$error=false;}}
     if ($error || !$this->dialogue("DATA",354) || !fwrite($fp,$data) || !$this->dialogue("\r\n.",250) )
        {
          echo("Failed delivering mail: <b>$lastmessage</b>");
          fclose($fp); exit;
        }
     $this->dialogue("QUIT",221);
     fclose($fp);
     return true;
   }
}

################################################################
#                                                              #
#             END OF send_mail CLASS DEFINITION                #
#                                                              #
################################################################


$log="";
$err="";
$link=mysql_connect($db_server,$username,$password);
if (!$link){
   error("No connection to server",$mysql_error_message); exit;
}
$OK=mysql_select_db($database,$link);
if (!$OK) {
   error("Can not select data base $database",$mysql_error_message); exit;
}

$date_now=date("ymd",time());
$time_now=date("H:i",time());
$full_date_now=date("Y-m-d");
$required_date=get_previous_day($date_now);
$full_required_date="20".eregi_replace("([0-9]{2})([0-9]{2})([0-9]{2})","\\1-\\2-\\3",$required_date);
$gamma=get_data($required_date,"gamma");
$temperature=get_data($required_date,"temperature");
$pressure=get_data($required_date,"pressure");
$wind_velocity=get_data($required_date,"wind_velocity");
$wind_direction=get_data($required_date,"wind_direction");

$gamma["data"]=round($gamma["data"]/10,1);
$temperature['data']=round($temperature['data'],1);
$pressure['data']=round($pressure['data'],1);
$wind_velocity['data']=round($wind_velocity['data'],1);
$wind_direction['data']=round($wind_direction['data'],0);



if (!$gamma && !$temperature && !$pressure && !$wind_direction && !$wind_velocity){
   $err.="\r\nNo data for Meteo and Radioactivity for ".$required_date;
}
else{
   if (!$gamma){
      $err.="\r\nNo data for Radoactivity on ".$required_date;
   }
   if (!$temperature && !$pressure && !$wind_velocity && !$wind_direction){
      $err.="\r\nNo data for Meteo on ".$required_date;
   }
}

if ($gamma['data'] && $gamma['start_date'] && $gamma['start_time'] && $gamma['stop_date'] && $gamma['stop_time']){
   $radio_sample="\BEGIN_RADIOLOGICAL;\r\n";
   $radio_sample.="\FIELD_LIST LOCALITY_CODE, VALUE, BEGIN, END, BACKGROUND, REMARKS;\r\n";
   $radio_sample.="\BEGIN_DEFAULT;\r\n";
   $radio_sample.="\SAMPLE_TYPE A5;\r\n";
   $radio_sample.="\NUCLIDE T-GAMMA;\r\n";
   $radio_sample.="\UNIT NSV/H;\r\n";
   $radio_sample.="\END_DEFAULT;\r\n";
   $radio_sample.="\BG0101,".$gamma['data']."E+01,".$gamma['start_date']."T".$gamma['start_time']."Z,".$gamma['stop_date']."T".$gamma['stop_time']."Z,-,\"\";\r\n";
   $radio_sample.="\END_RADIOLOGICAL;\r\n\r\n";
}
else{
   $radio_sample="";
}

if ($temperature || $pressure || $wind_velocity || $wind_direction){
   $meteo_sample="\BEGIN_METEO;\r\n";
   $meteo_sample.="\FIELD_LIST LOCALITY_CODE,BEGIN,END,METEO_TYPE,VALUE,REMARKS;\r\n";
   if ($temperature) {
      $meteo_sample.="\BG0103,".$temperature["start_date"]."T".$temperature["start_time"]."Z,".$temperature["stop_date"]."T".$temperature["stop_time"]."Z,TEMPERATURE,".(string)$temperature["data"].",\"\";\r\n";
   }
   if ($pressure) {
      $meteo_sample.="\BG0103,".$pressure["start_date"]."T".$pressure["start_time"]."Z,".$pressure["stop_date"]."T".$pressure["stop_time"]."Z,PRESSURE,".(string)$pressure["data"].",\"\";\r\n";
   }
   if ($wind_direction) {
      $meteo_sample.="\BG0103,".$wind_direction["start_date"]."T".$wind_direction["start_time"]."Z,".$wind_direction["stop_date"]."T".$wind_direction["stop_time"]."Z,WIND_DIRECTION,".(string)$wind_direction["data"].",\"\";\r\n";
   }
   if ($wind_velocity) {
      $meteo_sample.="\BG0103,".$wind_velocity["start_date"]."T".$wind_velocity["start_time"]."Z,".$wind_velocity["stop_date"]."T".$wind_velocity["stop_time"]."Z,WIND_SPEED,".(string)$wind_velocity["data"].",\"\";\r\n";
   }
   $meteo_sample.="\END_METEO;\r\n\r\n";
}
else{
   $meteo_sample="";
}

$main_sample="\BEGIN_EURDEP;\r\n\r\n";
$main_sample.="\BEGIN_HEADER;\r\n";
$main_sample.="\IMPORTANCE NORMAL;\r\n";
$main_sample.="\SOFTWARE_VERSION 1.5;\r\n";
$main_sample.="\FORMAT_VERSION 2.0;\r\n";
$main_sample.="\ORIGINATOR Jordan Stamenov / INRNE-BAS-BG / jstamen@inrne.bas.bg / ++359-2-9743761 / ++359-2-9753619;\r\n";
$main_sample.="\MESSAGE_ID BG-GM".$full_required_date."T23:50Z;\r\n";
$main_sample.="\FILENAME BG".$required_date."23.TXT;\r\n";
$main_sample.="\CARRIER TCP/IP E-mail;\r\n";
$main_sample.="\SENT ".$full_date_now."T".$time_now."Z;\r\n";
$main_sample.="\END_HEADER;\r\n\r\n";
$main_sample.="\BEGIN_LOCALITY;\r\n";
$main_sample.="\FIELD_LIST LOCALITY_CODE, LOCALITY_NAME, LONGITUDE, LATITUDE, HEIGHT_ABOVE_SEA;\r\n";
$main_sample.="\BG0101, BEO-1, E23.5833, N42.1833, 2925;\r\n";
$main_sample.="\BG0103, Vaisala, E23.5830, N42.1830, 2925;\r\n";
$main_sample.="\END_LOCALITY;\r\n\r\n";
$main_sample.=$radio_sample."".$meteo_sample."\END_EURDEP;\r\n\r\n";


/*
echo("<pre>");
echo($main_sample);
echo("</pre>"); echo("round up meteo values");exit;
*/

if ($gamma || $temperature){
   $try= new send_mail();
   #$try->charset="windows-1251";
   $try->header_to="eurdepdata@jrc.it";
   $try->header_from="alexei@inrne.bas.bg";
   $try->message="";
   $try->to=$to_send;
   $try->from="alexei@inrne.bas.bg";
   $try->subject="BG".$required_date."23.TXT";
   $try->HELO="beo-db.inrne.bas.bg";
   $try->add_attachment($main_sample,"BG".$required_date."23.TXT","text/plain","data");

   $success=$try->send();
   if ($success){
      $log.="\r\nData sucessfully send to: ".$try->to." on ".$date_now." at ".$time_now."GMT";
   }else{
      $log.="\r\nCan not send data to : ".$try->to." on ".$date_now." at ".$time_now."GMT";
      $err.="\r\nCan not send data to : ".$try->to." on ".$date_now." at ".$time_now."GMT";
   }
}

if ($err){
   $sms= new send_mail();
   #$sms->charset="windows-1251";
   $sms->header_to="$sms_alert_addresses";
   $sms->header_from="alexei@inrne.bas.bg";
   $sms->message=$err;
   $sms->to=$sms_alert_addresses;
   $sms->from="alexei@inrne.bas.bg";
   $sms->subject="Eurdep Alert";
   $sms->HELO="beo-db.inrne.bas.bg";
   $sms_success=$sms->send();
   $log.=$err;
   if ($sms_success){
      $log.=", sms alert send successfully";
   }else{
      $log.=", failed sending sms alert";
   }
}

$log.="\r\n";
$fp=fopen("$log_file","a");
fwrite($fp,$log);
fclose($fp);

?>