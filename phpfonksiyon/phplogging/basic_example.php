<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Basic Logging Example</title>
<style type="text/css">
  <!--
    body { 
	font-family: Arial, Verdana, sans-serif;
	}
  -->
</style>
</head>
<body>
<?php

/*
 You can test the logging by running this page with your browser when it's loaded onto your server.
*/

// Beginning of logging example code

    // get the required class file
    require("include/logging.inc.php");

	// Create a new logging class
	$log = new logging;
	
	// Add the item to the log
	$log->add("An error from the basic_example.php page","Example");
// End of logging example code
	
	
	print '<p>If you check your log files (<a href="/'.$log_path.$log->getTodaysLogFile().'">/'.$log_path.$log->getTodaysLogFile().'</a>) you will see an error</p>';
		
?>
<div align="center">PHP Logging by <a href="http://www.allhype.co.uk/">www.allhype.co.uk</a><br />
Copyright &copy; 2004 All Hype Design</div>
</body>
</html>
