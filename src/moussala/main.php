<?php 

#################  This is session template  ####################
//require("e:/www/guard/index.php");




###################   Inluding settings file#######################
include("./settings.php");





#########################################################


?>

<html>
<head>
 <title>Data from Moussala</title>
<link rel="alternate" type="application/rss+xml" title="Live Data from Moussala" href="rss.xml" />
</head>
<body bgcolor=ffffff leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php


function shadow($content) {
  $table="
  <TABLE border=0 cellPadding=0 cellSpacing=0 width=1 align=center>
    <TR vAlign=top>
      <TD width=1>".$content."</TD>
      <TD background=./shadows/right.gif width=7 height=8><IMG src=./shadows/top_right.gif width=7 height=8></TD>
    </TR>
    <TR>
      <TD background=./shadows/bottom.gif width=1><IMG height=8 src='./shadows/bottom_left.gif' width=8></TD>
      <TD width=7><IMG height=8 src='./shadows/bottom_right.gif' width=7></TD>
    </TR>
  </TABLE>";
  return $table;
}


if (isset($_POST["max"]) && ereg("^[0123456789.-]{1,}$",$_POST["max"])) {
  $max_value=$_POST["max"];
}
else {
  $max_value="";
  $_POST["max"]="";
}

if (isset($_POST["min"]) && ereg("^[0123456789.-]{1,}$",$_POST["min"])) {
  $min_value=$_POST["min"];
}
else {
  $min_value="";
  $_POST["min"]="";
}

if (isset($_POST["reset"])) {
  $_POST["width"]=500;
  $_POST["height"]=300;
  $_POST["type"]="lines";
  $max_value="";
  $_POST["max"]="";
  $min_value="";
  $_POST["min"]="";
}
if (isset($_GET{"action"})) {
?>
<table width=100% height=100% border=0>
<form method=post action=./main.php?action=<?php echo $_GET["action"] ?> >
<tr>
<td rowspan=3 width=1>

<?php
if ($_GET["action"]!="all") 
{
?>
    <table border=0>
     <tr>
      <td align=center>max</td>
     </tr>
     <tr>
      <td>

    	 <input type=text size=6 name=max value=<?php echo $max_value ?> >

      </td>
     </tr>
     <tr>
       <td height=100>
       &nbsp;
       </td>
     </tr>
     <tr>
       <td align=center>
       min
       </td>
      </tr>
      <tr>
        <td>
    	  <input type=text size=6 name=min value=<?php echo $min_value ?>  >
	</td>
      </tr>
    </table>
<?php
}
?>
</td>
<td height=1>
<?php }


function get_previous_day($today,$day=1) {
  return date("ymd",strtotime($today." 13:00") - 86400*$day);
}

function get_next_day($today) {
  return date("ymd",strtotime($today)." 13:00" + 86400);
}


if (isset($_POST["max"]) && $_POST["max"]!="") {
  $max_string="&max_value=".$_POST["max"];
}
else {
  $max_string="";
}

if (isset($_POST["min"]) && $_POST["min"]!="") {
  $min_string="&min_value=".$_POST["min"];
}
else {
  $min_string="";
}


if (isset($_GET["start_date"]) && !isset($_GET["stop_date"])) {
  $_GET["stop_date"]=$_GET["start_date"];
}

if (isset($_GET["width"]) && !isset($_POST["width"])) {
  $_POST["width"]=$_GET["width"];
}

if (isset($_GET["height"]) && !isset($_POST["height"])) {
  $_POST["height"]=$_GET["height"];
}

if (isset($_GET["type"]) && !isset($_POST["type"])) {
  $_POST["type"]=$_GET["type"];
}

if (isset($_POST["start_day"]) && isset($_POST["stop_day"]) && !isset($_GET["start_date"]) && !isset($_GET["stop_date"])) {
   
}


