<?php

$root_location="d:/moussala/";
$harwell_location="cern/harwell/";
$meteo_location="meteo/meteodata/";




function get_valid_files()
   {
   	$harwell_arr=array();
	$meteo_arr=array();
        global $root_location,$harwell_location,$meteo_location;
	$dp_1=opendir($root_location.$harwell_location);
	$dp_2=opendir($root_location.$meteo_location);
	
        while ($file=readdir($dp_1))
 	   {
	     if (preg_match("/([0123456789]{2})\.([0123456789]{2})\.([0123456789]{2}).*/",$file,$temp_arr_1)) 
		   {
	         $harwell_arr[$temp_arr_1[1].$temp_arr_1[2].$temp_arr_1[3]]=$temp_arr_1[1].$temp_arr_1[2].$temp_arr_1[3];
		   }
	   }

	while ($file=readdir($dp_2))
	   {
	     if (preg_match("/([0123456789]{2})([0123456789]{2})([0123456789]{2})[0123456789]{2}\.CSV/",$file,$temp_arr_2)) 
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
	    preg_match("/([01234567890]{1,2})\:([0123456789]{1,2})\:([0123456789]{1,2})/",$time,$temp_arr);
	    if (strlen($temp_arr[1])==1) {$temp_arr[1]="0".$temp_arr[1];}
	    if (strlen($temp_arr[2])==1) {$temp_arr[2]="0".$temp_arr[2];}
	    if (strlen($temp_arr[3])==1) {$temp_arr[3]="0".$temp_arr[3];}
		$rezult=$temp_arr[1].":".$temp_arr[2].":".$temp_arr[3];
		return $rezult;
     }
   
   function format_date($date)
      {
	    preg_match("/([01234567890]{1,2})([0123456789]{1,2})([0123456789]{2,4})/",$date,$temp_arr);
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
	    preg_match("/([01234567890]{1,2})\.([0123456789]{1,2})\.([0123456789]{2,4})/",$date,$temp_arr);
	    if (strlen($temp_arr[1])==1) {$temp_arr[1]="0".$temp_arr[1];}
	    if (strlen($temp_arr[2])==1) {$temp_arr[2]="0".$temp_arr[2];}
		$rezult=$temp_arr[3]."-".$temp_arr[2]."-".$temp_arr[1];
		return $rezult;
	  }
	  
    global $root_location,$meteo_location,$harwell_location;
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
	 if (!$lines_arr_1 && !$lines_arr_2) {echo("yahoooooo");  return FALSE;}
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
   $this->from="nobody@domain.com";
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
 if (!$message) {return;}
} else {$message=$file_location;}
 $this->attach="true";
$attach_data.="--$this->boundary
Content-Type: $ctype; name=\"$file_name\"
Content-Transfer-Encoding:base64
Content-Disposition: attachment; filename=\"$file_name\"

".chunk_split(base64_encode($message))."";
  }


