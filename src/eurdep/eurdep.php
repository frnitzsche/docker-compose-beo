#!/usr/local/bin/php

<?php
$root_location="/home/ivo/files/";
$harwell_location="cern/harwell/";
$meteo_location="meteo/meteodata/";
$log_file="/home/alex/eurodep.log";

                                                                            ###########################################################
                                                                            #                                                         #
$to_send="eurdepdata@jrc.it,alexei@inrne.bas.bg";    #         COMMA SEPARATED LIST OF MAIL RECIPIENTS         #
                                                                            #                                                         #
                                                                            ###########################################################



                                                     #############################################################################################
                                                     #                                                                                           #
$sms_alert_addresses="359889812154@sms.mtel.net";    #         COMMA SEPARATED LIST OF MAIL TO SMS ADDRESSES FOR ALERT IN CASE OF ERROR          #
                                                     #                                                                                           #
                                                     #############################################################################################


       
#######################################################################################################
#                                                                                                     #
#                              DEFENITIONS OF CLASSES AND FUNCTIONS                                   #
#                                                                                                     #
#######################################################################################################

function get_valid_files()
   {
     $harwell_arr=array();
     $meteo_arr=array();
     global $root_location,$harwell_location,$meteo_location;
     $dp_1=opendir($root_location.$harwell_location);
     $dp_2=opendir($root_location.$meteo_location);
      
     while ($file=readdir($dp_1))
        {
          if (ereg("([0123456789]{2})\.([0123456789]{2})\.([0123456789]{2}).*",$file,$temp_arr_1)) 
             {
               $harwell_arr[$temp_arr_1[1].$temp_arr_1[2].$temp_arr_1[3]]=$temp_arr_1[1].$temp_arr_1[2].$temp_arr_1[3];
             }
        }
       
     while ($file=readdir($dp_2))
        {
          if (ereg("([0123456789]{2})([0123456789]{2})([0123456789]{2})[0123456789]{2}\.CSV",$file,$temp_arr_2)) 
             {
               $meteo_arr[$temp_arr_2[1].$temp_arr_2[2].$temp_arr_2[3]]=$temp_arr_2[1].$temp_arr_2[2].$temp_arr_2[3];
             }
        }
     $final_arr=array_merge($harwell_arr,$meteo_arr);
     krsort($final_arr);
     return $final_arr;
   }



function get_max_value($data)
   {
    arsort($data);
    reset($data);
    return current($data);
   }    


       
function get_min_value($data)
   {
    asort($data);
    reset($data);
    return current($data);
   }

function normalize_time($time)
      {
	    ereg("([01234567890]{1,2})\:([0123456789]{1,2})\:([0123456789]{1,2})",$time,$temp_arr);
	    if (strlen($temp_arr[1])==1) {$temp_arr[1]="0".$temp_arr[1];}
	    if (strlen($temp_arr[2])==1) {$temp_arr[2]="0".$temp_arr[2];}
	    if (strlen($temp_arr[3])==1) {$temp_arr[3]="0".$temp_arr[3];}
		$rezult=$temp_arr[1].":".$temp_arr[2].":".$temp_arr[3];
		return $rezult;
     }
   
   function format_date($date)
      {
	    ereg("([01234567890]{1,2})([0123456789]{1,2})([0123456789]{2,4})",$date,$temp_arr);
	    if (strlen($temp_arr[1])==1) {$temp_arr[1]="0".$temp_arr[1];}
	    if (strlen($temp_arr[2])==1) {$temp_arr[2]="0".$temp_arr[2];}
		$rezult=$temp_arr[1].".".$temp_arr[2].".".$temp_arr[3];
		return $rezult;
	  }
	  
