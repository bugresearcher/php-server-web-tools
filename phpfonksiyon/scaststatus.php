<?php
include ("config.php");
include ("scastxml.php");

if($success!=2){
   include("online.php");    //server is on
}
else {
   include ("offline.php");  //Server is down
}

?>


