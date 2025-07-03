<?php 

#################  This is session template  ####################
require("e:/www/guard/index.php");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>title</title>
<meta http-equiv="content-type" content="text/html; charset=windows-1251">
<style><!--

--></style>
<script language=JavaScript><!--

//--></script>
</head>
<body leftmargin="10" topmargin="10" marginwidth="10" marginheight="10">
<table border=0 width=100%><tr>
<?php

echo("<td width=1%><NOBR>Hello <b>".ucwords($_SESSION["username"])."</b>.</NOBR></td>");
echo("<td ><a href=./?rand=".rand()." >".$_SESSION["counter"]."</a></td>");
echo("<td  align=right><a href=./?logout=yes&rand=".time()."><span style=text-align:right>Logout</span></a></td></tr>");
echo("<tr><td><NOBR><a href=./acount/>Add new acount</a></NOBR></td>");
echo("<tr><td><NOBR><a href=./navigate/>Navigate through file system</a></NOBR></td>");

?>
</tr></table>
</body>
</html>