function get_harwell_data($date)
   {
     global $root_location,$harwell_location;
     $formated_date=format_date($date);
     $file="$formated_date..txt";      
     $raw_data=@file($root_location.$harwell_location.$file);
     if (!$raw_data) {return false;}
     $raw_data_size=count($raw_data);
        	
     for ($i=0;$i<$raw_data_size;$i++)
        {
          $ok=eregi("([0123456789]{1,})[ \t]{1,}([0123456789]{1,})[\t ]{1,}([0123456789]{1,})\:([0123456789]{1,})",$raw_data[$i],$raw_data_2);
          if ($ok)
             {
               $doze_rates[$i]=$raw_data_2[2];
               $time[$i]=$raw_data_2[3].":".$raw_data_2[4];
               if (strlen($raw_data_2[3])==1) {$time[$i]="0".$time[$i];}
             }
        }
         	    
     $max_time=get_max_value($time);
     $min_time=get_min_value($time);          		
     $formated_date=date("Y-m-d",strtotime($date));
     $final_arr["data"]=round((array_sum($doze_rates)/count($doze_rates))/10,1);
     $final_arr["start_time"]=$min_time;
     $final_arr["stop_time"]=$max_time;
     $final_arr["start_date"]=$formated_date;
     $final_arr["stop_date"]=$formated_date;
     return $final_arr;
   }

function get_previous_day($today)
  {
    return date("ymd",strtotime($today) - 18000);
  }

function get_meteo_data($date)
   { 
     function rearrange_date($date)
        {
          ereg("([01234567890]{1,2})\.([0123456789]{1,2})\.([0123456789]{2,4})",$date,$temp_arr);
          if (strlen($temp_arr[1])==1) {$temp_arr[1]="0".$temp_arr[1];}
          if (strlen($temp_arr[2])==1) {$temp_arr[2]="0".$temp_arr[2];}
          $rezult=$temp_arr[3]."-".$temp_arr[2]."-".$temp_arr[1];
          return $rezult;
        }

     global $root_location,$meteo_location,$harwell_location,$err;;
     $lines_arr_1=Array();
     $lines_arr_2=Array();

     for ($i=0;$i<=24;$i++)
        {
          if (strlen($i)==1) {$i="0".$i;}
          if ($OK=@file($root_location.$meteo_location.$date.$i.".CSV"))
             {
               $lines_arr_2=array_merge($lines_arr_2,$OK);
             }
                		
          if ($OK=@file($root_location.$meteo_location.get_previous_day($date).$i.".CSV"))
             {
               $lines_arr_1=array_merge($lines_arr_1,$OK);
             }			
        }

     if (!$lines_arr_1 && !$lines_arr_2) {return FALSE;}
     $lines_arr_all=array_merge($lines_arr_1,$lines_arr_2);
    
     $count_lines_arr=count($lines_arr_all);
     $today=date("Y-m-d",strtotime($date));
     for ($i=2;$i<$count_lines_arr;$i++) 
        {
          eregi("([^\,]*)\,([^\,]*)\,([^\,]*)\,[^\,]*\,([^\,]*)\,[^\,]*\,[^\,]*\,([^\,]*)\,([^\,]*)\,",$lines_arr_all[$i],$raw_data_2);		
          $rearranged_date=rearrange_date($raw_data_2[1]);
          if ($rearranged_date==$today)
             {
               if ($raw_data_2[3] != "") { $data_temperature[$i]=$raw_data_2[3];}
               if ($raw_data_2[4] != "") { $data_pressure[$i]=$raw_data_2[4];}
               if ($raw_data_2[5] != "") { $data_wind_velocity[$i]=$raw_data_2[5];}
               if ($raw_data_2[6] != "") { $data_wind_direction[$i]=$raw_data_2[6];}
               $date_array[$i]=$raw_data_2[1];
               $time_array[$i]=normalize_time($raw_data_2[2]);
             }
        }
                 		   
     $data["file_date_start"]=rearrange_date(get_min_value($date_array));
     $data["file_date_stop"]=rearrange_date(get_max_value($date_array));
     $data["file_time_start"]=get_min_value($time_array);
     $data["file_time_stop"]=get_max_value($time_array);
     $data["temperature"]=number_format(array_sum($data_temperature)/count($data_temperature),1);

     if ($data["temperature"]>0)
        {
          $data["temperature"]="+".$data["temperature"];
        }

     $data["pressure"]=number_format(array_sum($data_pressure)/count($data_pressure),1);
     $data["wind_velocity"]=number_format(array_sum($data_wind_velocity)/count($data_wind_velocity),1);
     $data["wind_direction"]=number_format(array_sum($data_wind_direction)/count($data_wind_direction),0);
     return $data;
   }

