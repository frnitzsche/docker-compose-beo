<HTML>
<HEAD>
<TITLE>menu</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
</HEAD>
<BODY BGCOLOR=#FFFFFF LEFTMARGIN=0 TOPMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0>
<?php
################# including setting ########################

include("./settings.php");

######################################################
function get_previous_day($today,$day=1) {
  return date("ymd",strtotime($today." 13:00") - 86400*$day);
}
     $today=date("ymd");

     $NO_start_days=get_previous_day($today,$NO_previous_days);
     $NO2_start_days=get_previous_day($today,$NO2_previous_days);
     $O3_start_days=get_previous_day($today,$O3_previous_days);
     $gamma_start_days=get_previous_day($today,$gamma_previous_days);
     $neutrons_start_days=get_previous_day($today,$neutrons_previous_days);
	 $common_start_date=$today;
	 $common_stop_date=$today;


?>

<table border=0 cellpadding=0 cellspacing=0 background=images/line.jpg height=100%>
<tr><td align=top width=150 height=427>
   <img src="images/flat_menu_01.jpg" width="150" height="427" border="0" alt="" usemap="#flat_menu_01_Map">
</td></tr>
<tr><td>
</td></tr>
</table>

<map name="flat_menu_01_Map">
<area shape="poly" alt="" coords="0,386, 0,413, 5,418, 54,418, 59,412, 150,412, 150,386" href="#">
<area shape="poly" alt="" coords="0,356, 0,383, 5,388, 54,388, 59,382, 150,382, 150,356" href="./images/index.html?asddd" target="main">
<area shape="poly" alt="O3" coords="0,326, 0,353, 5,358, 54,358, 59,352, 150,352, 150,326" href="main.php?action=O3<?php echo("&start_date=".$O3_start_days."&stop_date=".$common_stop_date); ?>&type=barchart" target="main">
<area shape="poly" alt="NO2" coords="0,296, 0,323, 5,328, 54,328, 59,322, 150,322, 150,296" href="main.php?action=NO2<?php echo("&start_date=".$NO2_start_days."&stop_date=".$common_stop_date); ?>&type=barchart" target="main">
<area shape="poly" alt="NO" coords="0,266, 0,293, 5,298, 54,298, 59,292, 150,292, 150,266" href="main.php?action=NO<?php echo("&start_date=".$NO_start_days."&stop_date=".$common_stop_date); ?>&type=barchart" target="main">
<area shape="poly" alt="Neutrons" coords="0,236, 0,263, 5,268, 54,268, 59,262, 150,262, 150,236" href="main.php?action=harwell_neutron<?php echo("&start_date=".$neutrons_start_days."&stop_date=".$common_stop_date); ?>&type=barchart" target="main">
<area shape="poly" alt="Gamma" coords="0,206, 0,233, 5,238, 54,238, 59,232, 150,232, 150,206" href="main.php?action=harwell_gamma<?php echo("&start_date=".$gamma_start_days."&stop_date=".$common_stop_date); ?>&type=barchart" target="main">
<area shape="poly" alt="UV-B" coords="0,176, 0,203, 5,208, 54,208, 59,202, 150,202, 150,176" href="main.php?action=uv-b" target="main">
<area shape="poly" alt="UV-AB" coords="0,146, 0,173, 5,178, 54,178, 59,172, 150,172, 150,146" href="main.php?action=uv" target="main">
<area shape="poly" alt="Wind Direction" coords="0,116, 0,143, 5,148, 53,148, 59,142, 150,142, 150,116" href="main.php?action=vaisala_wind_direction" target="main">
<area shape="poly" alt="Wind Velocity" coords="0,86, 0,113, 5,118, 53,118, 59,112, 150,112, 150,86" href="main.php?action=vaisala_wind_velocity" target="main">
<area shape="poly" alt="Pressure" coords="0,56, 0,83, 5,88, 53,88, 59,82, 150,82, 150,56" href="main.php?action=vaisala_pressure" target="main">
<area shape="poly" alt="Temperature" coords="0,26, 0,53, 5,58, 53,58, 59,52, 150,52, 150,26" href="main.php?action=vaisala_temperature" target="main">
<area shape="poly" alt="All The Data For Today" coords="0,-1, 0,22, 6,28, 54,28, 60,22, 150,22, 150,-1" href="main.php?action=all" target="main">
</map>
</BODY>
</HTML>