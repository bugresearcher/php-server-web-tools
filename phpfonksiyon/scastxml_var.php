<?
///////////////////////////////////////////////////////////////////////////////////////////////
// Author: dstjohn@mediacast1.com
///////////////////////////////////////////////////////////////////////////////////////////////
include ("config.php");
/////////////////////////////////////////////////////////////////////////////////////////////////
$listenlink = 'http://'.$host.':'.$port.'/listen.pls';  //make link to stream
$fp = @fsockopen("$host", $port, &$errno, &$errstr, 30); //open connection
if(!$fp) {
	$success=2;  //set if no connection
}
if($success!=2){ //if connection
 fputs($fp,"GET /7.html HTTP/1.0\r\nUser-Agent: SHOUTcast stats XML Parser (Mozilla Compatible)\r\n\r\n"); //get 7.html
 while(!feof($fp)) {
  $page .= fgets($fp, 1000);
 }
 @fclose($fp); //close connection
 $page = ereg_replace(".*<body>", "", $page); //extract data
 $page = ereg_replace("</body>.*", ",", $page); //extract data
 $numbers = explode(",",$page); //extract data
 $currentlisteners=$numbers[0]; //set variable
 $connected=$numbers[1]; //set variable

 if($connected==1) //if DSP is connected
  $wordconnected="yes"; //set variable
 else //if no DSP connection
  $wordconnected="no"; //set variable
 $peaklisteners=$numbers[2]; //set variable
 $maxlisteners=$numbers[3]; //set variable
 $reportedlisteners=$numbers[4]; //set variable

##Stuff printing out to screen may want to shut off.##
 echo('$listenlink = '.$listenlink.'<BR>');
 echo('$reportedlisteners = '.$reportedlisteners.'<BR>');
 echo('$maxlisteners = '.$maxlisteners.'<BR>');
 echo('$peaklisteners = '.$peaklisteners.'<BR>');
 echo('$connected = '.$connected.' <BR>');
 echo('$wordconnection = '.$wordconnected.'<BR>');
 echo('$currentlisteners = '.$currentlisteners.'<BR>');
##Okay stop commenting stuff out now.##
}

if($connected==1){  //only do if DSP is connected
 $fp = @fsockopen("$host", $port, &$errno, &$errstr, 30);  //open connection
  if(!$fp) { //if no connection
   $success1=2; //dummy variable to see if successful connect
  }
 if($success1!=2){ //only do if connected
  fputs($fp,"GET /index.html HTTP/1.0\r\nUser-Agent: SHOUTcast stats XML Parser (Mozilla Compatible)\r\n\r\n"); //get index.html
  while(!feof($fp)) {
   $page .= fgets($fp, 1000);
  }
  $pageed = ereg_replace(".*Stream is up at ", "", $page); //extract data
  $bitrate = ereg_replace(" kbps.*", "", $pageed); //extract data
  @fclose($fp); //close connection

 ##Print out bitrate.##
 echo('$bitrate = '.$bitrate.' <BR>');
 ##You know the drill stop here.##

 }
}

$fp = @fsockopen("$host", $port, &$errno, &$errstr, 30); //open connection yet again
 if(!$fp) {  //if connection
  $success2=2;
 }