##########################################################################################################
#                                                                                                        #
#                                     send_mail CLASS DEFINITION                                         #
#                                                                                                        #
##########################################################################################################
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
          $this->from="alexei@inrne.bas.com";
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
          ereg("^([0-9]+).(.*)$",$data,&$mass);
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
###################################################################################################
#                                                                                                 #
#                                 END OF send_mail CLASS DEFINITION                               #
#                                                                                                 #
###################################################################################################


#######################################################################################################
#                                                                                                     #
#                           END OF DEFENITIONS OF CLASSES AND FUNCTIONS                               #
#                                                                                                     #
#######################################################################################################


$log="";
$err="";
$_POST["date"]=date("ymd",time() - 86300);
$file_date=date("Y-m-d",strtotime($_POST["date"]));
$date_now=date("Y-m-d",time());
$time_now=date("H:i",time());
$harwell=get_harwell_data($_POST["date"]);
$meteo=get_meteo_data($_POST["date"]);

if (!$harwell && !$meteo) 
   {
     $err.="\r\nNo data for Meteo and Radioactivity for ".$file_date;
   }
else 
   {
     if (!$harwell)
        {
          $err.="\r\nNo data for Radoactivity on ".$file_date; 
        }
     if (!$meteo)
        {
          $err.="\r\nNo data for Meteo on ".$file_date;
        }
   }

if ($harwell)
   {
     $radio_sample="\BEGIN_RADIOLOGICAL;\r\n";
     $radio_sample.="\FIELD_LIST LOCALITY_CODE, VALUE, BEGIN, END, BACKGROUND, REMARKS;\r\n";
     $radio_sample.="\BEGIN_DEFAULT;\r\n";
     $radio_sample.="\SAMPLE_TYPE A5;\r\n";
     $radio_sample.="\NUCLIDE T-GAMMA;\r\n";
     $radio_sample.="\UNIT NSV/H;\r\n";
     $radio_sample.="\END_DEFAULT;\r\n";
     $radio_sample.="\BG0101,".$harwell['data']."E+01,".$harwell['start_date']."T".$harwell['start_time']."Z,".$harwell['stop_date']."T".$harwell['stop_time']."Z,-,\"\";\r\n";
     $radio_sample.="\END_RADIOLOGICAL;\r\n\r\n";
   }
else 
   {
     $radio_sample="";
   }

if ($meteo)
   {
     $meteo_sample="\BEGIN_METEO;\r\n";
     $meteo_sample.="\FIELD_LIST LOCALITY_CODE,BEGIN,END,METEO_TYPE,VALUE,REMARKS;\r\n";
     $meteo_sample.="\BG0103,".$meteo["file_date_start"]."T".$meteo["file_time_start"]."Z,".$meteo["file_date_stop"]."T".$meteo["file_time_stop"]."Z,TEMPERATURE,".(string)$meteo["temperature"].",\"\";\r\n";
     $meteo_sample.="\BG0103,".$meteo["file_date_start"]."T".$meteo["file_time_start"]."Z,".$meteo["file_date_stop"]."T".$meteo["file_time_stop"]."Z,PRESSURE,".(string)$meteo["pressure"].",\"\";\r\n";
     $meteo_sample.="\BG0103,".$meteo["file_date_start"]."T".$meteo["file_time_start"]."Z,".$meteo["file_date_stop"]."T".$meteo["file_time_stop"]."Z,WIND_DIRECTION,".(string)$meteo["wind_direction"].",\"\";\r\n";
     $meteo_sample.="\BG0103,".$meteo["file_date_start"]."T".$meteo["file_time_start"]."Z,".$meteo["file_date_stop"]."T".$meteo["file_time_stop"]."Z,WIND_SPEED,".(string)$meteo["wind_velocity"].",\"\";\r\n";
     $meteo_sample.="\END_METEO;\r\n\r\n";
   }
