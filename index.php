<?php
session_start();
	if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin' || $_SESSION['user'] == 'editor') 
	{
		header("Location:admin.php");
	}
	else
	{
		header("Location:login.php");
	}

?>