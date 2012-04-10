<?php
session_start();
$cookie = $_COOKIE['user'];	

if (isset($_SESSION['user'])) 
		{
		  header("Location:admin.php");
		}
		else 
		{
			
		
	
			$string = '<!DOCTYPE html>
			<html lang="en">
			<head>
				<meta charset="utf-8" />
				<title>Login</title>
				<link type="text/css" rel="stylesheet" href="" />
			</head>
			<body>
			<form action="admin.php" method="post"> User name:  <input type="text" name="user"  size="10"  value='.$cookie.'  /> Password:  <input type="password" name="password"  size="20"  value=""  /> <input type="submit" name="login" value="Login" />
			</form>
			</body>

			</html>';
		}
	echo $string;



?>