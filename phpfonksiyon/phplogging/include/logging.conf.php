<?php
/**
* Author: Richard Brierton
* WWW: www.allhype.co.uk
* Contact: www.allhype.co.uk/contact/
*
* Date Created: July 21st 2004. 12:36
* Last Updated: July 21st 2004. 18:59
* Version: 1.0
* logging.inc.php Logging Configuration
* Relied on by logging.conf.php
*/

$log_file = "logging.%s.log"; // %s is where todays date will go. %s MUST BE IN THE NAME
$log_path = "logging/logs/"; // Path from root of application
$log_length = 1; // value in MB. Decimal is OK. or set to false for no limit
$log_level = array("Dev","Warn","Db","Example","Login","Failed Login"); // User defined. To get everything use: $log_level = array("All");
$log_show_errors = true; // Show php logging errors on screen (Use while developing only)
$log_date = "Y-m-d H:i:s"; // 2004-07-21 18:41:50

?>