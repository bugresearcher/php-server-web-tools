<?php
include ("config.php");
include ("scastxml.php");
echo'
<html>

<head>
<title>'.$title.'</title>
</head>
 <META content="'.$refresh.'" http-equiv=REFRESH>
<body text="'.$text.'" bgcolor="'.$bgrnd.'" link="'.$link.'" vlink="'.$vlink.'" alink="'.$alink.'">
  <font size="'.$hdrtext.'" face=arial><b><center>Pages refresh every '.$refresh.' seconds</center></font>
<table border=0 cellpadding=0 cellspacing=0 bgcolor="'.$tblhdr.'" width=100%>
  <tr>
    <td width=25%><a href="'.$listenlink.'"><img src=images/tunein.gif border="0"></a>
	&nbsp;<font size="'.$hdrtext.'" face=arial><b>'.$servertitle.'</center></td>
    <td width=25%><font size="'.$hdrtext.'" face=arial><b><center>Server is <img src=images/online.gif border="0"></center></td>
    <td width=25%><font size="'.$hdrtext.'" face=arial><b><center>Current Listeners '.$currentlisteners.'/'.$maxlisteners.'</center></td>
    <td width=25%><font size="'.$hdrtext.'" face=arial><b><center>Bitrate '.$bitrate.' kbps</center></td>
  </tr></font>
</table>
<table border=0 cellpadding=0 cellspacing=0 bgcolor="'.$tblhdr.'" width=100%>
  <tr>
    <td width=100% bgcolor="'.$cell.'"><font size="'.$hdrtext.'" face=arial><b>Currently Playing</td>
  </tr>
  <tr>
    <td width=100% bgcolor="'.$tblhdr.'"><font size="'.$bdytext.'" face=arial>('.$frmt_date[0].') '.$song[0].'</td>
  </tr>
  <tr>
    <td width=100% bgcolor="'.$cell.'"><font size="'.$hdrtext.'" face=arial><b>Past 6 Songs</td>
  </tr>
  <tr>
    <td width=100% bgcolor="'.$tblhdr.'"><font size="'.$bdytext.'" face=arial>1.('.$frmt_date[1].') '.$song[1].'<br>
	2.('.$frmt_date[2].') '.$song[2].'<br>
	3.('.$frmt_date[3].') '.$song[3].'<br>
	4.('.$frmt_date[4].') '.$song[4].'<br>
	5.('.$frmt_date[5].') '.$song[5].'<br>
	6.('.$frmt_date[6].') '.$song[6].'</td>
  </tr></font>
</table>';
// Leave $detailed to 1
if ($detailed == "1"){
    echo'
<font face=arial size="'.$bdytext.'"><a href="scastdetail.php">[Detailed Stats]</font></a>
<center><a target="_blank" href=http://www.mediacast1.com><img src=images/powered.gif border="0"></a></center><br>
</body>

</html>';
}
else {
    echo 'No detailed stats are available
    <center><a target="_blank" href=http://www.mediacast1.com><img src=images/powered.gif border="0"></a></center><br>
	</body>
</html>';
}


?>