function send()
  {
   global $attach_data;
   global $fp;
   global $lastmessage;
   $rcpt=explode(",",$this->to);
   $error=true;
  
if ($this->header_from) {$from_command=$this->header_from;} else {$from_command=$this->from;}  

$data="From: $from_command
To: "; if ($this->header_to) {$data.=$this->header_to;} else {$data.=$this->to;}
$data.="
Subject: $this->subject
MIME-Version: 1.0
";
if ($this->attach) 
{$data.="Content-Type: multipart/mixed; boundary=\"$this->boundary\"

This is a multi-part message in MIME format.

--$this->boundary
Content-Type: $this->ctype;charset=\"$this->charset\"
Content-Transfer-Encoding:base64

".base64_encode($this->message)."

$attach_data--$this->boundary--";} 
else {$data.="Content-Type: $this->ctype;charset=\"$this->charset\"\r\nContent-Transfer-Encoding: base64\r\n\r\n".base64_encode($this->message);}


$fp=@fsockopen($this->server,25,$errno, $errstr, 10);
if (!$fp) {return FALSE;}
fgets($fp,1024);
if (!$this->dialogue("HELO $this->HELO",250) ||
    !$this->dialogue("MAIL FROM:$this->from",250)) {echo("Failed delivering mail: <b>$lastmessage</b>"); fclose($fp); exit;}
for ($i=0;$i<count($rcpt);$i++) {if ($this->dialogue("RCPT TO: $rcpt[$i]",250))  {$error=false;}}
if ($error ||
    !$this->dialogue("DATA",354) ||
    !fwrite($fp,$data) ||
    !$this->dialogue("\r\n.",250) )
{echo("Failed delivering mail: <b>$lastmessage</b>"); fclose($fp); exit;}
$this->dialogue("QUIT",221);
fclose($fp);
return true;
  }
} // end of class


 
if (isset($_POST["date"]))
  {
  /*
echo("<pre>");
print_r(get_meteo_data($_POST["date"]));
echo("</pre>"); exit;
*/

$file_date=date("Y-m-d",strtotime($_POST["date"]));
$date_now=date("Y-m-d",time());
$time_now=date("H:i",time());
$harwell=get_harwell_data($_POST["date"]);
$meteo=get_meteo_data($_POST["date"]);


if (!$harwell && !$meteo) 
   {
     echo("<font color=red size=5>�������� ������! <br> ���� ����� ���� �� Harwell, ���� �� Vaisala</font>");
	 echo("<a href=".$_SERVER["PHP_SELF"]."><br>�����</a>");
	 exit;
   }

if ($harwell)
{
$radio_sample="\BEGIN_RADIOLOGICAL;
\FIELD_LIST LOCALITY_CODE, VALUE, BEGIN, END, BACKGROUND, REMARKS;
\BEGIN_DEFAULT;
\SAMPLE_TYPE A5;
\NUCLIDE T-GAMMA;
\UNIT NSV/H;
\END_DEFAULT;
\BG0101,".$harwell['data']."E+01,".$harwell['start_date']."T".$harwell['start_time']."Z,".$harwell['stop_date']."T".$harwell['stop_time']."Z,-,\"\";
\END_RADIOLOGICAL;

";
}
else 
{
$radio_sample="";
}

if ($meteo)
{
$meteo_sample="\BEGIN_METEO;
\FIELD_LIST LOCALITY_CODE,BEGIN,END,METEO_TYPE,VALUE,REMARKS;
\BG0103,".$meteo["file_date_start"]."T".$meteo["file_time_start"]."Z,".$meteo["file_date_stop"]."T".$meteo["file_time_stop"]."Z,TEMPERATURE,".(string)$meteo["temperature"].",\"\";
\BG0103,".$meteo["file_date_start"]."T".$meteo["file_time_start"]."Z,".$meteo["file_date_stop"]."T".$meteo["file_time_stop"]."Z,PRESSURE,".(string)$meteo["pressure"].",\"\";
\BG0103,".$meteo["file_date_start"]."T".$meteo["file_time_start"]."Z,".$meteo["file_date_stop"]."T".$meteo["file_time_stop"]."Z,WIND_DIRECTION,".(string)$meteo["wind_direction"].",\"\";
\BG0103,".$meteo["file_date_start"]."T".$meteo["file_time_start"]."Z,".$meteo["file_date_stop"]."T".$meteo["file_time_stop"]."Z,WIND_SPEED,".(string)$meteo["wind_velocity"].",\"\";
\END_METEO;

";
}
else
{
$meteo_sample="";
}

$main_sample="\BEGIN_EURDEP;

\BEGIN_HEADER;
\IMPORTANCE NORMAL;
\SOFTWARE_VERSION 1.5;
\FORMAT_VERSION 2.0;
\ORIGINATOR Jordan Stamenov / INRNE-BAS-BG / jstamen@inrne.bas.bg / ++359-2-9743761 / ++359-2-9753619;
\MESSAGE_ID BG-GM".$file_date."T23:50Z;
\FILENAME BG".$_POST["date"]."23.TXT;
\CARRIER TCP/IP E-mail;
\SENT ".$date_now."T".$time_now."Z;
\END_HEADER;

\BEGIN_LOCALITY;
\FIELD_LIST LOCALITY_CODE, LOCALITY_NAME, LONGITUDE, LATITUDE, HEIGHT_ABOVE_SEA;
\BG0101, BEO-1, E23.5833, N42.1833, 2925;
\BG0103, Vaisala, E23.5830, N42.1830, 2925;
\END_LOCALITY;

".$radio_sample."".$meteo_sample."\END_EURDEP;

";

   if (isset($_POST["send"]))
     {
      $try= new send_mail();

      #$try->charset="windows-1251";
      $try->header_to="eurdepdata@jrc.it";
      $try->header_from="alexei@inrne.bas.bg";
      $try->message="";
      $try->to="tony@inrne.bas.bg,alexeinishev@yahoo.com,eurdepdata@jrc.it";
      $try->from="beo-db@inrne.bas.bg";
      $try->subject="BG".$_POST["date"]."23.TXT";
      $try->HELO="beo-db.inrne.bas.bg";
      $try->add_attachment($main_sample,"BG".$_POST['date']."23.TXT","text/plain","data");

      $success=$try->send();
      if ($success) 
        {
         echo("������� � ������� ��������� ��: <b>$try->to</b>");
         echo("<a href=".$_SERVER["PHP_SELF"]."><br>������� ���</a>");
        }
      else
        {
         echo("�������� ������ ��� ������������ �� ������� ��: <b>$try->to</b>");
         echo("<a href=".$_SERVER["PHP_SELF"]."><br>������� ���</a>");
        }
     }
   else
     {
      echo("<pre>".$main_sample."</pre>");
      echo("
            <table border=0 cellpadding=5><tr><td>
            <form action=".$_SERVER["PHP_SELF"]." method=post enctype=multipart/form-data>
            <input type=hidden name=date value=".$_POST["date"].">
            <input type=submit name=send value=�������>
            </form>
            </td>
            <td>
            <form action=".$_SERVER["PHP_SELF"]." method=post>
            <input type=submit name=back value=�����>
            </form>
            </td></tr></table>
           ");
     } 
        

 
  } 
  else
  {

     
 ?>
 
<table width=100% height=100% border=0>
 <tr>
   <td align=center>
   <table bgcolor=black cellpadding=0 cellspacing=1>
     <tr>
	   <td bgcolor=silver>
	   <table width=300 cellpadding=5>
	     <tr>
		   <td>
	         <form enctype=multipart/form-data method=post action= 
	         
	         
	         	         <?php echo($_SERVER['PHP_SELF']); ?> >
	           <select name=date >
			   <?php
                           $valid_files=get_valid_files();
			   foreach ($valid_files as $key => $value)
			      {
	                       echo("<option value=".$value.">".$value."</option>");
			      }
			   ?>
	           </select>
			   <input type=submit value=���������>
			   </form>
		   </td>
		 </tr>
	   </table>

	   </td>
	 </tr>
   </table>
   </td>
 </tr>
</table>

<?php } ?>







