<?php
session_start();
	if (isset($_SESSION['user'])) 
	{
		  //echo "Anything?";
		  // unset session variable
		  unset($_SESSION['user']);
		  // invalidate the session cookie
		  /*if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-86400, '/');
			}*/
		  //ob_end_flush();
		  // end session
		  session_destroy();
		  
	}
	header("Location:login.php");
?>