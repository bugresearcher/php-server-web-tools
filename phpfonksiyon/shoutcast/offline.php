<?php
include ("config.php");
include ("scastxml.php");
echo"
<html>

<head>
<title>$title</title>
</head>
 <META content=$refresh http-equiv=REFRESH>
<body text=$text bgcolor=$bgrnd>
  <font size=$hdrtext face=arial><b><center>Pages refresh every $refresh seconds</center></font>
<table border=0 cellpadding=0 cellspacing=0 bgcolor=$tblhdr width=100%>
  <tr>
    <td width=25%><font size=$hdrtext face=arial><b><center>$title</center></td>
    <td width=25%><font size=$hdrtext face=arial><b><center>Server is <img src=images/offline.gif></center></td>
  </tr></font>
</table>
 <table border=0 cellpadding=0 cellspacing=0 bgcolor=$cell width=100%><tr>
 <td width=100%><center><font size=$bdytext face=arial><b>$cstmsg</font></center></td></tr></table>
 <a href=http://www.mediacast1.com><img src=images/powered.gif border=0></a>";
?>

