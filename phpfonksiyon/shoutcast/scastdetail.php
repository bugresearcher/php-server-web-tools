 <?php
include ("config.php");
include ("scastxml.php");
echo"
<html>

<head>
<title>$title</title>
</head>
 <META content=$refresh http-equiv=REFRESH>
<body text=$text bgcolor=$bgrnd link=$link vlink=$vlink alink=$alink>
  <font size=$hdrtext face=arial><b><center>Pages refresh every $refresh seconds</center></font>
<table border=0 cellpadding=0 cellspacing=0 bgcolor=$tblhdr width=100%>
  <tr>
    <td width=25%><a href=$listenlink><img src=images/tunein.gif border=0></a>&nbsp;<font size=$hdrtext face=arial><b>$servertitle</center></td>
    <td width=25%><font size=$hdrtext face=arial><b><center>Server is <img src=images/online.gif></center></td>
    <td width=25%><font size=$hdrtext face=arial><b><center>Current Listeners $currentlisteners/$maxlisteners</center></td>
    <td width=25%><font size=$hdrtext face=arial><b><center>Bitrate $bitrate kbps</center></td>
  </tr></font>
</table>
<table border=0 cellpadding=0 cellspacing=0 bgcolor=$tblhdr width=100%>
  <tr>
    <td width=100% bgcolor=$cell><font size=$hdrtext face=arial><b>Currently Playing</td>
  </tr>
  <tr>
    <td width=100% bgcolor=$tblhdr><font size=$bdytext face=arial>$song[0]</td>
  </tr>";
// Note if you do not want the last 6 songs showing just comment out this echo statement with /*c-style*/ comments
  echo"<tr>
    <td width=100% bgcolor=$cell><font size=$hdrtext face=arial><b>Past 6 Songs</td>
  </tr>
  <tr>
    <td width=100% bgcolor=$tblhdr><font size=$bdytext face=arial>1.$song[1]<br>2.$song[2]<br>3.$song[3]<br>4.$song[4]<br>5.$song[5]<br>6.$song[6]</td>
  </tr></font>
</table>";
// Detailed Stats
echo"<br><br>
<b><center><font face=Arial size=$hdrtext>Stats reset every $reset days</font></b><table border=0 cellpadding=0 cellspacing=0 width=100%>
  <tr>
    <td width=31% bgcolor=$tblhdr><font size=$hdrtext face=arial><b>Total Listeners</td>
    <td width=100% bgcolor=$tblhdr><font size=$bdytext face=arial>$streamhits</td>
  </tr>
  <tr>
    <td width=31% bgcolor=$cell><font size=$hdrtext face=arial><b>Total hits to server</td>
    <td width=100% bgcolor=$cell><font size=$bdytext face=arial>$webhits</td>
  </tr>
  <tr>
    <td width=31% bgcolor=$tblhdr><font size=$hdrtext face=arial><b>$servertitle can handle</td>
    <td width=100% bgcolor=$tblhdr><font size=$bdytext face=arial>$maxlisteners Listeners</td>
  </tr>
  <tr>
    <td width=31% bgcolor=$cell><font size=$hdrtext face=arial><b>Dj's Icq</td>
    <td width=100% bgcolor=$cell><font size=$bdytext face=arial><a href=http://www.icq.com/scripts/contact.dll?msgto=$icq>Contact $djname via ICQ</td>
  </tr>
</table>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
  <tr>
    <td width=31% bgcolor=$tblhdr><font size=$hdrtext face=arial><b>Dj's Aol IM handle</td>
    <td width=100% bgcolor=$tblhdr><font size=$bdytext face=arial><a href=aim:goim?screenname=$aim>Contact $djname via Aol Instant Messanger</td>
  </tr>
  <tr>
    <td width=31% bgcolor=$cell><font size=$hdrtext face=arial><b>Internet relay chat</td>
    <td width=100% bgcolor=$cell><font size=$bdytext face=arial><a href=http://www.shoutcast.com/chat.phtml?dc=$irc>Chat with $djname</td>
  </tr>
  <tr>
    <td width=31% bgcolor=$tblhdr><font size=$hdrtext face=arial><b>Average time spent listening</td>
    <td width=100% bgcolor=$tblhdr><font size=$bdytext face=arial>$averagetime Seconds</td>
  </tr>
  <tr>
    <td width=31% bgcolor=$cell><font size=$hdrtext face=arial><b>Genre for this station</td>
    <td width=100% bgcolor=$cell><font size=$bdytext face=arial>$servergenre</td>
  </tr>
   <tr>
    <td width=31% bgcolor=$tblhdr><font size=$hdrtext face=arial><b>Peak Stats</font></td>
    <td width=100% bgcolor=$tblhdr><font size=$bdytext face=arial>$servertitle has peaked @ $peaklisteners total listeners</td>
  </tr> </font>
</table><a href=http://www.mediacast1.com><img src=images/powered.gif border=0></a>";
?>