if (isset($_GET["start_date"]) && isset($_GET["stop_date"]) && !isset($_POST["start_day"]) && !isset($_POST["stop_day"])) {

    if (isset($_GET["next"])) {
      if ($_GET["start_date"] > $_GET["stop_date"]) {
        $_GET["start_date"]=$_GET["stop_date"];
      }
    $_GET["start_date"]=get_next_day($_GET["start_date"]);
    $_GET["stop_date"]=get_next_day($_GET["stop_date"]);	
   }

    if ($_GET["start_date"] > $_GET["stop_date"]) {
      $_GET["stop_date"]=$_GET["start_date"];
    }
  
    ereg("([0123456789]{2})([0123456789]{2})([0123456789]{2})",$_GET["start_date"],$start_arr);
    $_POST["start_year"]="20".$start_arr[1];
    $_POST["start_month"]=$start_arr[2];
    $_POST["start_day"]=$start_arr[3];

    ereg("([0123456789]{2})([0123456789]{2})([0123456789]{2})",$_GET["stop_date"],$stop_arr);
    $_POST["stop_year"]="20".$stop_arr[1];
    $_POST["stop_month"]=$stop_arr[2];
    $_POST["stop_day"]=$stop_arr[3];
}

if (isset($_POST["start_day"]) && isset($_POST["start_month"]) && isset($_POST["start_year"]) && isset($_POST["stop_day"]) && isset($_POST["stop_month"]) && isset($_POST["stop_year"])) {
  $start_date_post=(integer) $start_date_post=$_POST["start_year"].$_POST["start_month"].$_POST["start_day"];
  $stop_date_post=(integer) $stop_date_post=$_POST["stop_year"].$_POST["stop_month"].$_POST["stop_day"];
 
 

 
  if (isset($_POST["next"])) {
    if ($start_date_post > $stop_date_post) {
	  $start_date_post=$stop_date_post;
	}
    $start_date_post="20".get_next_day($start_date_post); 
	$stop_date_post="20".get_next_day($stop_date_post);
  }
  
  if (isset($_POST["back"])) {
     if ($start_date_post > $stop_date_post) {
	    $start_date_post=$stop_date_post;
	}
   $start_date_post="20".get_previous_day($start_date_post); 
   $stop_date_post="20".get_previous_day($stop_date_post); 
  }
  ereg("([0123456789]{4})([0123456789]{2})([0123456789]{2})",$start_date_post,$start_arr_temp);
  ereg("([0123456789]{4})([0123456789]{2})([0123456789]{2})",$stop_date_post,$stop_arr_temp);
  $_POST["start_year"]=$start_arr_temp[1];
  $_POST["start_month"]=$start_arr_temp[2];
  $_POST["start_day"]=$start_arr_temp[3];
  $_POST["stop_year"]=$stop_arr_temp[1];
  $_POST["stop_month"]=$stop_arr_temp[2];
  $_POST["stop_day"]=$stop_arr_temp[3];

  if ($start_date_post > $stop_date_post) {
    $_POST["stop_day"]=$_POST["start_day"];
	$_POST["stop_month"]=$_POST["start_month"];
	$_POST["stop_year"]=$_POST["start_year"];
  }

}

$today=date("ymd");
preg_match("/([0123456789]{2})([0123456789]{2})([0123456789]{2})/",$today,$today_arr);
$today_year=$today_arr[1];
$today_month=$today_arr[2];
$today_day=$today_arr[3];

