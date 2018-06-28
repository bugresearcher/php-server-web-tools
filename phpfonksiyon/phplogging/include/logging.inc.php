<?php
/**
* Author: Richard Brierton
* WWW: www.allhype.co.uk
* Contact: www.allhype.co.uk/contact/
*
* Date Created: July 21st 2004. 12:26
* Last Updated: July 21st 2004. 22:07
* Version: 1.0
* logging.inc.php Logging Class
* Requires logging.conf.php
*/

$gotConf = @include("logging.conf.php");

class logging {

/**
* Request to add the supplied error to the log file.
* @attribute error (String)	Error Message to store
* @attribute this_level (String) Log Level to store this message as
* @prints Error message if logging.conf.php is missing
* @returns nothing
*/
 function add($error,$this_level) {
    global $log_level, $gotConf, $log_show_errors;
		
    $log = new logging;
	
	if ($gotConf == false) {
	   print "<p><strong>The logging.conf.php file is missing. Logging is disabled</strong></p>";	
	}	
	elseif ($this_level == "All" && $log_show_errors == true) { 
	  print "<p><strong>You should not add an event log with log_level 'All'</strong></p>";
	}
    elseif (in_array($this_level,$log_level) or in_array("All",$log_level)) { 		
       $log->write($error,$this_level);		  
    }
		 
 }

/**
* Write the error message to the log file.
* @attribute error (String)	Error Message to store
* @attribute this_level (String) Log Level to store this message as
* @prints Logging file write error (file or directory unwritable) (if $log_show_errors is true)
* @prints Log file full error (if $log_show_errors is true)
* @returns nothing
*/
 function write($error,$this_level) {
    global $log_show_errors, $log_date;
	
	$log = new logging;
	$log_file = $log->getTodaysLogFile();
	$log_path = $log->getRelativePath();
		
	$nextline = date($log_date)."	".$this_level."	".$error."
";
	$checkLogFile = $log->checkLogFile();
	if ($checkLogFile == "valid") {			
      $file = fopen($log_path.$log_file,"a");
      fwrite ($file, $nextline, strlen($nextline));
      fclose($file);
	}
	elseif ($log_show_errors == true && $checkLogFile == "unwritable") {
	   print "<p><strong>Logging Disabled. Log file (".$log_path.$log_file.") unwritable.</strong></p>";
	}
	elseif ($log_show_errors == true && $checkLogFile == "toobig") {
	   print "<p><strong>Todays log file (".$log_path.$log_file.") is full. Logging starts again at midnight.</strong></p>";
	}
		
 }

/**
* Check the log file is writable and not full.
* @returns valid The file is OK
* @returns unwritable The file or directory cannot be written to
* @returns toobig The log file size is bigger than $log_length in logging.conf.php.
*/
 function checkLogFile() {
    global $log_length;
	
	$log = new logging;
	$log_file = $log->getTodaysLogFile();
	$log_path = $log->getRelativePath();
	
	$logsize = 0;
	if (file_exists($log_path.$log_file)) {
	   $logsize = filesize($log_path.$log_file);
       $logsize = $logsize / 1024 / 1024; // in MB
	}
 
   if (file_exists($log_path.$log_file) && is_writable($log_path.$log_file)) {
     $return = "valid"; 
   }
   elseif (!file_exists($log_path.$log_file) && is_writable($log_path)) {
	 touch($log_path.$log_file);
	 chmod($log_path.$log_file,0755);
	 $return = "valid";
   }
   elseif(!file_exists($log_path.$log_file) && !is_writable($log_path)) {
     $return = "unwritable"; 
   }
   elseif (file_exists($log_path.$log_file) && !is_writable($log_path.$log_file)) {
     $return = "unwritable"; 
   }
   
   if ($log_length != false && $logsize > $log_length) {
     $return = "toobig";
   }
   
   return $return;

 }
 
/**
* Get the filename of todays log file
* @returns log_file Todays log file. Requires getRelativePath()
*/
 function getTodaysLogFile() {
    global $log_file;
    $log_file = sprintf($log_file,date("Y-m-d"));
	return $log_file; 
 }
 
/**
* Get the absolute path to the log files.
* @returns path absolute path to the log file directory.
*/
 function getRelativePath() {
   global $log_path;
   $path =  $_SERVER['DOCUMENT_ROOT'];
   
   if (strpos($path,"/",(strlen($path)) - 1) == false) {
        $path = $path."/".$log_path;
   }
   else {
        $path = $path.$log_path;
   }   

   return $path;   
 }
 
 
}

?>