else
   {
     $meteo_sample="";
   }

$main_sample="\BEGIN_EURDEP;\r\n\r\n";
$main_sample.="\BEGIN_HEADER;\r\n";
$main_sample.="\IMPORTANCE NORMAL;\r\n";
$main_sample.="\SOFTWARE_VERSION 1.5;\r\n";
$main_sample.="\FORMAT_VERSION 2.0;\r\n";
$main_sample.="\ORIGINATOR Jordan Stamenov / INRNE-BAS-BG / jstamen@inrne.bas.bg / ++359-2-9743761 / ++359-2-9753619;\r\n";
$main_sample.="\MESSAGE_ID BG-GM".$file_date."T23:50Z;\r\n";
$main_sample.="\FILENAME BG".$_POST["date"]."23.TXT;\r\n";
$main_sample.="\CARRIER TCP/IP E-mail;\r\n";
$main_sample.="\SENT ".$date_now."T".$time_now."Z;\r\n";
$main_sample.="\END_HEADER;\r\n\r\n";
$main_sample.="\BEGIN_LOCALITY;\r\n";
$main_sample.="\FIELD_LIST LOCALITY_CODE, LOCALITY_NAME, LONGITUDE, LATITUDE, HEIGHT_ABOVE_SEA;\r\n";
$main_sample.="\BG0101, BEO-1, E23.5833, N42.1833, 2925;\r\n";
$main_sample.="\BG0103, Vaisala, E23.5830, N42.1830, 2925;\r\n";
$main_sample.="\END_LOCALITY;\r\n\r\n";
$main_sample.=$radio_sample."".$meteo_sample."\END_EURDEP;\r\n\r\n";

if ($meteo || $harwell) 
   {
     $try= new send_mail();
     #$try->charset="windows-1251";
     $try->header_to="eurdepdata@jrc.it";
     $try->header_from="alexei@inrne.bas.bg";
     $try->message="";
     $try->to=$to_send;
     $try->from="alexei@inrne.bas.bg";
     $try->subject="BG".$_POST["date"]."23.TXT";
     $try->HELO="beo-db.inrne.bas.bg";
     $try->add_attachment($main_sample,"BG".$_POST['date']."23.TXT","text/plain","data");
        
     $success=$try->send();
     if ($success) 
        {
          $log.="\r\nData sucessfully send to: ".$try->to." on ".$date_now." at ".$time_now."GMT";
        }
     else
        {
         $log.="\r\nCan not send data to : ".$try->to." on ".$date_now." at ".$time_now."GMT";
         $err.="\r\nCan not send data to : ".$try->to." on ".$date_now." at ".$time_now."GMT";
        }
   }

if ($err)
   {
     $sms= new send_mail();
     #$sms->charset="windows-1251";
     $sms->header_to="$sms_alert";
     $sms->header_from="alexei@inrne.bas.bg";
     $sms->message=$err;
     $sms->to=$sms_alert_addresses;
     $sms->from="alexei@inrne.bas.bg";
     $sms->subject="Eurdep Alert";
     $sms->HELO="beo-db.inrne.bas.bg";
     $sms_success=$sms->send();
     $log.=$err;
     if ($sms_success) 
        {
          $log.=", sms alert send successfully";
        }
     else 
        {
          $log.=", failed sending sms alert";
        }
   }

$log.="\r\n";
$fp=fopen("/home/alex/eurdep.log","a");
fwrite($fp,$log);

?>