if (isset($_GET["action"]))
  {

	if (isset($_POST["start_year"]) && isset($_POST["start_month"]) && isset($_POST["start_day"]) && isset($_POST["stop_year"]) && isset($_POST["stop_month"]) && isset($_POST["stop_day"]))
	  {
	    $_POST["start_year"]=preg_replace("/^[0123456789]{2}/","",$_POST["start_year"]);
	    $_POST["stop_year"]=preg_replace("/^[0123456789]{2}/","",$_POST["stop_year"]);		
	    $date="&start_date=".$_POST["start_year"].$_POST["start_month"].$_POST["start_day"]."&stop_date=".$_POST["stop_year"].$_POST["stop_month"].$_POST["stop_day"];
	  }
	else
	  {
	    $date="&start_date=".$today;
		
		$_POST["start_year"]=$today_year;
		$_POST["start_month"]=$today_month;
		$_POST["start_day"]=$today_day;
		
	    $_POST["stop_year"]=$today_year;
		$_POST["stop_month"]=$today_month;
		$_POST["stop_day"]=$today_day;
	  }
	  
  if (!isset($_GET["start_date"]) && !isset($_GET["stop_date"])) {
     $start_year_two_digits=preg_replace("/20/","",$_POST["start_year"]);
     $stop_year_two_digits=preg_replace("/20/","",$_POST["stop_year"]);
     $_GET["start_date"]=$start_year_two_digits.$_POST["start_month"].$_POST["start_day"];
     $_GET["stop_date"]=$stop_year_two_digits.$_POST["stop_month"].$_POST["stop_day"];
  }

function show_graphics($parameters,$device) {
  global $width,$height;
  echo(shadow("<a href=./main.php?action=".$device."><IMG src=./graphics.php?".$parameters." width=".$width." height=".$height." border=0 ></a>"));
}


function space($number=1) {
  for ($i=0;$i<$number;$i++)
    {
	  echo("\r\n<br><br>\r\n");
	}
}
function show_all() {
  global $date,$width,$height,$type,$today,$neutrons_previous_days,$gamma_previous_days,$O3_previous_days,$NO2_previous_days,$NO_previous_days;
  if ($_GET["start_date"] == $_GET["stop_date"]) {
     $NO_start_days=get_previous_day($_GET["start_date"],$NO_previous_days);
     $NO2_start_days=get_previous_day($_GET["start_date"],$NO2_previous_days);
     $O3_start_days=get_previous_day($_GET["start_date"],$O3_previous_days);
     $gamma_start_days=get_previous_day($_GET["start_date"],$gamma_previous_days);
     $neutrons_start_days=get_previous_day($_GET["start_date"],$neutrons_previous_days);
     $common_start_date=$_GET["start_date"];
     $common_stop_date=$_GET["stop_date"];
  }
  else {
     $NO_start_days=$_GET["start_date"];
     $NO2_start_days=$_GET["start_date"];
     $O3_start_days=$_GET["start_date"];
     $gamma_start_days=$_GET["start_date"];
     $neutrons_start_days=$_GET["start_date"];
	 $common_start_date=$_GET["start_date"];
	 $common_stop_date=$_GET["stop_date"];
  }

  show_graphics("device=vaisala_temperature&start_date=".$common_start_date."&stop_date=".$common_stop_date."&width=".$width."&height=".$height."&type=".$type,"vaisala_temperature".$date); space();
  show_graphics("device=vaisala_pressure&start_date=".$common_start_date."&stop_date=".$common_stop_date."&width=".$width."&height=".$height."&type=".$type,"vaisala_pressure".$date); space();
  show_graphics("device=vaisala_wind_velocity&start_date=".$common_start_date."&stop_date=".$common_stop_date."&width=".$width."&height=".$height."&type=".$type,"vaisala_wind_velocity".$date); space();
  show_graphics("device=vaisala_wind_direction&start_date=".$common_start_date."&stop_date=".$common_stop_date."&width=".$width."&height=".$height."&type=".$type,"vaisala_wind_direction".$date); space();
  show_graphics("device=vaisala_humidity&start_date=".$common_start_date."&stop_date=".$common_stop_date."&width=".$width."&height=".$height."&type=".$type,"vaisala_humidity".$date); space();
  show_graphics("device=vaisala_precipitations&start_date=".$common_start_date."&stop_date=".$common_stop_date."&width=".$width."&height=".$height."&type=".$type,"vaisala_precipitations".$date); space();
  show_graphics("device=uv&start_date=".$common_start_date."&stop_date=".$common_stop_date."&width=".$width."&height=".$height."&type=".$type,"uv".$date); space();
  show_graphics("device=uv-b&start_date=".$common_start_date."&stop_date=".$common_stop_date."&width=".$width."&height=".$height."&type=".$type,"uv-b".$date); space();
  show_graphics("device=liulin_dose&start_date=".$common_start_date."&stop_date=".$common_stop_date."&width=".$width."&height=".$height."&type=".$type,"liulin_dose".$date); space();  
  show_graphics("device=liulin_flux&start_date=".$common_start_date."&stop_date=".$common_stop_date."&width=".$width."&height=".$height."&type=".$type,"liulin_flux".$date); space();    
  show_graphics("device=environ_no&start_date=".$NO_start_days."&stop_date=".$common_stop_date."&width=".$width."&height=".$height."&type=barchart","NO&start_date=".$NO_start_days."&stop_date=".$common_stop_date."&type=barchart"); space();
  show_graphics("device=environ_no2&start_date=".$NO2_start_days."&stop_date=".$common_stop_date."&width=".$width."&height=".$height."&type=barchart","NO2&start_date=".$NO2_start_days."&stop_date=".$common_stop_date."&type=barchart"); space();
  show_graphics("device=environ_o3&start_date=".$O3_start_days."&stop_date=".$common_stop_date."&width=".$width."&height=".$height."&type=barchart","O3&start_date=".$O3_start_days."&stop_date=".$common_stop_date."&type=barchart"); space();
  show_graphics("device=harwell_gamma&start_date=".$gamma_start_days."&stop_date=".$common_stop_date."&width=".$width."&height=".$height."&type=barchart","harwell_gamma&start_date=".$gamma_start_days."&stop_date=".$common_stop_date."&type=barchart"); space();
  show_graphics("device=harwell_neutron&start_date=".$neutrons_start_days."&stop_date=".$common_stop_date."&width=".$width."&height=".$height."&type=barchart","harwell_neutron&start_date=".$neutrons_start_days."&stop_date=".$common_stop_date."&type=barchart"); space();
######  
#####  
  
}

function show_controls() {
global $today, $start_year_data;


for ($i=1;$i<32;$i++) {
    if (strlen($i)==1) {$i="0".$i;}
    $temp="d".$i;
	$temp_s="sd".$i;
	$$temp="";
    $$temp_s="";
  }
  for ($i=1;$i<13;$i++) {
    if (strlen($i)==1) {$i="0".$i;}
    $temp_s="sm".$i; 
    $temp="m".$i;
	$$temp="";
	$$temp_s="";
  }
  $year=date("Y");
  for ($i=2002;$i<=$year;$i++) {
    $temp="y".$i;
	$temp_s="sy".$i ;
	$$temp="";
    $$temp_s=""; 
  }

	$start_year="y20".$_POST["start_year"];
	$$start_year="selected";
	
	$start_month="m".$_POST["start_month"];
	$$start_month="selected";
	
    $start_day="d".$_POST["start_day"];
	$$start_day="selected";

	$stop_year="sy20".$_POST["stop_year"];
	$$stop_year="selected";
	
	$stop_month="sm".$_POST["stop_month"];
	$$stop_month="selected";
	
    $stop_day="sd".$_POST["stop_day"];
	$$stop_day="selected";

  echo("

  <table border=0 align=center>
    <tr>
	  <td>
	  <input type=submit value=GO width=0 height=0>
	    From <select name=start_day>
		  <option value=01 ".$d01.">1</option>
		  <option value=02 ".$d02.">2</option>
		  <option value=03 ".$d03.">3</option>
		  <option value=04 ".$d04.">4</option>
		  <option value=05 ".$d05.">5</option>
		  <option value=06 ".$d06.">6</option>
		  <option value=07 ".$d07.">7</option>
		  <option value=08 ".$d08.">8</option>
		  <option value=09 ".$d09.">9</option>
		  <option value=10 ".$d10.">10</option>
		  <option value=11 ".$d11.">11</option>
		  <option value=12 ".$d12.">12</option>
		  <option value=13 ".$d13.">13</option>
		  <option value=14 ".$d14.">14</option>
		  <option value=15 ".$d15.">15</option>
		  <option value=16 ".$d16.">16</option>
		  <option value=17 ".$d17.">17</option>
		  <option value=18 ".$d18.">18</option>
		  <option value=19 ".$d19.">19</option>
		  <option value=20 ".$d20.">20</option>
		  <option value=21 ".$d21.">21</option>
		  <option value=22 ".$d22.">22</option>
		  <option value=23 ".$d23.">23</option>
		  <option value=24 ".$d24.">24</option>
		  <option value=25 ".$d25.">25</option>
		  <option value=26 ".$d26.">26</option>
		  <option value=27 ".$d27.">27</option>
		  <option value=28 ".$d28.">28</option>
		  <option value=29 ".$d29.">29</option>
		  <option value=30 ".$d30.">30</option>
		  <option value=31 ".$d31.">31</option>		 
		</select>
	  </td>
	  <td>
	     <select name=start_month>
		  <option value=01 ".$m01.">Jan</option>
		  <option value=02 ".$m02.">Feb</option>
		  <option value=03 ".$m03.">Mar</option>
		  <option value=04 ".$m04.">Apr</option>
		  <option value=05 ".$m05.">May</option>
		  <option value=06 ".$m06.">June</option>
		  <option value=07 ".$m07.">July</option>
		  <option value=08 ".$m08.">Aug</option>
		  <option value=09 ".$m09.">Sep</option>
		  <option value=10 ".$m10.">Oct</option>
		  <option value=11 ".$m11.">Nov</option>
		  <option value=12 ".$m12.">Dec</option>
		</select>
	  </td>
	  <td>
	<select name=start_year>\r\n");
    $this_year=date("Y");
	echo("\r\nstart_year is ".$start_year_data);
for ($i=$start_year_data;$i<=$this_year;$i++) {
  $year_selected="y".$i;
  echo("<option value=".$i." ".$$year_selected.">".$i."</option>\r\n");
}
		echo("
     </select>
	  </td>
	  <td width=5>
	  </td>
	  <td width=0>
	    <input type=submit name=back value=&#60&#60>
	  </td>
	  <td width=0>
	    <input type=submit name=next value=&#62&#62 >
	  </td>	 
      <td width=5>
	    
	  </td>	  
	  <td>
	    To
		<select name=stop_day>
		  <option value=01 ".$sd01.">1</option>
		  <option value=02 ".$sd02.">2</option>
		  <option value=03 ".$sd03.">3</option>
		  <option value=04 ".$sd04.">4</option>
		  <option value=05 ".$sd05.">5</option>
		  <option value=06 ".$sd06.">6</option>
		  <option value=07 ".$sd07.">7</option>
		  <option value=08 ".$sd08.">8</option>
		  <option value=09 ".$sd09.">9</option>
		  <option value=10 ".$sd10.">10</option>
		  <option value=11 ".$sd11.">11</option>
		  <option value=12 ".$sd12.">12</option>
		  <option value=13 ".$sd13.">13</option>
		  <option value=14 ".$sd14.">14</option>
		  <option value=15 ".$sd15.">15</option>
		  <option value=16 ".$sd16.">16</option>
		  <option value=17 ".$sd17.">17</option>
		  <option value=18 ".$sd18.">18</option>
		  <option value=19 ".$sd19.">19</option>
		  <option value=20 ".$sd20.">20</option>
		  <option value=21 ".$sd21.">21</option>
		  <option value=22 ".$sd22.">22</option>
		  <option value=23 ".$sd23.">23</option>
		  <option value=24 ".$sd24.">24</option>
		  <option value=25 ".$sd25.">25</option>
		  <option value=26 ".$sd26.">26</option>
		  <option value=27 ".$sd27.">27</option>
		  <option value=28 ".$sd28.">28</option>
		  <option value=29 ".$sd29.">29</option>
		  <option value=30 ".$sd30.">30</option>
		  <option value=31 ".$sd31.">31</option>		 
		</select>
	  </td>
	  <td>
	     <select name=stop_month>
		  <option value=01 ".$sm01.">Jan</option>
		  <option value=02 ".$sm02.">Feb</option>
		  <option value=03 ".$sm03.">Mar</option>
		  <option value=04 ".$sm04.">Apr</option>
		  <option value=05 ".$sm05.">May</option>
		  <option value=06 ".$sm06.">June</option>
		  <option value=07 ".$sm07.">July</option>
		  <option value=08 ".$sm08.">Aug</option>
		  <option value=09 ".$sm09.">Sep</option>
		  <option value=10 ".$sm10.">Oct</option>
		  <option value=11 ".$sm11.">Nov</option>
		  <option value=12 ".$sm12.">Dec</option>
		</select>
	  </td>
	  <td>
	<select name=stop_year>\r\n");
$this_year=date("Y");

for ($i=$start_year_data;$i<=$this_year;$i++) {
  $year_selected="sy".$i;
  echo("<option value=".$i." ".$$year_selected.">".$i."</option>\r\n");
}
		echo("</select>
	  </td>
	  <td>
	    &nbsp;<input type=submit value=GO default>
	  </td>
	</tr>

  </table>

  ");
}









    show_controls();
	echo("</td></tr>");	
	echo("<tr height=100%><td>");
    if (isset($_POST["width"]) && $_POST["width"] > 500)
      {
        $width=$_POST["width"];
      }
    else
      {
	    $width=500;
	  }
	  
    if (isset($_POST["height"]) && $_POST["height"] > 300)
      {
        $height=$_POST["height"];
      }
    else
      {
	    $height=300;
	  }
  
    $lines="";
	$dots="";
	$combined="";
	$dashed="";
	$barchart="";
  
    if (isset($_POST["type"])) {
	  switch ($_POST["type"]) {
	    case "lines" : $type="lines"; $lines="selected"; break;
	    case "dots" : $type="dots"; $dots="selected"; break;
	    case "combined" : $type="combined"; $combined="selected"; break;
	    case "dashed" : $type="dashed"; $dashed="selected"; break;
		case "barchart" : $type="barchart"; $barchart="selected"; break;
		default : $type="lines"; $lines="selected";
	  }
	}
	else {
	  $type="lines";
	  $lines="selected";
	}
  
   switch ($_GET["action"]) 
      {
       case "vaisala_temperature" : show_graphics("device=vaisala_temperature".$date."&width=".$width."&height=".$height."&type=".$type.$min_string.$max_string,"vaisala_temperature".$date."&next=1&width=".$width."&height=".$height."&type=".$type); break;
       case "vaisala_pressure" : show_graphics("device=vaisala_pressure".$date."&width=".$width."&height=".$height."&type=".$type.$min_string.$max_string,"vaisala_pressure".$date."&next=1&width=".$width."&height=".$height."&type=".$type); break;
       case "vaisala_wind_velocity" : show_graphics("device=vaisala_wind_velocity".$date."&width=".$width."&height=".$height."&type=".$type.$min_string.$max_string,"vaisala_wind_velocity".$date."&next=1&width=".$width."&height=".$height."&type=".$type); break;
       case "vaisala_wind_direction" : show_graphics("device=vaisala_wind_direction".$date."&width=".$width."&height=".$height."&type=".$type.$min_string.$max_string,"vaisala_wind_direction".$date."&next=1&width=".$width."&height=".$height."&type=".$type); break;       
       case "vaisala_humidity" : show_graphics("device=vaisala_humidity".$date."&width=".$width."&height=".$height."&type=".$type.$min_string.$max_string,"vaisala_humidity".$date."&next=1&width=".$width."&height=".$height."&type=".$type); break;       
       case "vaisala_precipitations" : show_graphics("device=vaisala_precipitations".$date."&width=".$width."&height=".$height."&type=".$type.$min_string.$max_string,"vaisala_precipitations".$date."&next=1&width=".$width."&height=".$height."&type=".$type); break;       
       case "harwell_gamma" : show_graphics("device=harwell_gamma".$date."&width=".$width."&height=".$height."&type=".$type.$min_string.$max_string,"harwell_gamma".$date."&next=1&width=".$width."&height=".$height."&type=".$type); break;
       case "harwell_neutron" : show_graphics("device=harwell_neutron".$date."&width=".$width."&height=".$height."&type=".$type.$min_string.$max_string,"harwell_neutron".$date."&next=1&width=".$width."&height=".$height."&type=".$type); break;
       case "uv" : show_graphics("device=uv".$date."&width=".$width."&height=".$height."&type=".$type.$min_string.$max_string,"uv".$date."&next=1&width=".$width."&height=".$height."&type=".$type); break;
       case "uv-b" : show_graphics("device=uv-b".$date."&width=".$width."&height=".$height."&type=".$type.$min_string.$max_string,"uv-b".$date."&next=1&width=".$width."&height=".$height."&type=".$type); break;	   
       case "NO" : show_graphics("device=environ_no".$date."&width=".$width."&height=".$height."&type=".$type.$min_string.$max_string,"NO".$date."&next=1&width=".$width."&height=".$height."&type=".$type); break;
       case "NO2" : show_graphics("device=environ_no2".$date."&width=".$width."&height=".$height."&type=".$type.$min_string.$max_string,"NO2".$date."&next=1&width=".$width."&height=".$height."&type=".$type); break;
       case "O3" : show_graphics("device=environ_o3".$date."&width=".$width."&height=".$height."&type=".$type.$min_string.$max_string,"O3".$date."&next=1&width=".$width."&height=".$height."&type=".$type); break;       
       case "liulin_dose" : show_graphics("device=liulin_dose".$date."&width=".$width."&height=".$height."&type=".$type.$min_string.$max_string,"liulin_dose".$date."&next=1&width=".$width."&height=".$height."&type=".$type); break;
       case "liulin_flux" : show_graphics("device=liulin_flux".$date."&width=".$width."&height=".$height."&type=".$type.$min_string.$max_string,"liulin_flux".$date."&next=1&width=".$width."&height=".$height."&type=".$type); break;       
       case "all" : show_all(); break;
      }
	if ($_GET["action"] != "all") {
	  if (isset($_POST["lock"]) ) {
	  echo("ass");
	     $lock_width="disabled";  
		 $lock_height="disabled";  
	     $lock_type="disabled"; 
         $lock="unlock";		 
	  }
	  else {
	    $lock_width="";
	    $lock_height="";
	    $lock_type="";
		$lock="lock";
	  }
	echo("
	</td></tr><tr><td>
	<table align=center border=0>
	  <tr>
		<td>
		  <input type=submit name=reset value=Reset >
		</td>
		<td width=0>
		  &nbsp;
		</td>		
	    <td>
		  Width&nbsp;	  
		</td>
	    <td>
          <input type=text size=6 name=width value=".$width." maxlength=4 ".$lock_width.">
		</td>
		<td width=0>
		  &nbsp;
		</td>
	    <td>
		  Height&nbsp;
		</td>
		<td>
          <input type=text size=6 name=height value=".$height." maxlength=3  ".$lock_height.">
		</td>
		<td width=0>
		  &nbsp;
		</td>
	    <td>
		 Type&nbsp;
		</td>
	    <td>
		 <select name=type  ".$lock_type.">
		  <option value=lines ".$lines.">Line</option>
		  <option value=dots ".$dots.">Dots</option>
		  <option value=combined ".$combined.">Combined</option>
		  <option value=barchart ".$barchart.">Bar chart</option>
		</select>
		</td>
	    <td width=0>
		  &nbsp;
		</td>
		<td>
		  <input type=submit value=GO >
		</td>
	  </tr>
	  	  </form>
	</table>
		");
	}
	else {
	echo("</form>");
	}
  }
else
  {
    echo("
	   <table border=0 width=100% height=100%>
	    <tr>
		  <td  align=center>");
		    echo shadow("<img src=./images/BEO.jpg >");
		  echo("</td>
		</tr>
	   </table>
	");
  }

?>
</td></tr></table>
</body>
</html>