if($success2!=2){ //if connected
 //for newer shoutcast servers
fputs ($fp, "GET /admin.cgi?mode=viewxml HTTP/1.1\r\nHost: $host:$port\r\n .
User-Agent: SHOUTcast Listener Stats (author: dstjohn@mediacast1.com)(Mozilla Compatible)\r\n .
Authorization: Basic ".base64_encode ("admin:$password")."\r\n\r\n");

 while(!feof($fp)) {
  $page .= fgets($fp, 1000);
 }

 $loop = array("AVERAGETIME", "SERVERGENRE", "SERVERURL", "SERVERTITLE", "SONGTITLE", "SONGURL", "IRC", "ICQ", "AIM", "WEBHITS", "STREAMHITS", "INDEX", "LISTEN", "PALM7", 
               "LOGIN", "LOGINFAIL", "PLAYED", "COOKIE", "ADMIN", "UPDINFO", "KICKSRC", "KICKDST", "UNBANDST", "BANDST", "VIEWBAN", "UNRIPDST", "VIEWRIP", "VIEWXML", 
              "VIEWLOG", "INVALID"); //define all the variables to get (delte any ones you don't want)
 $y=0; //dummy variable for while loop
 while($loop[$y]!=''){ //while there are things in loop
  $pageed = ereg_replace(".*<$loop[$y]>", "", $page); // extract data
  $phpname = strtolower($loop[$y]); //make names in loop lowercase for variable names
  $$phpname = ereg_replace("</$loop[$y]>.*", "", $pageed); //finish extracting data
  if($loop[$y]==SERVERGENRE || $loop[$y]==SERVERTITLE || $loop[$y]==SONGTITLE) //if for code clean-up (if you have problems with variables with URL encoding (i.e. %20 for space put them in this loop)
   $$phpname = urldecode($$phpname); // replace URL code with regular text (i.e. %20 = space)

 ##More stuff that prints##
 echo ('$'.$phpname.' = '.$$phpname.' <BR>');
 ##Stop here. Your server will be upset if you comment out the next line of code ($y++;).##

  $y++; //update dummy variable for while loop
 }
 $pageed = ereg_replace(".*<SONGHISTORY>", "", $page); //extract data
 $pageed = ereg_replace("<SONGHISTORY>.*", "", $pageed); //extract data
 $songatime = explode("<SONG>", $pageed); //break data down for each song
 $r=1; //dummy variable
 while($songatime[$r]!=""){ //while loop for each song
  $t=$r-1; //correction for first value in array from explode is worthless
  $playedat[$t] = ereg_replace(".*<PLAYEDAT>", "", $songatime[$r]); // extract data
  $playedat[$t] = ereg_replace("</PLAYEDAT>.*", "", $playedat[$t]); //extract data
  $song[$t] = ereg_replace(".*<TITLE>", "", $songatime[$r]); //extract data
  $song[$t] = ereg_replace("</TITLE>.*", "", $song[$t]); //extract data
  $song[$t] = urldecode($song[$t]); //cleans-up the URL code thing again
  $frmt_date[$t] = date('j/m/Y h:i:s A',$playedat[$t]);

 ##Yet even more crap that gets printed out.##
 echo ('$song['.$t.'] = '.$song[$t].' <BR>$playedat['.$t.'] = '.$frmt_date[$t].' <BR>');
 ##Same as last time.  Unhappy servers tend to hurt people.##

  $r++; //update loop variable
 }
 $pageed = ereg_replace(".*<LISTENERS>", "", $page); //extract data
 $pageed = ereg_replace("</LISTENERS>.*", "", $pageed); //extract data
 $listeninfo = explode("<LISTENER>", $pageed); //break apart data
 $r=1; //dummy loop variable
 while($listeninfo[$r]!=""){ //while loop for extraction
  $t=$r-1; //correction for first value in array from explode is worthless
  $hostname[$t] = ereg_replace(".*<HOSTNAME>", "", $listeninfo[$r]); //extract data
  $hostname[$t] = ereg_replace("</HOSTNAME>.*", "", $hostname[$t]); //extract data
  $useragent[$t] = ereg_replace(".*<USERAGENT>", "", $listeninfo[$r]); //extract data
  $useragent[$t] = ereg_replace("</USERAGENT>.*", "", $useragent[$t]);  //extract data
  $underruns[$t] = ereg_replace(".*<UNDERRUNS>", "", $listeninfo[$r]);  //extract data
  $underruns[$t] = ereg_replace("</UNDERRUNS>.*", "", $underruns[$t]);  //extract data
  $connecttime[$t] = ereg_replace(".*<CONNECTTIME>", "", $listeninfo[$r]);  //extract data
  $connecttime[$t] = ereg_replace("</CONNECTTIME>.*", "", $connecttime[$t]);  //extract data

 ##Yet even more crap that gets printed out.##
 echo ('$hostname['.$t.'] = '.$hostname[$t].' <BR>$useragent['.$t.'] = '.$useragent[$t].' <BR>$underruns['.$t.'] = '.$underruns[$t].' <BR>$connecttime['.$t.'] = '.$connecttime[$t].'<BR>');
 ##STOP.  Just think that was the last set of echos you have to comment out.##

  $r++;  //update loop variable
 }
 fclose($fp);  //close connection 
}
?>
