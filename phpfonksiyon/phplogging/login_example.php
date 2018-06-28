<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Login Example</title>
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
// get the required class file
require("include/logging.inc.php");

	// Create a new logging class
	$log = new logging;
	
  if (isset($_POST["login"])) {
      if ($_POST["username"] == "root" && $_POST["password"] == "hype") {
	    // Successful login
		$log->add("Someone logged in as '".$_POST["username"]."' with password '".$_POST["password"]."' from IP '".$_SERVER["REMOTE_ADDR"]."'","Login");	  
		print '<p>Congratulations. You managed to log in correctly. You have been logged as successful (<a href="/'.$log_path.$log->getTodaysLogFile().'">/'.$log_path.$log->getTodaysLogFile().'</a>).</p>';
	  }
	  else {
	    // Failed login
		$log->add("Someone tried to log in as '".$_POST["username"]."' with password '".$_POST["password"]."' from IP '".$_SERVER["REMOTE_ADDR"]."'","Failed Login");	  
		print '<p>Your username and password were incorrect. This attempted access has been logged (<a href="/'.$log_path.$log->getTodaysLogFile().'">/'.$log_path.$log->getTodaysLogFile().'</a>).</p>';	  
	  }

  }
  
  else {
  
    print '<h1>Log In Example</h1>';
	
	print '<p>Username: <strong>root</strong> Password: <strong>hype</strong>. Correct and incorrect log ins are logged. Try
	doing both and then checking the log files.</p>';
	
    print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">
	  <label for="username">Username:</label> <input type="text" name="username" /><br />
	  <label for="password">Password:</label> <input type="password" name="password" /><br />
	  <input type="submit" name="login" value="Log In" />	
	</form>';
 
 
  }
		
?>
<div align="center">PHP Logging by <a href="http://www.allhype.co.uk/">www.allhype.co.uk</a><br />
Copyright &copy; 2004 All Hype Design</div>
</body>
</html>
