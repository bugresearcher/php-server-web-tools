<?php
ini_set('max_execution_time','10');
////////////////////////
// Configuration file //
////////////////////////
// Shoutcast server ip, port number and password
$host = "127.0.0.1";
$port = "8000";
$password = "changeme";
// End shoutcast server config
// Default configuration if server is down
$title = "Latex Radio";  // Title of radio station, use same as shoutcast dsp plug-in
$cstmsg = "Our server is temporarily down, please stop back soon!!"; // Custom message you want to tell your listeners if server is offline
// End
// Config for look and feel
$bgrnd = "000000";          // Color of page background
$text = "ffffff";           // Text Color
$link = "E3690F";           // Link color
$vlink = "FF0000";          // Visited link color
$alink = "800080";          // Active link color
$tblhdr = "333333";         // Table header color
$cell = "767676";           // Table cell color for body
$hdrtext = "2";             // Size of text in header
$bdytext = "1";             // Size of text in the rest of the page
// End
// Misc Config
$reset = "2";               // How often in days are stats (SERVER) reset, default is 3
$detailed = "1";            // Detailed stats on or off (1=on) (0=off)
$djname = "Dj Pulse";       // Your dj name dhu!!
$refresh = "30";            // How often in seconds should the pages refresh themselves
?>
