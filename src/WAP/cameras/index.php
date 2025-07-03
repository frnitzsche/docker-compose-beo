<?php
header("Content-type: text/vnd.wap.wml"); 
echo("<?xml version=\"1.0\"?>");
echo "<!DOCTYPE wml PUBLIC \"-//WAPFORUM//DTD WML 1.1//EN\""
   . " \"http://www.wapforum.org/DTD/wml_1.1.xml\">";
?>
<wml>
<card id="beo" title="BEO Cameras">
<p>Cameras View</p>
<p>
<anchor title="link">
  North view
  <go href="./north.php">
  </go>
</anchor>  
</p>
<p>
<anchor title="link">
  West view
  <go href="./west.php">
  </go>
</anchor>  
</p>
<p>
<anchor title="link">
  South view
  <go href="./south.php">
  </go>
</anchor>  
</p>
<p>
<anchor title="link">
  East view
  <go href="./east.php">
  </go>
</anchor>  
</p>
</card>
</wml>